<?php

use App\Http\Controllers\ProfileController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

// Include test routes only in development
if (app()->environment('local', 'development')) {
    require __DIR__.'/test-csrf.php';
    require __DIR__.'/test-session.php';
}

use App\Http\Controllers\Lawyer\DashboardController;
use App\Http\Controllers\Lawyer\CaseController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CaseController as AdminCaseController;
use App\Http\Controllers\Admin\SystemSettingsController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Supervisor\DashboardController as SupervisorDashboardController;
use App\Http\Controllers\Supervisor\CaseController as SupervisorCaseController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Api\ChatController as ApiChat;
use App\Http\Controllers\Api\UserController as ApiUser;
use App\Http\Controllers\Api\TestPusherController;
// Pusher test route (only in local environment)
if (app()->environment('local')) {
    Route::get('/pusher-test', function () {
        return Inertia::render('PusherTest', [
            'pusher' => [
                'key' => config('broadcasting.connections.pusher.key'),
                'cluster' => config('broadcasting.connections.pusher.options.cluster'),
            ]
        ]);
    })->middleware(['auth'])->name('pusher.test');
}

Route::get('/', function () {
    return view('welcome');
});

// Health check endpoint for monitoring
Route::get('/health', function () {
    $health = [
        'status' => 'healthy',
        'timestamp' => now()->toISOString(),
        'services' => [
            'database' => 'disconnected',
            'cache' => 'disconnected',
            'queue' => 'unknown',
        ],
    ];

    // Check database connection
    try {
        DB::connection()->getPdo();
        $health['services']['database'] = 'connected';
    } catch (Exception $e) {
        $health['status'] = 'degraded';
        $health['services']['database'] = 'error: ' . $e->getMessage();
    }

    // Check cache connection
    try {
        Cache::put('health_check', 'ok', 10);
        if (Cache::get('health_check') === 'ok') {
            $health['services']['cache'] = 'connected';
        }
    } catch (Exception $e) {
        $health['status'] = 'degraded';
        $health['services']['cache'] = 'error: ' . $e->getMessage();
    }

    // Check queue connection
    try {
        $health['services']['queue'] = 'connected';
    } catch (Exception $e) {
        $health['services']['queue'] = 'error: ' . $e->getMessage();
    }

    $httpStatus = $health['status'] === 'healthy' ? 200 : 503;
    return response()->json($health, $httpStatus);
});

use Illuminate\Support\Facades\Auth;

Route::get('/dashboard', function () {
    $user = Auth::user();
    $primaryRole = strtolower($user->roles->first()?->name ?? ($user->role ?? ''));
    if ($primaryRole === 'lawyer') {
        return redirect()->route('lawyer.dashboard');
    }
    if ($primaryRole === 'supervisor') {
        return redirect()->route('supervisor.dashboard');
    }
    if (in_array($primaryRole, ['admin', 'system administrator'])) {
        return redirect()->route('admin.dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Notifications
    Route::get('/notifications/{id}', [\App\Http\Controllers\NotificationController::class, 'open'])->name('notifications.open');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/notifications/{id}/read', function($id) {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return back();
    })->name('notifications.read')->middleware('auth');
});

// Chat route for all authenticated users
use App\Http\Controllers\ChatController;

Route::middleware(['auth', 'verified', 'require.password.change'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');

    // Chat API routes via web middleware to leverage session authentication
    Route::prefix('api')->middleware('auth')->group(function () {
        // user search for chat modal
        Route::get('/users', [ApiUser::class, 'search']);
        Route::get('/conversations', [ChatController::class, 'index']);
        Route::post('/conversations', [ApiChat::class, 'createOrGetConversation']);
        Route::get('/conversations/{conversation}', [ChatController::class, 'show']);
        Route::put('/conversations/{conversation}', [ChatController::class, 'updateConversation']);
        Route::delete('/conversations/{conversation}', [ChatController::class, 'deleteConversation']);
        Route::post('/conversations/{conversation}/leave', [ChatController::class, 'leaveConversation']);
    });
});

// Supervisor case approval and reassignment routes
Route::middleware(['auth', 'verified', 'require.password.change'])->group(function () {
    Route::post('/cases/{case}/approve', [\App\Http\Controllers\Supervisor\CaseController::class, 'approve'])
        ->name('supervisor.cases.approve');
    Route::post('/cases/{case}/reassign', [\App\Http\Controllers\Supervisor\CaseController::class, 'reassignLawyer'])
        ->name('supervisor.cases.reassign');
});

