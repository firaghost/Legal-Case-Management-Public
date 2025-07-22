<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_default',
        'guard_name'
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    // All permission-related methods are inherited from Spatie\Permission\Models\Role
}






