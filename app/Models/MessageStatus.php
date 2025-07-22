<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Message;
use App\Models\User;

class MessageStatus extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'message_id',
        'user_id',
        'is_delivered',
        'delivered_at',
        'is_seen',
        'seen_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_delivered' => 'boolean',
        'delivered_at' => 'datetime',
        'is_seen' => 'boolean',
        'seen_at' => 'datetime',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'delivered_at',
        'seen_at',
    ];

    /**
     * Get the message that owns the status.
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    /**
     * Get the user that the status belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark the message as delivered.
     */
    public function markAsDelivered(): self
    {
        if (!$this->is_delivered) {
            $this->update([
                'is_delivered' => true,
                'delivered_at' => $this->delivered_at ?? now(),
            ]);
        }

        return $this;
    }

    /**
     * Mark the message as seen.
     */
    public function markAsSeen(): self
    {
        if (!$this->is_seen) {
            $this->update([
                'is_seen' => true,
                'seen_at' => $this->seen_at ?? now(),
            ]);
        }

        return $this;
    }

    /**
     * Check if the message has been delivered.
     */
    public function isDelivered(): bool
    {
        return (bool) $this->is_delivered;
    }

    /**
     * Check if the message has been seen.
     */
    public function isSeen(): bool
    {
        return (bool) $this->is_seen;
    }
}






