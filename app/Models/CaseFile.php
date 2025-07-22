<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Traits\CalculatesOutstanding;
use App\Traits\HasActionLogs;

class CaseFile extends Model
{
    use HasFactory, Auditable, CalculatesOutstanding, HasActionLogs;

    protected static function booted()
    {
        parent::booted();

        static::creating(function (self $case) {
            if (empty($case->file_number)) {
                $prefix = optional($case->caseType)->code ?? str_pad($case->case_type_id, 2, '0', STR_PAD_LEFT);
                $latestSeq = self::where('file_number', 'LIKE', $prefix.'-%')
                    ->orderByDesc('file_number')
                    ->value('file_number');
                $next = $latestSeq ? ((int)substr($latestSeq, 3)) + 1 : 1;
                $case->file_number = sprintf('%s-%04d', $prefix, $next);
            }
        });
    }

    protected $fillable = [
        'file_number',
        'title',
        'description',
        'case_type_id',
        'court_id',
        'court_name',
        'status',
        'opened_at',
        'closed_at',
        'lawyer_id',
        'created_by',
        'branch_id',
        'work_unit_id',
        'claimed_amount',
        'recovered_amount',
        'outstanding_amount',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Accessor to expose the case type code directly on the CaseFile model.
     */
    public function getCodeAttribute(): ?string
    {
        return optional($this->caseType)->code;
    }

    /**
     * Ensure the virtual "code" attribute appears in serialized models.
     *
     * @var array<int, string>
     */
    protected $appends = ['code'];

    /* Relationships */
    public function caseType()
    {
        return $this->belongsTo(CaseType::class);
    }

    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    public function lawyer()
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Case Type Relationships
    public function litigation()
    {
        return $this->hasOne(LitigationCase::class);
    }

    public function laborLitigation()
    {
        return $this->hasOne(LaborLitigationCase::class);
    }

    public function otherCivilLitigation()
    {
        return $this->hasOne(OtherCivilLitigationCase::class);
    }

    public function criminalLitigation()
    {
        return $this->hasOne(CriminalLitigationCase::class);
    }

    public function securedLoanRecovery()
    {
        return $this->hasOne(SecuredLoanRecoveryCase::class);
    }

    public function legalAdvisory()
    {
        return $this->hasOne(LegalAdvisoryCase::class);
    }

    public function plaintiffs()
    {
        return $this->hasMany(Plaintiff::class);
    }

    public function defendants()
    {
        return $this->hasMany(Defendant::class);
    }

    public function appeals()
    {
        return $this->hasMany(Appeal::class);
    }

    public function progressUpdates()
    {
        return $this->hasMany(ProgressUpdate::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Accessor: get the next upcoming appointment date for this case.
     *
     * @return \Illuminate\Support\Carbon|null
     */
    public function getNextAppointmentAttribute()
    {
        // If the appointments relationship is already loaded (eager loaded with upcoming scope)
        // just use the first item. Otherwise, query using the upcoming scope to avoid loading
        // unnecessary past appointments.
        if ($this->relationLoaded('appointments')) {
            $appointment = $this->appointments->first();
        } else {
            $appointment = $this->appointments()->upcoming()->first();
        }

        return $appointment?->appointment_date;
    }

    public function workUnit()
    {
        return $this->belongsTo(WorkUnit::class);
    }

    public function cleanLoanRecovery()
    {
        return $this->hasOne(\App\Models\CleanLoanRecoveryCase::class);
    }

    public function evidences()
    {
        return $this->hasMany(Evidence::class, 'case_file_id');
    }
}





