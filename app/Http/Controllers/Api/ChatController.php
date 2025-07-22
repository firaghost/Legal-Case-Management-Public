<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Events\ConversationRead;
use App\Models\MessageStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    /**
     * Mark all messages in a conversation as seen by the authenticated user.
     */
  
    /**
     * Get all conversations for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $conversations = $user->conversations()
            ->with(['participants.user', 'latestMessage'])
            ->withCount(['messages as unread_messages_count' => function ($query) use ($user) {
                $query->whereDoesntHave('statuses', function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->where('is_seen', true);
                });
            }])
            ->orderByDesc('updated_at')
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'data' => ConversationResource::collection($conversations),
            'meta' => [
                'current_page' => $conversations->currentPage(),
                'last_page' => $conversations->lastPage(),
                'per_page' => $conversations->perPage(),
                'total' => $conversations->total(),
            ],
        ]);
    }

    /**
     * Create a new conversation or return existing one.
     */
    public function createOrGetConversation(Request $request): JsonResponse
    {
        Log::info('createOrGetConversation called with data:', $request->all());
        
        $validated = $request->validate([
            'participants' => 'required|array|min:1',
            'participants.*' => 'exists:users,id',
            'title' => 'nullable|string|max:255',
        ]);

        $user = $request->user();
        $participantIds = array_unique(array_merge($validated['participants'], [$user->id]));
        Log::info('Merged participant IDs:', $participantIds);

        // Check if a conversation already exists with exactly these participants
        $existingConversation = $this->findExistingConversation($participantIds);

        if ($existingConversation) {
            return response()->json([
                'message' => 'Conversation retrieved successfully',
                'data' => new ConversationResource($existingConversation->load(['participants.user', 'latestMessage'])),
            ]);
        }

        // Start a database transaction
        $conversation = DB::transaction(function () use ($user, $validated, $participantIds) {
            // Create the conversation
            $conversation = new Conversation([
                'title' => $validated['title'] ?? null,
                'type' => count($validated['participants']) > 1 ? 'group' : 'private',
                'created_by' => $user->id,
            ]);
            $conversation->save();
            
            // Add the current user as a participant with admin role
            $conversation->participants()->create([
                'user_id' => $user->id,
                'role' => 'admin'
            ]);
            
            // Add other participants
            $participants = array_diff($validated['participants'], [$user->id]);
            foreach ($participants as $participantId) {
                $conversation->participants()->create([
                    'user_id' => $participantId,
                    'role' => 'member'
                ]);
            }
            
            // Load the participants with their user relationship
            return $conversation->load(['participants.user']);
        });
        
        return response()->json([
            'message' => 'Conversation created successfully',
            'data' => new ConversationResource($conversation),
        ], 201);
    }

    /**
     * Get a specific conversation by ID.
     */
    public function show(Conversation $conversation): JsonResponse
    {
        // Eager load relationships
        $conversation->load([
            'participants.user',
            'messages' => function ($query) {
                return $query->latest()->limit(50);
            },
            'messages.user',
            'latestMessage'
        ]);

        // Check if the user is a participant in the conversation
        if (!$conversation->participants->contains('user_id', request()->user()->id)) {
            return response()->json(['message' => 'You are not authorized to view this conversation'], 403);
        }

        return new JsonResponse([
            'data' => new ConversationResource($conversation)
        ]);
    }

    /**
     * Get a specific message by ID.
     */
    public function showMessage(Message $message): JsonResponse
    {
        // Check if the user is a participant in the conversation
        $authUser = request()->user();
        if (!$authUser || !$authUser->conversations->contains($message->conversation_id)) {
            return response()->json(['message' => 'You are not authorized to view this message'], 403);
        }

        return new JsonResponse([
            'data' => new \App\Http\Resources\MessageResource($message->load(['user', 'statuses']))
        ]);
    }

    /**
     * Send a message to a conversation.
     */
    public function sendMessage(StoreMessageRequest $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();

        // Check if user is a participant in the conversation
        if (!$conversation->participants()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'You are not a participant in this conversation'], 403);
        }

        try {
            $message = DB::transaction(function () use ($request, $conversation, $user) {
                // Handle file upload if present
                $attachmentPath = null;
                if ($request->hasFile('attachments.0')) {
                    // For now, we'll just take the first attachment
                    $attachmentPath = $this->storeAttachment(
                        $request->file('attachments')[0], 
                        $conversation->id
                    );
                }
                
                // Create the message
                $message = $conversation->messages()->create([
                    'user_id' => $user->id,
                    'body' => $request->input('body'),
                    'type' => $request->input('type', 'text'),
                    'attachment' => $attachmentPath,
                    'reply_to' => $request->input('reply_to'),
                ]);

                // Update conversation's last message timestamp
                $conversation->update([
                    'last_message_at' => now(),
                ]);

                // Mark message as delivered to sender
                $message->statuses()->create([
                    'user_id' => $user->id,
                    'is_delivered' => true,
                    'delivered_at' => now(),
                ]);

                // Load relationships for the response
                $message->load(['user', 'statuses', 'conversation', 'replyTo.user']);

                // Broadcast the message to other participants
                broadcast(new MessageSent($message))->toOthers();

                return $message;
            });

            // Create the resource with the message
            $messageResource = new MessageResource($message);

            return response()->json([
                'message' => 'Message sent successfully',
                'data' => $messageResource,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error sending message: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'message' => 'Failed to send message',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get messages for a conversation.
     */
    public function getMessages(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();

        // Check if user is a participant in the conversation
        if (!$conversation->participants()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'You are not a participant in this conversation'], 403);
        }

        $messages = $conversation->messages()
            ->with(['user', 'statuses', 'replyTo.user'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 20));

        // Mark messages as seen
        $this->markMessagesAsSeen($conversation->id, $user->id);

        return response()->json([
            'data' => MessageResource::collection($messages),
            'meta' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
            ],
        ]);
    }

    /**
     * Add participants to a conversation.
     */
    public function addParticipants(Request $request, Conversation $conversation): JsonResponse
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ]);

        $user = $request->user();

        // Check if user has permission to add participants
        if (!$conversation->participants()->where('user_id', $user->id)->where('role', 'admin')->exists()) {
            return response()->json(['message' => 'You do not have permission to add participants'], 403);
        }

        $existingParticipants = $conversation->participants()->pluck('user_id')->toArray();
        $newParticipants = array_diff($request->user_ids, $existingParticipants);

        if (empty($newParticipants)) {
            return response()->json(['message' => 'All users are already participants in this conversation'], 422);
        }

        $participants = [];
        foreach ($newParticipants as $participantId) {
            $participants[$participantId] = [
                'role' => 'member',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $conversation->participants()->attach($participants);

        // Notify participants about the new members (you can create an event for this)
        // broadcast(new ParticipantsAdded($conversation, $newParticipants))->toOthers();

        return response()->json([
            'message' => 'Participants added successfully',
            'data' => new ConversationResource($conversation->load(['participants.user'])),
        ]);
    }

    /**
     * Remove participants from a conversation.
     */
    public function removeParticipants(Request $request, Conversation $conversation): JsonResponse
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ]);

        $user = $request->user();

        // Check if user has permission to remove participants
        if (!$conversation->participants()->where('user_id', $user->id)->where('role', 'admin')->exists()) {
            return response()->json(['message' => 'You do not have permission to remove participants'], 403);
        }

        // Prevent removing the last admin
        $adminCount = $conversation->participants()->where('role', 'admin')->count();
        $adminsToRemove = $conversation->participants()
            ->whereIn('user_id', $request->user_ids)
            ->where('role', 'admin')
            ->count();

        if ($adminCount - $adminsToRemove < 1) {
            return response()->json(['message' => 'A conversation must have at least one admin'], 422);
        }

        $conversation->participants()->detach($request->user_ids);

        // Notify remaining participants about the removal (you can create an event for this)
        // broadcast(new ParticipantsRemoved($conversation, $request->user_ids))->toOthers();

        return response()->json([
            'message' => 'Participants removed successfully',
        ]);
    }

    /**
     * Leave a conversation.
     */
    public function leaveConversation(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();
        $participant = $conversation->participants()->where('user_id', $user->id)->first();

        if (!$participant) {
            return response()->json(['message' => 'You are not a participant in this conversation'], 403);
        }

        // If user is the last admin, promote another user to admin
        if ($participant->role === 'admin') {
            $adminCount = $conversation->participants()->where('role', 'admin')->count();
            if ($adminCount === 1) {
                $newAdmin = $conversation->participants()
                    ->where('user_id', '!=', $user->id)
                    ->first();
                
                if ($newAdmin) {
                    $newAdmin->update(['role' => 'admin']);
                }
            }
        }

        $conversation->participants()->detach($user->id);

        // Notify remaining participants (you can create an event for this)
        // broadcast(new ParticipantLeft($conversation, $user))->toOthers();

        return response()->json([
            'message' => 'You have left the conversation',
        ]);
    }

    /**
     * Update conversation details.
     */
    public function updateConversation(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();

        // Check if user is an admin of the conversation
        if (!$conversation->participants()->where('user_id', $user->id)->where('role', 'admin')->exists()) {
            return response()->json(['message' => 'You do not have permission to update this conversation'], 403);
        }

        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $conversation->update($request->only(['title', 'description']));

        // Broadcast the update (you can create an event for this)
        // broadcast(new ConversationUpdated($conversation))->toOthers();

        return response()->json([
            'message' => 'Conversation updated successfully',
            'data' => new ConversationResource($conversation->load(['participants.user'])),
        ]);
    }

    /**
     * Delete a conversation.
     */
    public function deleteConversation(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();

        // Check if user is an admin of the conversation
        if (!$conversation->participants()->where('user_id', $user->id)->where('role', 'admin')->exists()) {
            return response()->json(['message' => 'You do not have permission to delete this conversation'], 403);
        }

        $conversation->delete();

        // Broadcast the deletion (you can create an event for this)
        // broadcast(new ConversationDeleted($conversation->id))->toOthers();

        return response()->json([
            'message' => 'Conversation deleted successfully',
        ]);
    }

    /**
     * Find an existing conversation with the exact same participants.
     */
    private function findExistingConversation(array $participantIds)
    {
        $count = count($participantIds);
        Log::info('Searching for existing conversation with participants:', $participantIds);
        Log::info('Total participants to match:', ['count' => $count]);
        
        // Get all conversations that have all the specified participants
        $conversationIds = DB::table('participants')
            ->whereIn('user_id', $participantIds)
            ->whereNull('deleted_at')
            ->groupBy('conversation_id')
            ->havingRaw('COUNT(DISTINCT user_id) = ?', [$count])
            ->pluck('conversation_id');
            
        if ($conversationIds->isEmpty()) {
            Log::info('No conversations found with all participants');
            return null;
        }
        
        // Now find conversations that have exactly these participants (no more, no less)
        $query = Conversation::whereIn('id', $conversationIds)
            ->where('type', 'private')
            ->whereHas('participants', function($q) use ($count) {
                $q->whereNull('deleted_at')
                  ->select('conversation_id')
                  ->groupBy('conversation_id')
                  ->havingRaw('COUNT(*) = ?', [$count]);
            });
            
        // Log the generated SQL for debugging
        Log::info('Conversation search query:', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);
        
        $conversation = $query->first();
        if ($conversation) {
            Log::info('Found conversation:', $conversation->toArray());
        } else {
            Log::info('No existing conversation found with exact participants');
            
            // Debug: Check each conversation to see why it's not matching
            foreach ($conversationIds as $cid) {
                $participants = DB::table('participants')
                    ->where('conversation_id', $cid)
                    ->whereNull('deleted_at')
                    ->pluck('user_id')
                    ->toArray();
                Log::info(sprintf(
                    'Conversation %d has participants: %s', 
                    $cid, 
                    implode(', ', $participants)
                ));
            }
        }
        
        return $conversation;
    }

    /**
     * Generate a conversation title based on participant names.
     */
    private function generateConversationTitle(array $participantIds, int $currentUserId): string
    {
        $otherUserIds = array_diff($participantIds, [$currentUserId]);
        $userNames = User::whereIn('id', $otherUserIds)
            ->limit(3)
            ->pluck('name')
            ->toArray();

        if (count($otherUserIds) > 3) {
            return implode(', ', $userNames) . ' and ' . (count($otherUserIds) - 3) . ' others';
        }

        return implode(', ', $userNames);
    }

    /**
     * Store an attachment and return its path.
     */
    private function storeAttachment($file, $conversationId): ?string
    {
        if (!$file->isValid()) {
            return null;
        }

        $path = $file->store("attachments/conversations/{$conversationId}", 'public');
        return $path ?: null;
    }

    /**
     * Mark all messages in a conversation as seen for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Conversation  $conversation
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsSeen(Request $request, Conversation $conversation)
    {
        $user = $request->user();

        // Check if user is a participant in the conversation
        if (!$conversation->participants()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'You are not a participant in this conversation'], 403);
        }

        // Mark all messages as seen for this user
        $this->markMessagesAsSeen($conversation->id, $user->id);

        event(new ConversationRead($conversation, $user->id));

        return response()->json([
            'message' => 'Messages marked as seen',
            'unread' => 0,
                'unread' => 0,
            'data' => [
                'conversation_id' => $conversation->id,
                'user_id' => $user->id,
                'marked_at' => now()->toDateTimeString(),
            ]
        ]);
    }

    /**
     * Mark all messages in a conversation as seen for a user.
     */
    private function markMessagesAsSeen(int $conversationId, int $userId): void
    {
        // Get all undelivered messages for this user in the conversation
        $messages = Message::where('conversation_id', $conversationId)
            ->where('user_id', '!=', $userId)
            ->whereDoesntHave('statuses', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->get();

        // Create status records for each message
        foreach ($messages as $message) {
            MessageStatus::updateOrCreate(
                ['message_id' => $message->id, 'user_id' => $userId],
                [
                    'is_delivered' => true,
                    'delivered_at'  => now(),
                    'is_seen'       => true,
                    'seen_at'       => now(),
                ]
            );
        }
    }
}






