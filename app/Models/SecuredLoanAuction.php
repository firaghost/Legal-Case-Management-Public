<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecuredLoanAuction extends Model
{
    use HasFactory;

    protected $fillable = [
        'secured_loan_recovery_case_id',
        'round',
        'auction_date',
        'result', // sold, ORGANIZATION_acquired, failed
        'sold_amount',
        'notes',
    ];

    protected $casts = [
        'auction_date' => 'date',
        'sold_amount' => 'decimal:2',
    ];

    public function case()
    {
        return $this->belongsTo(SecuredLoanRecoveryCase::class, 'secured_loan_recovery_case_id');
    }
}






