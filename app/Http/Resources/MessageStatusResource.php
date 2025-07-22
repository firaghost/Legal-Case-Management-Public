<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class MessageStatusResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'status';

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $now = now();
        
        // Calculate time differences for relative time display
        $deliveredAgo = $this->delivered_at ? $this->delivered_at->diffForHumans() : null;
        $seenAgo = $this->seen_at ? $this->seen_at->diffForHumans() : null;
        
        // Format status for display
        $status = 'pending';
        $lastStatusAt = null;
        $lastStatusAgo = null;
        
        if ($this->is_seen && $this->seen_at) {
            $status = 'seen';
            $lastStatusAt = $this->seen_at;
            $lastStatusAgo = $seenAgo;
        } elseif ($this->is_delivered && $this->delivered_at) {
            $status = 'delivered';
            $lastStatusAt = $this->delivered_at;
            $lastStatusAgo = $deliveredAgo;
        } elseif ($this->created_at) {
            $status = 'sent';
            $lastStatusAt = $this->created_at;
            $lastStatusAgo = $this->created_at->diffForHumans();
        }
        
        // Determine if the status is recent (within the last 5 minutes)
        $isRecent = $lastStatusAt && $lastStatusAt->diffInMinutes($now) <= 5;
        
        return [
            'id' => $this->id,
            'message_id' => $this->message_id,
            'user_id' => $this->user_id,
            'status' => $status,
            'is_delivered' => (bool) $this->is_delivered,
            'is_seen' => (bool) $this->is_seen,
            'delivered_at' => $this->delivered_at,
            'seen_at' => $this->seen_at,
            'meta' => [
                'is_recent' => $isRecent,
                'status_display' => ucfirst($status),
                'last_status_at' => $lastStatusAt,
                'last_status_ago' => $lastStatusAgo,
                'delivered_ago' => $deliveredAgo,
                'seen_ago' => $seenAgo,
            ],
            'relationships' => [
                'user' => new UserResource($this->whenLoaded('user')),
                'message' => new MessageResource($this->whenLoaded('message')),
            ],
            'links' => [
                'self' => url("/api/messages/{$this->message_id}/status/{$this->id}"),
                'message' => url("/api/messages/{$this->message_id}"),
                'user' => $this->user_id ? url("/api/users/{$this->user_id}") : null,
            ],
        ];
    }
}






