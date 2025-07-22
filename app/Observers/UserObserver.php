<?php

namespace App\Observers;

use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class UserObserver extends BaseModelObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(Model $user): void
    {
        parent::created($user);
        if ($user instanceof User) {
            $this->logUserAction($user, 'USER_CREATED');
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(Model $user): void
    {
        parent::updated($user);
        if ($user instanceof User) {
            $this->logUserAction($user, 'USER_UPDATED');
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(Model $user): void
    {
        parent::deleted($user);
        if ($user instanceof User) {
            $this->logUserAction($user, 'USER_DELETED');
        }
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(Model $user): void
    {
        parent::restored($user);
        if ($user instanceof User) {
            $this->logUserAction($user, 'USER_RESTORED');
        }
    }

    /**
     * Log user-specific actions
     */
    protected function logUserAction(User $user, string $action): void
    {
        try {
            $changes = [
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'roles' => $user->roles->pluck('name')->toArray(),
                'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
            ];

            AuditLogService::log(
                $action,
                get_class($user),
                $user->id,
                $changes
            );
        } catch (\Exception $e) {
            Log::error('Failed to log user action: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'action' => $action,
                'exception' => $e
            ]);
        }
    }

    /**
     * Handle the User "password reset" event.
     */
    public function passwordReset(User $user): void
    {
        $this->logUserAction($user, 'PASSWORD_RESET');
    }

    /**
     * Handle the User "email verified" event.
     */
    public function emailVerified(User $user): void
    {
        $this->logUserAction($user, 'EMAIL_VERIFIED');
    }

    /**
     * Handle the User "login" event.
     */
    public function login(User $user): void
    {
        // This is handled by the LogSuccessfulLogin listener
    }

    /**
     * Handle the User "logout" event.
     */
    public function logout(User $user): void
    {
        // This is handled by the LogLogout listener
    }
}






