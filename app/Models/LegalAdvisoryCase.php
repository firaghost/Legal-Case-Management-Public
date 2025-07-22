<?php

namespace App\Models;

use App\Traits\HasActionLogs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegalAdvisoryCase extends Model
{
    use HasFactory, HasActionLogs;

    protected $fillable = [
        'case_file_id',
        'advisory_type',
        'subject',
        'description',
        'assigned_lawyer_id',
        'request_date',
        'submission_date',
        'status',
        'outstanding_amount',
        'recovered_amount',
        'claimed_amount',
        'loan_amount',
        'document_path',
        'review_notes',
        'reviewed_document_path',
        'is_own_motion',
        'reference_number',
        'approved_by',
        'approved_at',
        'closure_notes',
        'closed_at',
        'closed_by',
        'court_file_number',
    ];

    protected $casts = [
        'outstanding_amount' => 'decimal:2',
        'recovered_amount'   => 'decimal:2',
        'claimed_amount'     => 'decimal:2',
        'loan_amount'        => 'decimal:2',
        'request_date' => 'date',
        'submission_date' => 'date',
        'approved_at' => 'datetime',
        'closed_at' => 'datetime',
        'is_own_motion' => 'boolean',
    ];

    /* Relationships */
    public function caseFile()
    {
        return $this->belongsTo(CaseFile::class);
    }

    public function assignedLawyer()
    {
        return $this->belongsTo(User::class, 'assigned_lawyer_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function documentVersions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class);
    }

    public function stakeholders()
    {
        return $this->morphMany(AdvisoryStakeholder::class, 'stakeholderable');
    }

    public function progressUpdates()
    {
        return $this->hasMany(ProgressUpdate::class, 'case_file_id', 'case_file_id');
    }

    /* Scopes */
    public function scopeWrittenAdvice($query)
    {
        return $query->where('advisory_type', 'written_advice');
    }

    public function scopeDocumentReview($query)
    {
        return $query->where('advisory_type', 'document_review');
    }

    public function scopeOwnMotion($query, $value = true)
    {
        return $query->where('is_own_motion', $value);
    }
}






