<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\JsonResponse;

class BranchController extends Controller
{
    /**
     * Return list of active branches (id & name)
     */
    public function index(): JsonResponse
    {
        try {
            \Log::info('BranchController@index called');
            $branches = Branch::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']);
            \Log::info('BranchController@index result', ['count' => $branches->count()]);
            return response()->json($branches, 200, [], JSON_PRETTY_PRINT);
        } catch (\Throwable $e) {
            \Log::error('BranchController@index error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
        }
    }
}






