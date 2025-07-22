<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ConversationResource;

class ChatController extends Controller
{
    /**
     * Display the chat interface.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user's conversations and other necessary data
        $initialData = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->profile_photo_url ?? '',
            ],
            'conversations' => \App\Http\Resources\ConversationResource::collection(
            $user->conversations()->with(['participants.user','latestMessage'])->get()
        ),
        'pusher' => [
                'key' => config('broadcasting.connections.pusher.key'),
                'cluster' => config('broadcasting.connections.pusher.options.cluster'),
            ],
        ];

        return Inertia::render('Chat/Index', [
            'initialData' => $initialData,
        ]);
    }
}






