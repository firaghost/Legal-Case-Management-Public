<?php

namespace App\Broadcasting;

use App\Models\Conversation;
use App\Models\User;

class ConversationChannel
{
    /**
     * Authenticate the user's access to the channel.
     *
     * @param  \App\Models\User  $user
     * @param  int  $conversationId
     * @return array|bool
     */
    public function join(User $user, $conversationId)
    {
        // Check if the user is a participant in the conversation
        $isParticipant = $user->conversations()
            ->where('conversations.id', $conversationId)
            ->exists();

        if ($isParticipant) {
            // Return user data that will be available in presence channels
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_online' => $user->is_online ?? false,
                'last_seen' => $user->last_seen?->toDateTimeString(),
            ];
        }

        return false;
    }
}






