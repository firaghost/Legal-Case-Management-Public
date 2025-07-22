<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;

    /**
     * Get total unread chat messages for the user.
     */
    public function unreadChatCount(): int
    {
        $userId = $this->id;

        return $this->conversations()
            ->withCount(['messages as unread_messages_count' => function ($query) use ($userId) {
                $query->where('user_id', '!=', $userId);
                $query->whereDoesntHave('statuses', function ($q) use ($userId) {
                    $q->where('user_id', $userId)
                      ->where('is_seen', true);
                });
            }])
            ->get()
            ->sum('unread_messages_count');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'password_change_required',
        'role',
        'profile_picture',
        'is_online',
        'is_active',
        'last_seen',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'password_change_required' => 'boolean',
        'is_active' => 'boolean',
        'is_online' => 'boolean',
        'last_login_at' => 'datetime',
        'last_seen' => 'datetime',
    ];

    /**
     * The roles that belong to the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')
            ->withTimestamps();
    }

    /**
     * The case files assigned to the user as a lawyer.
     */
    public function assignedCases(): HasMany
    {
        return $this->hasMany(CaseFile::class, 'lawyer_id');
    }

    /**
     * The action logs created by the user.
     */
    public function actionLogs(): HasMany
    {
        return $this->hasMany(ActionLog::class);
    }

    /**
     * The conversations that the user is a participant in.
     */
    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'participants')
            ->using(Participant::class)
            ->withPivot(['role', 'last_read', 'muted_until', 'settings'])
            ->withTimestamps();
    }

    /**
     * The messages sent by the user.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * The message statuses for the user.
     */
    public function messageStatuses(): HasMany
    {
        return $this->hasMany(MessageStatus::class);
    }

    /**
     * Get all progress updates created by the user.
     */
    public function progressUpdates(): HasMany
    {
        return $this->hasMany(ProgressUpdate::class, 'updated_by');
    }

    /**
     * Mark the user as online.
     */
    public function markAsOnline(): void
    {
        $this->update([
            'is_online' => true,
            'last_seen' => now(),
        ]);
    }

    /**
     * Mark the user as offline.
     */
    public function markAsOffline(): void
    {
        $this->update([
            'is_online' => false,
            'last_seen' => now(),
        ]);
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole($role): bool
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }

        if ($role instanceof Role) {
            return $this->roles->contains('id', $role->id);
        }

        return (bool) $role->intersect($this->roles)->count();
    }

    /**
     * Check if the user has any of the given roles.
     */
    public function hasAnyRole($roles): bool
    {
        if (is_string($roles)) {
            return $this->hasRole($roles);
        }

        if ($roles instanceof Collection) {
            $roles = $roles->all();
        }

        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the user has all of the given roles.
     */
    public function hasAllRoles($roles): bool
    {
        if (is_string($roles)) {
            return $this->hasRole($roles);
        }

        if ($roles instanceof Collection) {
            $roles = $roles->all();
        }

        foreach ($roles as $role) {
            if (!$this->hasRole($role)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the user has a specific permission.
     */
    public function hasPermissionTo($permission): bool
    {
        return $this->hasPermissionThroughRole($permission);
    }

    /**
     * Check if the user has a permission through their role.
     */
    protected function hasPermissionThroughRole($permission): bool
    {
        $permission = $this->getStoredPermission($permission);

        if (!$permission) {
            return false;
        }

        return $this->hasRole($permission->roles);
    }

    /**
     * Get the stored permission instance.
     */
    protected function getStoredPermission($permission)
    {
        if (is_string($permission)) {
            return Permission::where('name', $permission)->first();
        }

        if (is_int($permission)) {
            return Permission::find($permission);
        }

        return $permission;
    }

    /**
     * Assign a role to the user.
     */
    public function assignRole($role): self
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }

        $this->roles()->syncWithoutDetaching($role);

        return $this;
    }

    /**
     * Remove a role from the user.
     */
    public function removeRole($role): self
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }

        $this->roles()->detach($role);

        return $this;
    }

    /**
     * Get all permissions for the user.
     * 
     * @return \Illuminate\Support\Collection
     */
    public function getAllPermissions()
    {
        return $this->roles->flatMap(function ($role) {
            return $role->permissions;
        })->unique('name');
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if the user is a supervisor.
     */
    public function isSupervisor(): bool
    {
        return $this->hasRole('supervisor');
    }

    /**
     * Check if the user is a lawyer.
     */
    public function isLawyer(): bool
    {
        return $this->hasRole('lawyer');
    }

    /**
     * Record that the user has logged in.
     */
    public function recordLogin()
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);
    }

   
    /**
     * Get the user's permissions with caching.
     */
    public function getPermissions(): Collection
    {
        return Cache::remember(
            "user.{$this->id}.permissions",
            self::CACHE_TTL,
            fn () => $this->getAllPermissions()
        );
    }

    /**
     * Clear the user's permission cache.
     */
    public function clearPermissionCache(): void
    {
        Cache::forget("user.{$this->id}.permissions");
    }

    /**
     * Disable the user account.
     */
    public function disable(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Enable the user account.
     */
    public function enable(): bool
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * Check if the user is active.
     */
    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive users.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}







