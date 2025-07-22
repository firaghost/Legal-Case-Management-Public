<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Request;

class ActionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'properties',
        'old_properties',
        'ip_address',
        'user_agent',
        'description',
    ];

    protected $casts = [
        'properties' => 'array',
        'old_properties' => 'array',
    ];

    /**
     * Get the user that performed the action.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent model (polymorphic).
     */
    public function model(): MorphTo
    {
        return $this->morphTo('model', 'model_type', 'model_id');
    }

    /**
     * Scope a query to only include logs for a specific model.
     */
    public function scopeForModel($query, $model)
    {
        return $query->where('model_type', get_class($model))
                    ->where('model_id', $model->getKey());
    }

    /**
     * Scope a query to only include logs for a specific action.
     */
    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope a query to only include logs for a specific user.
     */
    public function scopeForUser($query, $user)
    {
        $userId = $user instanceof User ? $user->id : $user;
        return $query->where('user_id', $userId);
    }

    /**
     * Log an action.
     */
    public static function logAction($model, string $action, array $properties = [], ?array $oldProperties = null, ?string $description = null): self
    {
        $user = auth()->user();
        
        $log = new static([
            'user_id' => $user ? $user->id : null,
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'properties' => $properties,
            'old_properties' => $oldProperties,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'description' => $description ?? ucfirst($action) . ' ' . class_basename($model) . ' #' . $model->getKey(),
        ]);

        $log->save();

        return $log;
    }
}






