<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvisoryStakeholder extends Model
{
    use HasFactory;

    protected $fillable = [
        'stakeholderable_id',
        'stakeholderable_type',
        'name',
        'email',
        'phone',
        'organization',
        'type',
        'notes',
    ];

    /* Relationships */
    public function stakeholderable()
    {
        return $this->morphTo();
    }

    /* Scopes */
    public function scopeRequesters($query)
    {
        return $query->where('type', 'requester');
    }

    public function scopeReviewers($query)
    {
        return $query->where('type', 'reviewer');
    }

    public function scopeApprovers($query)
    {
        return $query->where('type', 'approver');
    }

    public function scopeRecipients($query)
    {
        return $query->where('type', 'recipient');
    }
}






