<?php

namespace App\Traits;

use App\Models\ActionLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Trait for logging actions in controllers.
 */
trait LogsActions
{
    /**
     * Log an action for a model.
     */
    protected function logAction(
        string $action,
        Model $model,
        ?array $properties = null,
        ?string $description = null
    ): ActionLog {
        $user = Auth::user();
        $properties = $properties ?? $model->getDirty();
        
        return ActionLog::logAction(
            $model,
            $action,
            $properties,
            $model->getOriginal(),
            $description
        );
    }

    /**
     * Log a custom action.
     */
    protected function logCustomAction(
        string $action,
        string $description,
        ?Model $model = null,
        ?array $properties = null
    ): ?ActionLog {
        if (!$model) {
            return null;
        }
        
        return $this->logAction($action, $model, $properties, $description);
    }

    /**
     * Log a successful login.
     */
    protected function logLogin(): void
    {
        $user = Auth::user();
        
        ActionLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'model_type' => get_class($user),
            'model_id' => $user->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => 'User logged in',
        ]);
    }

    /**
     * Log a failed login attempt.
     */
    protected function logFailedLogin(string $email): void
    {
        ActionLog::create([
            'action' => 'failed_login',
            'model_type' => 'User',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => "Failed login attempt for email: {$email}",
        ]);
    }

    /**
     * Log a logout action.
     */
    protected function logLogout(): void
    {
        $user = Auth::user();
        
        if (!$user) {
            return;
        }
        
        ActionLog::create([
            'user_id' => $user->id,
            'action' => 'logout',
            'model_type' => get_class($user),
            'model_id' => $user->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => 'User logged out',
        ]);
    }

    /**
     * Log a review action on a case file.
     */
    protected function logReview(Model $caseFile, ?string $description = null): ActionLog
    {
        return $this->logAction('review', $caseFile, null, $description ?? 'Case file reviewed');
    }

    /**
     * Log a close action on a case file.
     */
    protected function logClose(Model $caseFile, ?string $description = null): ActionLog
    {
        return $this->logAction('close', $caseFile, null, $description ?? 'Case file closed');
    }
}






