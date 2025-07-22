<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentReview extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'case_file_id',
        'document_path',
        'original_filename',
        'deadline',
        'priority',
        'status',
        'assigned_to',
        'review_comments',
        'reviewed_at',
        'reviewed_by',
    ];

    protected $casts = [
        'deadline' => 'date',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the case file that owns the document review.
     */
    public function caseFile(): BelongsTo
    {
        return $this->belongsTo(CaseFile::class);
    }

    /**
     * Get all versions for the document review.
     */
    public function versions(): MorphMany
    {
        return $this->morphMany(DocumentVersion::class, 'documentable');
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    /**
     * Get the stakeholders for the document review.
     */
    public function stakeholders(): MorphMany
    {
        return $this->morphMany(AdvisoryStakeholder::class, 'stakeholderable');
    }

    /**
     * Get the user who is assigned to review this document.
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the user who reviewed this document.
     */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scope a query to only include document reviews assigned to a specific user.
     */
    public function scopeAssignedToUser($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Scope a query to only include document reviews with a specific status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include document reviews that are past their deadline.
     */
    public function scopePastDeadline($query)
    {
        return $query->where('deadline', '<', now())
            ->whereIn('status', ['under_review', 'needs_revision']);
    }
}