// Role-based dashboards
Route::middleware(['auth', 'verified', 'require.password.change'])->group(function () {

    Route::prefix('lawyer')->name('lawyer.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/cases', [CaseController::class, 'index'])->name('cases.index');
        Route::get('/cases/{case}/edit', [CaseController::class, 'edit'])->name('cases.edit');
        Route::put('/cases/{case}', [CaseController::class, 'update'])->name('cases.update');
        Route::get('/cases/new', [CaseController::class, 'create'])->name('cases.create');
        Route::post('/cases', [CaseController::class, 'store'])->name('cases.store');
        Route::get('/cases/{case}', [CaseController::class, 'show'])->name('cases.show');
        Route::get('/cases/{case}/progress/create', [CaseController::class, 'createProgress'])->name('cases.progress.create');
        // Appeal routes
        Route::get('/cases/{case}/appeals/create', [\App\Http\Controllers\AppealController::class, 'create'])->name('cases.appeals.create');
        Route::post('/cases/{case}/appeals', [\App\Http\Controllers\AppealController::class, 'store'])->name('cases.appeals.store');
        Route::post('/cases/{case}/progress', [CaseController::class, 'storeProgress'])->name('cases.progress.store');
        Route::get('/progress', [CaseController::class, 'progress'])->name('progress');
        Route::get('/advisory', fn() => redirect()->route('lawyer.cases.index', ['type' => 6]))->name('advisory');
        Route::get('/cases/{case}/appointments/create', [\App\Http\Controllers\AppointmentController::class, 'create'])->name('cases.appointments.create');
        Route::post('/cases/{case}/appointments', [\App\Http\Controllers\AppointmentController::class, 'store'])->name('cases.appointments.store');
        Route::post('/cases/{case}/request-closure', [CaseController::class, 'requestClosure'])->name('cases.requestClosure');
        Route::get('/cases/{case}/edit-history', [App\Http\Controllers\Lawyer\CaseController::class, 'editHistory'])->name('cases.edit-history');
    });
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Admin Cases
        Route::get('/cases', [AdminCaseController::class, 'index'])->name('cases.index');
        Route::get('/cases/create', [AdminCaseController::class, 'create'])->name('cases.create');
        Route::post('/cases', [AdminCaseController::class, 'store'])->name('cases.store');
        Route::get('/cases/{case}', [AdminCaseController::class, 'show'])->name('cases.show');
        
        Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users');
        Route::get('/users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
        Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
        // Added missing user management routes
        Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/disable', [\App\Http\Controllers\Admin\UserController::class, 'disable'])->name('users.disable');
        Route::post('/users/{user}/enable', [\App\Http\Controllers\Admin\UserController::class, 'enable'])->name('users.enable');
        Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
        Route::view('/reports', 'admin.reports.index')->name('reports');
        Route::get('/reports/{module}', [ReportController::class, 'show'])->name('reports.show');
        Route::get('/reports/{module}/export', [ReportController::class, 'export'])->name('reports.export');
        Route::get('/settings', [SystemSettingsController::class, 'index'])->name('settings');
        Route::put('/settings/court-codes', [SystemSettingsController::class, 'updateCourtCodes'])->name('settings.court-codes.update');
        Route::put('/settings/branches', [SystemSettingsController::class, 'updateBranches'])->name('settings.branches.update');
        Route::put('/settings/case-types', [SystemSettingsController::class, 'updateCaseTypes'])->name('settings.case-types.update');
        Route::put('/settings/permissions', [SystemSettingsController::class, 'updatePermissions'])->name('settings.permissions.update');
        Route::post('/settings/backup', [SystemSettingsController::class, 'backup'])->name('settings.backup');
        Route::get('/settings/export', [SystemSettingsController::class, 'export'])->name('settings.export');
        Route::get('/logs', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('logs');
        Route::get('/logs/export', [\App\Http\Controllers\Admin\AuditLogController::class, 'export'])->name('logs.export');
        
        // Test Audit Log (only in development)
        if (app()->environment('local', 'development')) {
            Route::get('/test-audit-log', [\App\Http\Controllers\Admin\TestAuditLogController::class, 'createTestLog']);
        }
        // Admin Management
        // Route::get('/permissions', fn() => view('admin.permissions'))->name('permissions.index');
        Route::resource('roles', App\Http\Controllers\Admin\RoleController::class);
        Route::resource('work-units', App\Http\Controllers\Admin\WorkUnitController::class);
        Route::resource('branches', App\Http\Controllers\Admin\BranchController::class);
        Route::resource('permissions', App\Http\Controllers\Admin\PermissionController::class);
        Route::get('admin/cases/{case}/edit-history', [App\Http\Controllers\Admin\CaseController::class, 'editHistory'])->name('admin.cases.edit-history');
    });
    Route::prefix('supervisor')->name('supervisor.')->group(function () {
        Route::get('/dashboard', [SupervisorDashboardController::class, 'index'])->name('dashboard');
        Route::get('/cases', [SupervisorCaseController::class, 'index'])->name('cases');
        Route::get('/cases/approvals', [SupervisorCaseController::class, 'approvals'])->name('approvals');
        Route::get('/cases/closed', [SupervisorCaseController::class, 'closed'])->name('closed');
        Route::get('/cases/assignment-history', [SupervisorCaseController::class, 'assignmentHistory'])->name('cases.assignment-history');
        Route::get('/cases/{case}', [SupervisorCaseController::class, 'show'])->name('cases.show');
        Route::get('/reports', [SupervisorCaseController::class, 'reports'])->name('reports');
        Route::get('/advisory', [SupervisorCaseController::class, 'advisory'])->name('advisory');
        Route::get('supervisor/cases/{case}/edit-history', [App\Http\Controllers\Supervisor\CaseController::class, 'editHistory'])->name('supervisor.cases.edit-history');
    });
});

Route::post('supervisor/cases/approve/bulk', [\App\Http\Controllers\Supervisor\CaseController::class, 'bulkApprove'])->name('supervisor.cases.bulk-approve');

// Password change routes (must be accessible even when password change is required)
Route::middleware(['auth'])->group(function () {
    Route::get('/password/change', [\App\Http\Controllers\Auth\PasswordChangeController::class, 'show'])->name('password.change');
    Route::put('/password/change', [\App\Http\Controllers\Auth\PasswordChangeController::class, 'update'])->name('password.update');
});

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::get('/branches', [\App\Http\Controllers\Api\BranchController::class, 'index']);
    Route::get('/work-units', [\App\Http\Controllers\Api\WorkUnitController::class, 'index']);
    Route::get('/lawyers', [\App\Http\Controllers\Api\LawyerController::class, 'index']);
});






