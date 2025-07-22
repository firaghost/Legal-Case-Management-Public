<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DocumentVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'documentable_id',
        'documentable_type',
        'version',
        'file_path',
        'notes',
        'uploaded_by',
    ];

    protected $casts = [
        'version' => 'integer',
    ];

    /**
     * Get the parent documentable model (DocumentReview, LegalAdvisoryCase, etc.).
     */
    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who uploaded this version.
     */
    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the document URL for this version.
     */
    public function getDocumentUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::disk('documents')->url($this->file_path) : null;
    }

    /**
     * Get the file name from the file path.
     */
    public function getFileNameAttribute(): string
    {
        return basename($this->file_path);
    }
}






