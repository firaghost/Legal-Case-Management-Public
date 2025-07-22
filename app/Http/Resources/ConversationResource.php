<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class ConversationResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'conversation';

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $isGroup = $this->type === 'group';
        
        // Always load participants with their associated user to avoid missing names
        $participants = $this->participants->loadMissing('user');

        // Get the other participants (excluding the current user)
        $otherParticipants = $participants->filter(function ($participant) use ($user) {
            return $participant->user_id !== $user->id;
        })->values();

        // For 1:1 chats, get the other participant
        $otherParticipant = null;
        if (!$isGroup && $otherParticipants->isNotEmpty()) {
            $otherParticipant = $otherParticipants->first()->user;
        }

        // Get unread count for the current user
        $unreadCount = 0;
        $currentUserParticipant = $this->participants->firstWhere('user_id', $user->id);
        
        if ($currentUserParticipant) {
            $unreadCount = $currentUserParticipant->unread_count ?? 0;
        }

        // Format the response
        return [
            'id' => $this->id,
            'title' => $this->when(
                $isGroup,
                $this->title,
                $otherParticipant ? $otherParticipant->name : 'Unknown User'
            ),
            'type' => $this->type,
            'is_group' => $isGroup,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'last_message_at' => $this->last_message_at,
            'unread_count' => $unreadCount,
            'is_muted' => $currentUserParticipant ? ($currentUserParticipant->muted_until ? true : false) : false,
            'is_archived' => $currentUserParticipant ? ($currentUserParticipant->is_archived ?? false) : false,
            'left_at' => $currentUserParticipant ? $currentUserParticipant->left_at : null,
            'meta' => [
                'participants_count' => $this->whenCounted('participants'),
                'messages_count' => $this->whenCounted('messages'),
            ],
            'relationships' => [
                'participants' => UserResource::collection($participants->map->user->filter()),
                'other_participant' => $otherParticipant ? new UserResource($otherParticipant) : null,
                'latest_message' => new MessageResource($this->whenLoaded('latestMessage')),
                'creator' => new UserResource($this->whenLoaded('creator')),
            ],
            'settings' => [
                'can_send_messages' => $currentUserParticipant ? ($currentUserParticipant->settings['can_send_messages'] ?? true) : true,
                'can_add_participants' => $currentUserParticipant ? ($currentUserParticipant->settings['can_add_participants'] ?? true) : true,
                'can_change_info' => $currentUserParticipant ? ($currentUserParticipant->settings['can_change_info'] ?? true) : true,
                'can_pin_messages' => $currentUserParticipant ? ($currentUserParticipant->settings['can_pin_messages'] ?? true) : true,
            ],
            'links' => [
                'self' => url("/api/conversations/{$this->id}"),
                'messages' => url("/api/conversations/{$this->id}/messages"),
                'participants' => url("/api/conversations/{$this->id}/participants"),
                'mark_as_read' => url("/api/conversations/{$this->id}/mark-as-seen"),
            ],
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'version' => '1.0',
                'api_version' => 'v1',
            ],
        ];
    }
}






