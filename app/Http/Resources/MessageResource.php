<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MessageResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'message';

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $isSender = $user && $user->id === $this->user_id;
        $hasAttachment = !empty($this->attachment);
        
        // Get the status for the current user
        $userStatus = null;
        if ($this->relationLoaded('statuses')) {
            $userStatus = $this->statuses->firstWhere('user_id', $request->user()->id);
        }

        // Get the attachment URL if it exists
        $attachmentUrl = null;
        $attachmentType = null;
        $attachmentName = null;
        $attachmentSize = null;
        $attachmentMimeType = null;
        
        if ($hasAttachment) {
            try {
                $attachmentUrl = Storage::disk('public')->url($this->attachment);
                $attachmentPath = Storage::disk('public')->path($this->attachment);
                
                if (file_exists($attachmentPath)) {
                    $attachmentName = basename($this->attachment);
                    $attachmentSize = filesize($attachmentPath);
                    $attachmentMimeType = mime_content_type($attachmentPath);
                    
                    // Determine attachment type based on mime type
                    if (Str::startsWith($attachmentMimeType, 'image/')) {
                        $attachmentType = 'image';
                    } elseif (Str::startsWith($attachmentMimeType, 'audio/')) {
                        $attachmentType = 'audio';
                    } elseif (Str::startsWith($attachmentMimeType, 'video/')) {
                        $attachmentType = 'video';
                    } else {
                        $attachmentType = 'document';
                    }
                }
            } catch (\Exception $e) {
                // If there's an error getting the file, just continue without attachment details
                $hasAttachment = false;
            }
        }

        // Get the reply message if this is a reply
        $replyTo = null;
        if ($this->reply_to && $this->relationLoaded('replyTo')) {
            $replyTo = [
                'id' => $this->replyTo->id,
                'body' => Str::limit($this->replyTo->body, 100),
                'type' => $this->replyTo->type,
                'user' => [
                    'id' => $this->replyTo->user->id,
                    'name' => $this->replyTo->user->name,
                ],
                'created_at' => $this->replyTo->created_at,
            ];
            
            if ($this->replyTo->type !== 'text' && $this->replyTo->attachment) {
                $replyTo['attachment_type'] = $this->replyTo->type;
                $replyTo['body'] = ucfirst($this->replyTo->type) . ': ' . ($replyTo['body'] ?: 'Attachment');
            }
        }

        // Format the response
        $data = [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'conversation_id' => $this->conversation_id,
            'reply_to' => $replyTo,
            'body' => $this->when($this->type === 'text', $this->body),
            'type' => $this->type,
            'content' => $this->when($this->type === 'text', $this->body),
            'is_sender' => $isSender,
            'is_edited' => $this->created_at->ne($this->updated_at),
            'is_forwarded' => (bool) $this->forwarded_from,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'meta' => [
                'is_delivered' => $userStatus ? (bool) $userStatus->is_delivered : false,
                'is_seen' => $userStatus ? (bool) $userStatus->is_seen : false,
                'delivered_at' => $userStatus ? $userStatus->delivered_at : null,
                'seen_at' => $userStatus ? $userStatus->seen_at : null,
                'deleted_at' => $this->deleted_at,
            ],
            'relationships' => [
                'user' => new UserResource($this->whenLoaded('user')),
                'statuses' => MessageStatusResource::collection($this->whenLoaded('statuses')),
                'forwarded_from' => $this->when(
                    $this->forwarded_from && $this->relationLoaded('forwardedFrom'),
                    function () {
                        return new UserResource($this->forwardedFrom);
                    }
                ),
                'reply_to' => $replyTo,
            ],
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'avatar' => $this->user->avatar,
            ],
            'attachments' => $hasAttachment ? [[
                'url' => $attachmentUrl,
                'type' => $attachmentType,
                'name' => $attachmentName,
                'size' => $attachmentSize,
                'mime_type' => $attachmentMimeType,
                'duration' => $this->duration ?? null,
            ]] : [],
            'links' => [
                'self' => url("/api/messages/{$this->id}"),
                'conversation' => url("/api/conversations/{$this->conversation_id}"),
            ],
        ];

        // Add attachment data if the message has an attachment
        if ($hasAttachment) {
            $data['attachment'] = [
                'url' => $attachmentUrl,
                'name' => $attachmentName,
                'type' => $attachmentType,
                'mime_type' => $attachmentMimeType,
                'size' => $attachmentSize,
                'size_formatted' => $this->formatBytes($attachmentSize),
            ];
            
            // Add audio_url for convenience in frontend
            if ($attachmentType === 'audio') {
                $data['audio_url'] = $attachmentUrl;
            }
            
            // For audio messages, add duration if available in metadata
            if ($attachmentType === 'audio' && $this->metadata) {
                $metadata = is_string($this->metadata) ? json_decode($this->metadata, true) : $this->metadata;
                if (isset($metadata['duration'])) {
                    $data['attachment']['duration'] = $metadata['duration'];
                    $data['attachment']['duration_formatted'] = $this->formatDuration($metadata['duration']);
                }
            }
            
            // For images/videos, add dimensions if available
            if (in_array($attachmentType, ['image', 'video']) && $this->metadata) {
                $metadata = is_string($this->metadata) ? json_decode($this->metadata, true) : $this->metadata;
                if (isset($metadata['width']) && isset($metadata['height'])) {
                    $data['attachment']['width'] = $metadata['width'];
                    $data['attachment']['height'] = $metadata['height'];
                    $data['attachment']['aspect_ratio'] = $metadata['width'] / max(1, $metadata['height']);
                }
            }
        }

        return $data;
    }
    
    /**
     * Format bytes to human-readable format
     */
    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
    
    /**
     * Format duration in seconds to HH:MM:SS or MM:SS format
     */
    protected function formatDuration($seconds)
    {
        $seconds = floor($seconds);
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
        
        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        }
        
        return sprintf('%d:%02d', $minutes, $seconds);
    }
}






