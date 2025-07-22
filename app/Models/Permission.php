<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'group',
        'guard_name',
    ];

    /**
     * Scope a query to only include permissions of a given group.
     */
    public function scopeGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Helper method to find or create a permission with additional fields.
     */
    public static function findOrCreateWithDetails(string $name, string $description = null, string $group = 'general')
    {
        $permission = static::findByName($name);

        if (! $permission) {
            $permission = static::create([
                'name' => $name,
                'description' => $description,
                'group' => $group,
                'guard_name' => 'web', // Default guard
            ]);
        }

        return $permission;
    }
}






