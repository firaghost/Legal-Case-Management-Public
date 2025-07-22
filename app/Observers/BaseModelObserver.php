<?php

namespace App\Observers;

use App\Services\AuditLogService;
use Illuminate\Database\Eloquent\Model;

class BaseModelObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        $this->logModelAction($model, 'CREATED');
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        $this->logModelAction($model, 'UPDATED', $model->getChanges());
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        $this->logModelAction($model, 'DELETED');
    }

    /**
     * Handle the Model "restored" event.
     */
    public function restored(Model $model): void
    {
        $this->logModelAction($model, 'RESTORED');
    }

    /**
     * Handle the Model "forceDeleted" event.
     */
    public function forceDeleted(Model $model): void
    {
        $this->logModelAction($model, 'FORCE_DELETED');
    }

    /**
     * Log model action to audit log
     */
    protected function logModelAction(Model $model, string $action, array $changes = []): void
    {
        try {
            $modelClass = get_class($model);
            $modelName = class_basename($modelClass);
            
            AuditLogService::log(
                "{$action}_{$modelName}",
                $modelClass,
                $model->getKey(),
                $this->prepareChangesForLogging($model, $changes, $action)
            );
        } catch (\Exception $e) {
            \Log::error("Failed to log model action: " . $e->getMessage(), [
                'model' => get_class($model),
                'action' => $action,
                'exception' => $e
            ]);
        }
    }

    /**
     * Prepare changes for logging
     */
    protected function prepareChangesForLogging(Model $model, array $changes, string $action): array
    {
        $prepared = [];
        $hidden = $model->getHidden();
        
        // Handle specific actions
        if ($action === 'UPDATED') {
            foreach ($changes as $attribute => $value) {
                // Skip hidden attributes
                if (in_array($attribute, $hidden)) {
                    $prepared[$attribute] = '**HIDDEN**';
                    continue;
                }
                
                // Handle password hashing
                if ($attribute === 'password') {
                    $prepared[$attribute] = '**HASHED**';
                    continue;
                }
                
                // Format the change
                $prepared[$attribute] = [
                    'old' => $model->getOriginal($attribute, null),
                    'new' => $value
                ];
            }
        } else {
            // For other actions, log all attributes except hidden ones
            $attributes = $model->getAttributes();
            foreach ($attributes as $key => $value) {
                if (in_array($key, $hidden)) {
                    $prepared[$key] = '**HIDDEN**';
                } elseif ($key === 'password') {
                    $prepared[$key] = '**HASHED**';
                } else {
                    $prepared[$key] = $value;
                }
            }
        }
        
        return $prepared;
    }
}






