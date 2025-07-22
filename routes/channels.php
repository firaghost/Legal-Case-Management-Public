<?php

use App\Models\User;
use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Private channel for user presence
Broadcast::channel('user.{userId}', function (User $user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Private channel for conversations
Broadcast::channel('conversation.{conversationId}', function (User $user, $conversationId) {
    return $user->conversations()->where('conversations.id', $conversationId)->exists();
});

// Presence channel for online users in a conversation
Broadcast::channel('presence-conversation.{conversationId}', function (User $user, $conversationId) {
    if ($user->conversations()->where('conversations.id', $conversationId)->exists()) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->profile_picture,
            'is_online' => $user->is_online,
            'last_seen' => $user->last_seen?->toDateTimeString(),
        ];
    }
    return false;
});

// Private channel for user notifications
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Presence channel for online users
Broadcast::channel('presence-users', function (User $user) {
    return [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'avatar' => $user->profile_picture,
    ];
});






