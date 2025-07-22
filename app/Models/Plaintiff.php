<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Plaintiff extends Model
{
    protected $fillable = [
        'name',
        'case_file_id',
        'contact_number',
        'address',
        'email',
    ];

    public function caseFile(): BelongsTo
    {
        return $this->belongsTo(CaseFile::class);
    }
}






