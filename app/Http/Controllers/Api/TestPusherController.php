<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PusherService;
use Illuminate\Http\Request;

class TestPusherController extends Controller
{
    protected PusherService $pusherService;

    public function __construct(PusherService $pusherService)
    {
        $this->pusherService = $pusherService;
    }

    /**
     * Send a test message to a channel
     */
    public function sendTestMessage(Request $request)
    {
        $request->validate([
            'channel' => 'required|string',
            'event' => 'required|string',
            'message' => 'required|string',
        ]);

        $result = $this->pusherService->trigger(
            $request->channel,
            $request->event,
            ['message' => $request->message, 'time' => now()->toDateTimeString()]
        );

        return response()->json([
            'success' => (bool)$result,
            'message' => $result ? 'Message sent successfully' : 'Failed to send message',
            'data' => $result
        ]);
    }

    /**
     * Send a test message to a conversation
     */
    public function sendTestConversationMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|integer|exists:conversations,id',
            'message' => 'required|string',
        ]);

        $message = [
            'id' => time(),
            'content' => $request->message,
            'user_id' => auth()->id(),
            'created_at' => now()->toDateTimeString(),
            'is_test' => true,
        ];

        $result = $this->pusherService->triggerNewMessage(
            $request->conversation_id,
            $message
        );

        return response()->json([
            'success' => (bool)$result,
            'message' => $result ? 'Test message sent to conversation' : 'Failed to send test message',
            'data' => $message
        ]);
    }
}






