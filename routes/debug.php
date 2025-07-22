<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\User;

// Debug route to check database state
Route::get('/debug/users', function () {
    // Check if column exists
    $columnExists = Schema::hasColumn('users', 'password_change_required');
    
    // Get first user with raw query
    $rawUser = DB::table('users')->first();
    
    // Get first user with model
    $modelUser = User::first();
    
    return response()->json([
        'column_exists' => $columnExists,
        'raw_user' => $rawUser,
        'model_user' => $modelUser,
        'model_user_attributes' => $modelUser ? $modelUser->getAttributes() : null,
        'fillable_fields' => (new User())->getFillable(),
        'casts' => (new User())->getCasts(),
    ]);
});






