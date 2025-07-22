<?php

use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\TestPusherController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for Chat
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for the chat functionality.
| These routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Apply auth middleware to all chat routes
Route::middleware(['auth:sanctum', 'web'])->group(function () {
    // Conversations
    Route::get('/conversations', [\App\Http\Controllers\Api\ChatController::class, 'index']);
    Route::post('/conversations', [\App\Http\Controllers\Api\ChatController::class, 'createOrGetConversation']);
    Route::get('/conversations/{conversation}', [\App\Http\Controllers\Api\ChatController::class, 'show'])
        ->name('api.conversations.show');
    Route::put('/conversations/{conversation}', [\App\Http\Controllers\Api\ChatController::class, 'updateConversation']);
    Route::delete('/conversations/{conversation}', [\App\Http\Controllers\Api\ChatController::class, 'deleteConversation']);
    Route::post('/conversations/{conversation}/leave', [\App\Http\Controllers\Api\ChatController::class, 'leaveConversation']);
    
    // Messages
    Route::get('/conversations/{conversation}/messages', [\App\Http\Controllers\Api\ChatController::class, 'getMessages']);
    Route::post('/conversations/{conversation}/messages', [\App\Http\Controllers\Api\ChatController::class, 'sendMessage']);
    
    // Test Pusher Endpoints (for development only)
    if (app()->environment('local')) {
        Route::post('/test/pusher/message', [TestPusherController::class, 'sendTestMessage']);
        Route::post('/test/pusher/conversation-message', [TestPusherController::class, 'sendTestConversationMessage']);
    }
    Route::get('/messages/{message}', [\App\Http\Controllers\Api\ChatController::class, 'showMessage'])->name('api.messages.show');
    Route::post('/conversations/{conversation}/mark-as-seen', [\App\Http\Controllers\Api\ChatController::class, 'markAsSeen']);
    
    // Participants
    Route::get('/conversations/{conversation}/participants', [\App\Http\Controllers\Api\ChatController::class, 'getParticipants']);
    Route::post('/conversations/{conversation}/participants', [\App\Http\Controllers\Api\ChatController::class, 'addParticipants']);
    Route::delete('/conversations/{conversation}/participants', [\App\Http\Controllers\Api\ChatController::class, 'removeParticipants']);
    
    // Message Status
    Route::get('/messages/{message}/status', [\App\Http\Controllers\Api\ChatController::class, 'getMessageStatus']);
    Route::post('/messages/{message}/mark-delivered', [\App\Http\Controllers\Api\ChatController::class, 'markAsDelivered']);
    Route::post('/messages/{message}/mark-seen', [\App\Http\Controllers\Api\ChatController::class, 'markAsSeen']);
    
    // Search
    Route::get('/search/conversations', [\App\Http\Controllers\Api\ChatController::class, 'searchConversations']);
    Route::get('/search/messages', [\App\Http\Controllers\Api\ChatController::class, 'searchMessages']);
    
    // User presence
    Route::post('/user/online', [\App\Http\Controllers\Api\ChatController::class, 'setOnlineStatus']);
    Route::post('/user/typing', [\App\Http\Controllers\Api\ChatController::class, 'setTypingStatus']);
});






