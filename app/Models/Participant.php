<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Conversation;

class Participant extends Pivot
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'participants';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'conversation_id',
        'user_id',
        'role',
        'last_read',
        'muted_until',
        'settings',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'last_read' => 'datetime',
        'muted_until' => 'datetime',
        'settings' => 'array',
    ];

    /**
     * Get the user that owns the participant.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the conversation that the participant belongs to.
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Check if the participant is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the participant is currently muted.
     */
    public function getIsMutedAttribute(): bool
    {
        return $this->muted_until && $this->muted_until->isFuture();
    }

    /**
     * Check if the participant is currently online.
     * This requires the presence of a last_seen column in the users table.
     */
    public function getIsOnlineAttribute(): bool
    {
        if (!$this->relationLoaded('user')) {
            $this->load('user');
        }

        return $this->user && $this->user->last_seen && 
               $this->user->last_seen->diffInMinutes(now()) < 5;
    }

    /**
     * Mute the participant for a given number of minutes.
     */
    public function mute(int $minutes = 60): void
    {
        $this->update([
            'muted_until' => now()->addMinutes($minutes),
        ]);
    }

    /**
     * Unmute the participant.
     */
    public function unmute(): void
    {
        $this->update([
            'muted_until' => null,
        ]);
    }

    /**
     * Mark the conversation as read for this participant.
     */
    public function markAsRead(): void
    {
        $this->update([
            'last_read' => now(),
        ]);
    }

    /**
     * Get the number of unread messages for this participant.
     */
    public function unreadCount(): int
    {
        return $this->conversation->messages()
            ->where('user_id', '!=', $this->user_id)
            ->where('created_at', '>', $this->last_read ?? $this->created_at)
            ->count();
    }
}






