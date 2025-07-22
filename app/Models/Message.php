<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Conversation;
use App\Models\User;
use App\Models\MessageStatus;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'body',
        'type',
        'attachment_path',
        'attachment_url',
        'attachment_name',
        'attachment_type',
        'attachment_size',
        'reply_to',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'attachment_size' => 'integer',
    ];

    protected $appends = ['is_audio', 'is_image', 'is_document'];

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($message) {
            if (empty($message->type)) {
                $message->type = $message->attachment_path ? 'document' : 'text';
            }
        });

        static::created(function ($message) {
            $message->conversation->update([
                'last_message_at' => $message->created_at
            ]);
        });

        static::deleting(function ($message) {
            if ($message->isForceDeleting() && $message->attachment_path) {
                Storage::disk('public')->delete($message->attachment_path);
            }
        });
    }

    /**
     * Get the conversation that the message belongs to.
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the user that owns the message.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the statuses for the message.
     */
    public function statuses(): HasMany
    {
        return $this->hasMany(MessageStatus::class);
    }

    /**
     * The message this one replies to (parent).
     */
    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reply_to');
    }

    /**
     * Children replies referencing this message.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'reply_to');
    }

    /**
     * Scope a query to only include unread messages for a user.
     */
    public function scopeUnreadForUser($query, $userId)
    {
        return $query->whereDoesntHave('statuses', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    /**
     * Check if the message has an audio attachment.
     */
    public function getIsAudioAttribute(): bool
    {
        return $this->type === 'audio';
    }

    /**
     * Check if the message has an image attachment.
     */
    public function getIsImageAttribute(): bool
    {
        return $this->type === 'image';
    }

    /**
     * Check if the message has a document attachment.
     */
    public function getIsDocumentAttribute(): bool
    {
        return $this->type === 'document';
    }

    /**
     * Store an audio file for the message.
     */
    public function storeAudioFile($file): ?string
    {
        return $this->storeFile($file, 'audio');
    }

    /**
     * Store a document file for the message.
     */
    public function storeDocumentFile($file): ?string
    {
        return $this->storeFile($file, 'documents');
    }

    /**
     * Store a file for the message.
     */
    protected function storeFile($file, string $directory): ?string
    {
        if (!$file || !$file->isValid()) {
            return null;
        }

        $path = $file->store("messages/{$directory}", 'public');
        
        $this->update([
            'attachment_name' => $file->getClientOriginalName(),
            'attachment_path' => $path,
            'attachment_url' => Storage::disk('public')->url($path),
            'attachment_type' => $file->getMimeType(),
            'attachment_size' => $file->getSize(),
        ]);

        return $path;
    }

    /**
     * Mark the message as read for a user.
     */
    public function markAsRead(int $userId): void
    {
        $this->statuses()->updateOrCreate(
            ['user_id' => $userId],
            ['is_read' => true, 'read_at' => now()]
        );
    }

    /**
     * Check if the message is read by a user.
     */
    public function isReadBy(int $userId): bool
    {
        return $this->statuses()
            ->where('user_id', $userId)
            ->where('is_read', true)
            ->exists();
    }
}






