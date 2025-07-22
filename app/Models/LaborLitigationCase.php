<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class LaborLitigationCase extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'case_file_id',
        'claim_type',
        'claim_amount',
        'claimed_amount',
        'claim_material_desc',
        'recovered_amount',
        'outstanding_amount',
        'loan_amount',
        'early_settled',
        'execution_opened_at',
        'closed_at',
        'closed_by',
        'court_file_number',
    ];

    protected $casts = [
        'claim_amount'        => 'decimal:2',
        'claimed_amount'      => 'decimal:2',
        'recovered_amount'    => 'decimal:2',
        'outstanding_amount'  => 'decimal:2',
        'loan_amount'         => 'decimal:2',
        'execution_opened_at' => 'date',
        'closed_at' => 'date',
        'early_settled' => 'boolean',
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






