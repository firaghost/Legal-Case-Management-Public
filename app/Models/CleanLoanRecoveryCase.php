<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CalculatesOutstanding;

class CleanLoanRecoveryCase extends Model
{
    use CalculatesOutstanding;

    // Re-use the secured_loan_recovery_cases table so we don't duplicate schema
    protected $table = 'clean_loan_recovery_cases';

    protected $fillable = [
        'case_file_id',
        'branch_id',
        'work_unit_id',
        'company_file_number',
        'customer_name',
        'outstanding_amount',
        'claimed_amount',
        'recovered_amount',
        'loan_amount',
        'court_name',
        'lawyer_id',
        'court_info',
        'court_file_number',
    ];

    protected $casts = [
        'outstanding_amount' => 'decimal:2',
        'claimed_amount'     => 'decimal:2',
        'recovered_amount'   => 'decimal:2',
        'loan_amount'        => 'decimal:2',
    ];

    public function caseFile()
    {
        return $this->belongsTo(CaseFile::class);
    }
}






