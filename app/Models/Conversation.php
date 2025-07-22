<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Message;
use App\Models\User;
use App\Models\Participant;

class Conversation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'type',
        'created_by',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    /**
     * The participants in the conversation.
     */
    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }
    
    /**
     * The users that belong to the conversation.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'participants')
            ->using(Participant::class)
            ->withPivot(['role', 'last_read', 'muted_until', 'settings'])
            ->withTimestamps();
    }

    /**
     * Get all messages for the conversation.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the user who created the conversation.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the most recent message in the conversation.
     */
    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /**
     * Add a participant to the conversation.
     */
    public function addParticipant(User $user, string $role = 'member')
    {
        return $this->participants()->attach($user->id, ['role' => $role]);
    }

    /**
     * Remove a participant from the conversation.
     */
    public function removeParticipant(User $user)
    {
        return $this->participants()->detach($user->id);
    }

    /**
     * Check if a user is a participant in the conversation.
     */
    public function hasParticipant(User $user): bool
    {
        return $this->participants()->where('user_id', $user->id)->exists();
    }

    /**
     * Mark all messages as read for a specific user.
     */
    public function markAsReadForUser(User $user)
    {
        $this->messages()
            ->where('user_id', '!=', $user->id)
            ->whereDoesntHave('statuses', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->each(function ($message) use ($user) {
                $message->statuses()->create([
                    'user_id' => $user->id,
                    'is_read' => true,
                    'read_at' => now(),
                ]);
            });
    }
}






