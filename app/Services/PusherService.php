<?php

namespace App\Services;

use Pusher\Pusher;
use Pusher\PusherException;

class PusherService
{
    protected Pusher $pusher;

    /**
     * @throws PusherException
     */
    public function __construct()
    {
        $this->pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            [
                'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                'useTLS' => true,
                'encrypted' => true,
            ]
        );
    }

    /**
     * Trigger a new event
     *
     * @param string $channel
     * @param string $event
     * @param array $data
     * @return array|bool
     */
    public function trigger(string $channel, string $event, array $data)
    {
        try {
            return $this->pusher->trigger($channel, $event, $data);
        } catch (\Exception $e) {
            \Log::error('Pusher trigger error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Trigger a new message event
     *
     * @param int $conversationId
     * @param array $message
     * @return array|bool
     */
    public function triggerNewMessage(int $conversationId, array $message)
    {
        return $this->trigger(
            'conversation.' . $conversationId,
            'new-message',
            ['message' => $message]
        );
    }

    /**
     * Trigger a typing indicator event
     *
     * @param int $conversationId
     * @param array $user
     * @return array|bool
     */
    public function triggerTyping(int $conversationId, array $user)
    {
        return $this->trigger(
            'conversation.' . $conversationId,
            'typing',
            ['user' => $user]
        );
    }

    /**
     * Trigger a message read event
     *
     * @param int $conversationId
     * @param int $userId
     * @return array|bool
     */
    public function triggerMessageRead(int $conversationId, int $userId)
    {
        return $this->trigger(
            'conversation.' . $conversationId,
            'message-read',
            ['user_id' => $userId]
        );
    }
}






