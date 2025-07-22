<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            self::logChange($model, 'created');
        });

        static::updated(function ($model) {
            self::logChange($model, 'updated', $model->getDirty());
        });

        static::deleted(function ($model) {
            self::logChange($model, 'deleted');
        });
    }

    protected static function logChange($model, string $action, array $changes = []): void
    {
        // Filter out timestamps if not relevant
        unset($changes['updated_at']);

        AuditLog::create([
            'user_id' => optional(Auth::user())->id,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->getKey(),
            'action' => $action,
            'changes' => $changes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}






