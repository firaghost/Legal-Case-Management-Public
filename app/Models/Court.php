<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Court extends Model
{
    protected $fillable = [
        'name',
        'code',
        'address',
    ];

    public function caseFiles(): HasMany
    {
        return $this->hasMany(\App\Models\CaseFile::class);
    }
}






