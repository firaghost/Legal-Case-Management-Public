<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class AuditLogService
{
    public static function log($action, $auditableType = null, $auditableId = null, $changes = null, $ipAddress = null, $userAgent = null)
    {
        try {
            $user = Auth::user();
            
            $log = new AuditLog([
                'user_id' => $user ? $user->id : null,
                'auditable_type' => $auditableType,
                'auditable_id' => $auditableId,
                'action' => $action,
                'changes' => is_array($changes) ? json_encode($changes) : $changes,
                'ip_address' => $ipAddress ?? request()->ip(),
                'user_agent' => $userAgent ?? request()->userAgent(),
            ]);

            $log->save();
            return $log;
        } catch (\Exception $e) {
            Log::error('Failed to log audit action: ' . $e->getMessage(), [
                'action' => $action,
                'auditable_type' => $auditableType,
                'exception' => $e
            ]);
            return null;
        }
    }

    public static function logLogin($user, $success, $ipAddress = null, $userAgent = null)
    {
        return self::log(
            $success ? 'LOGIN_SUCCESS' : 'LOGIN_FAILED',
            'User',
            $user ? $user->id : null,
            ['email' => $user ? $user->email : 'unknown'],
            $ipAddress,
            $userAgent
        );
    }

    public static function logLogout($user, $ipAddress = null, $userAgent = null)
    {
        return self::log(
            'LOGOUT',
            'User',
            $user ? $user->id : null,
            null,
            $ipAddress,
            $userAgent
        );
    }

    public static function logModelCreated($model, $userId = null, $ipAddress = null, $userAgent = null)
    {
        return self::log(
            'CREATE_' . class_basename($model),
            get_class($model),
            $model->id,
            $model->getChanges(),
            $ipAddress,
            $userAgent
        );
    }

    public static function logModelUpdated($model, $userId = null, $ipAddress = null, $userAgent = null)
    {
        return self::log(
            'UPDATE_' . class_basename($model),
            get_class($model),
            $model->id,
            $model->getChanges(),
            $ipAddress,
            $userAgent
        );
    }

    public static function logModelDeleted($model, $userId = null, $ipAddress = null, $userAgent = null)
    {
        return self::log(
            'DELETE_' . class_basename($model),
            get_class($model),
            $model->id,
            $model->getOriginal(),
            $ipAddress,
            $userAgent
        );
    }
}






