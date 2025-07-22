<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CaseType extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    public function caseFiles(): HasMany
    {
        return $this->hasMany(\App\Models\CaseFile::class);
    }
}






