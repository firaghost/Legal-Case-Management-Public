<?php

namespace App\Providers;

use App\Broadcasting\ConversationChannel;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register broadcasting routes with proper middleware
        Broadcast::routes([
            'middleware' => ['web', 'auth:sanctum'],
            'prefix' => 'broadcasting',
            'as' => 'broadcasting.',
        ]);

        // Also register the auth route without prefix for Pusher compatibility
        Broadcast::routes([
            'middleware' => ['web', 'auth:sanctum'],
        ]);

        // Register channel authorizations with proper validation
        Broadcast::channel('App.Models.User.{userId}', function ($user, $userId) {
            return (int) $user->id === (int) $userId;
        });

        // Conversation channel authorization
        Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
            // Check if user is part of the conversation
            return $user->conversations()->where('conversations.id', $conversationId)->exists();
        });

        // Register custom channel classes
        Broadcast::channel('conversation.{conversationId}', ConversationChannel::class);

        // Presence channel for online users
        Broadcast::channel('online', function ($user) {
            if (auth()->check()) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->profile_photo_url,
                ];
            }
            return null;
        });
    }
}






