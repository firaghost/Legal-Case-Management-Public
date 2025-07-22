<?php

namespace App\Traits;

use App\Models\ActionLog;
use Illuminate\Support\Facades\Auth;

/**
 * Trait for adding action logging to models.
 */
trait HasActionLogs
{
    /**
     * Boot the trait.
     */
    public static function bootHasActionLogs()
    {
        static::created(function ($model) {
            if (Auth::check()) {
                ActionLog::logAction(
                    $model,
                    'created',
                    $model->attributesToArray()
                );
            }
        });

        static::updated(function ($model) {
            // Only log if there are actual changes
            if (count($model->getChanges()) > 0 && Auth::check()) {
                ActionLog::logAction(
                    $model,
                    'updated',
                    $model->getChanges(),
                    array_intersect_key($model->getOriginal(), $model->getChanges()),
                    $model->getActionLogDescription('updated')
                );
            }
        });

        static::deleted(function ($model) {
            if (Auth::check()) {
                ActionLog::logAction(
                    $model,
                    'deleted',
                    $model->getAttributes(),
                    null,
                    $model->getActionLogDescription('deleted')
                );
            }
        });
    }

    /**
     * Get the action log description for the model.
     */
    public function getActionLogDescription(string $action): string
    {
        $modelName = class_basename($this);
        
        if (method_exists($this, 'getActionLogName')) {
            $name = $this->getActionLogName();
            return "{$action} {$modelName}: {$name}";
        }
        
        if (isset($this->name)) {
            return "{$action} {$modelName}: {$this->name}";
        }
        
        if (isset($this->title)) {
            return "{$action} {$modelName}: {$this->title}";
        }
        
        return "{$action} {$modelName} #{$this->getKey()}";
    }

    /**
     * Get all action logs for the model.
     */
    public function actionLogs()
    {
        return $this->morphMany(ActionLog::class, 'model');
    }

    /**
     * Log a custom action for the model.
     */
    public function logAction(string $action, array $properties = [], ?string $description = null): ActionLog
    {
        return ActionLog::logAction(
            $this,
            $action,
            $properties,
            null,
            $description ?? $this->getActionLogDescription($action)
        );
    }
}






