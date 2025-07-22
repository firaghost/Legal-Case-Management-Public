<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkUnit;

class WorkUnitController extends Controller
{
    /**
     * Return list of active work units (id & name)
     */
    public function index()
    {
        try {
            \Log::info('WorkUnitController@index called');
            $units = WorkUnit::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']);
            \Log::info('WorkUnitController@index result', ['count' => $units->count()]);
            return response()->json($units, 200, [], JSON_PRETTY_PRINT);
        } catch (\Throwable $e) {
            \Log::error('WorkUnitController@index error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
        }
    }
}






