<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CaseFile;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'users' => User::count(),
            'open_cases' => CaseFile::whereNull('closed_at')->count(),
            'closed_cases' => CaseFile::whereNotNull('closed_at')->count(),
            'lawyers' => User::where('role', 'lawyer')->count(),
            'supervisors' => User::where('role', 'supervisor')->count(),
            'pending' => CaseFile::whereNull('approved_at')->count(),
        ];

        // New cases per month (last 6 months)
        $graph = CaseFile::where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, DATE_FORMAT(created_at, '%b') as month, COUNT(*) as count")
            ->groupBy('ym', 'month')
            ->orderBy('ym')
            ->get();

        return view('admin.dashboard', compact('stats', 'graph'));
    }
}






