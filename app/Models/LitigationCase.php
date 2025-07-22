<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Traits\CalculatesOutstanding;

class LitigationCase extends Model
{
    use HasFactory, Auditable, CalculatesOutstanding;

    protected $fillable = [
        'case_file_id',
        'branch',
        'internal_file_no',
        'outstanding_amount',
        'claimed_amount',
        'recovered_amount',
        'execution_opened_at',
        'early_closed',
        'closed_at',
        'closed_by',
    ];

    protected $casts = [
        'execution_opened_at' => 'date',
        'claimed_amount'     => 'decimal:2',
        'closed_at' => 'date',
        'early_closed' => 'boolean',
    ];

    /* Relationships */
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






