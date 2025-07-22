<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Appeal extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'case_file_id',
        'level',
        'file_number',
        'notes',
        'decided_at',
    ];

    protected $casts = [
        'decided_at' => 'date',
    ];

    public function caseFile()
    {
        return $this->belongsTo(CaseFile::class);
    }
}






