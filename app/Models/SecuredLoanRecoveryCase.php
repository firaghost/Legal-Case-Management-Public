<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\CalculatesOutstanding;
use App\Traits\Auditable;
use App\Traits\HasActionLogs;

class SecuredLoanRecoveryCase extends Model
{
    use CalculatesOutstanding, HasFactory, Auditable, HasActionLogs;

    protected $fillable = [
        'branch_id',
        'work_unit_id',
        'customer_name',
        'company_file_number',
        'court_name',
        'lawyer_id',
        'court_info',
        'case_file_id',
        'loan_amount',
        'outstanding_amount',
        'claimed_amount',
        'foreclosure_notice_date',
        'collateral_description',
        'collateral_value',
        'foreclosure_warning',
        'first_auction_held',
        'second_auction_held',
        'recovered_amount',
        'closure_type',
        'closure_notes',
        'closed_at',
        'closed_by',
        'court_file_number',
        'collateral_estimation_path',
        'warning_doc_path',
        'auction_publication_path',
    ];

    protected $casts = [
        'first_auction_held' => 'boolean',
        'foreclosure_warning' => 'boolean',
        'second_auction_held' => 'boolean',
        'loan_amount' => 'decimal:2',
        'outstanding_amount' => 'decimal:2',
        'claimed_amount' => 'decimal:2',
        'collateral_value' => 'decimal:2',
        'recovered_amount' => 'decimal:2',
        'foreclosure_notice_date' => 'date',
        'closed_at' => 'date',
    ];

    public function caseFile()
    {
        return $this->belongsTo(CaseFile::class);
    }

        public function auctions()
    {
        return $this->hasMany(SecuredLoanAuction::class);
    }

    public function progressUpdates()
    {
        return $this->hasMany(ProgressUpdate::class, 'case_file_id', 'case_file_id');
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
}






