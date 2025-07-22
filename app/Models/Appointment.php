<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasActionLogs;

class Appointment extends Model
{
    use HasFactory, HasActionLogs;

    protected $fillable = [
        'case_file_id',
        'appointment_date',
        'appointment_time',
        'title',
        'purpose',
        'location',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'appointment_date' => 'date',
    ];

    public function caseFile(): BelongsTo
    {
        return $this->belongsTo(CaseFile::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope a query to only include upcoming appointments.
     */
    public function scopeUpcoming($query)
    {
        return $query->whereDate('appointment_date', '>=', now())
                    ->orderBy('appointment_date');
    }
}






