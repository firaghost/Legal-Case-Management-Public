<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class UserResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'user';

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isCurrentUser = $request->user() && $request->user()->id === $this->id;
        $isAdmin = $request->user() && $request->user()->is_admin;
        $showEmail = $isCurrentUser || $isAdmin;
        $showPhone = $isCurrentUser || $isAdmin;
        
        // Get the participant data if this is in the context of a conversation
        $participantData = $this->whenPivotLoaded('participants', function () {
            return [
                'role' => $this->pivot->role,
                'joined_at' => $this->pivot->created_at,
                'is_muted' => (bool) $this->pivot->is_muted,
                'is_admin' => $this->pivot->role === 'admin',
                'can_send_messages' => (bool) $this->pivot->can_send_messages,
                'can_add_participants' => (bool) $this->pivot->can_add_participants,
                'can_change_info' => (bool) $this->pivot->can_change_info,
                'can_pin_messages' => (bool) $this->pivot->can_pin_messages,
            ];
        }, []);

        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'username' => $this->username,
            'email' => $this->when($showEmail, $this->email),
            'phone' => $this->when($showPhone, $this->phone),
            'avatar' => $this->avatar ? asset('storage/' . $this->avatar) : $this->defaultAvatar(),
            'cover_photo' => $this->cover_photo ? asset('storage/' . $this->cover_photo) : null,
            'bio' => $this->bio,
            'last_seen' => $this->last_seen,
            'is_online' => $this->is_online,
            'email_verified_at' => $this->email_verified_at,
            'phone_verified_at' => $this->phone_verified_at,
            'settings' => [
                'notifications' => [
                    'email' => $this->email_notifications,
                    'push' => $this->push_notifications,
                    'sms' => $this->sms_notifications,
                ],
                'privacy' => [
                    'show_email' => $this->privacy_show_email,
                    'show_phone' => $this->privacy_show_phone,
                    'show_last_seen' => $this->privacy_show_last_seen,
                ],
            ],
            'meta' => [
                'is_current_user' => $isCurrentUser,
                'is_admin' => $this->is_admin,
                'is_active' => $this->is_active,
                'is_verified' => (bool) $this->email_verified_at,
                'is_banned' => (bool) $this->banned_at,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            'links' => [
                'self' => url("/api/users/{$this->id}"),
                'avatar' => $this->avatar ? asset('storage/' . $this->avatar) : $this->defaultAvatar(),
                'profile' => url("/profile/{$this->username}"),
            ],
        ];

        // Add participant data if available
        if (!empty($participantData)) {
            $data['participant'] = $participantData;
        }

        return $data;
    }

    /**
     * Generate a default avatar URL based on the user's name.
     */
    protected function defaultAvatar(): string
    {
        $name = trim(collect(explode(' ', $this->name))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));

        $colors = [
            'bg-blue-500', 'bg-green-500', 'bg-purple-500', 
            'bg-pink-500', 'bg-red-500', 'bg-yellow-500', 'bg-indigo-500'
        ];
        
        $color = $colors[array_rand($colors)];
        
        return "https://ui-avatars.com/api/?name=" . urlencode($name) . "&color=fff&background=" . substr($color, 3, 1) . "&size=128";
    }
}






