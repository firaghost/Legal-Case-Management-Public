<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Traits\CalculatesOutstanding;

class CriminalLitigationCase extends Model
{
    use HasFactory, Auditable, CalculatesOutstanding;

    protected $fillable = [
        'case_file_id',
        'police_ref_no',
        'prosecutor_ref_no',
        'police_station',
        'police_file_number',
        'evidence_summary',
        'status',
        'outstanding_amount',
        'loan_amount',
        'claimed_amount',
        'recovered_amount',
        'closed_at',
        'closed_by',
        'court_file_number',
    ];

    protected $casts = [
        'outstanding_amount'  => 'decimal:2',
        'loan_amount'         => 'decimal:2',
        'claimed_amount'      => 'decimal:2',
        'recovered_amount'    => 'decimal:2',
        'closed_at' => 'date',
    ];

    public function caseFile()
    {
        return $this->belongsTo(CaseFile::class);
    }

    public function appeals()
    {
        return $this->hasMany(Appeal::class, 'case_file_id', 'case_file_id');
    }

    public function progressUpdates()
    {
        return $this->hasMany(ProgressUpdate::class, 'case_file_id', 'case_file_id');
    }
}






