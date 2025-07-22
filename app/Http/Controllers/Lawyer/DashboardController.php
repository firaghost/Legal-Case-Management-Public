<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\CaseFile;
use App\Models\ProgressUpdate;
use App\Models\Appointment;
use App\Models\CaseType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $lawyerId = Auth::id();

        // Basic counts
        $activeCases = CaseFile::where('lawyer_id', $lawyerId)
            ->whereNull('closed_at')
            ->count();

        $closedMonth = CaseFile::where('lawyer_id', $lawyerId)
            ->whereMonth('closed_at', now()->month)
            ->whereYear('closed_at', now()->year)
            ->count();

        $upcomingHearings = Appointment::whereHas('caseFile', fn($q) => $q->where('lawyer_id', $lawyerId))
            ->whereDate('appointment_date', '>=', now())
            ->count();

        // Get case types with counts
        $caseTypes = CaseType::withCount(['caseFiles' => function ($query) use ($lawyerId) {
            $query->where('lawyer_id', $lawyerId);
        }])->get();

        return view('lawyer.dashboard', [
            'stats' => [
                'active' => $activeCases,
                'closedMonth' => $closedMonth,
                'hearings' => $upcomingHearings,
            ],
            'caseTypes' => $caseTypes,
        ]);
    }
}






