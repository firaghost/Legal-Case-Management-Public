<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;

use App\Models\CaseFile;
use Illuminate\Contracts\View\View;

class CaseDetailController extends Controller
{
    /**
     * Display the case detail page.
     */
    public function __invoke(CaseFile $case): View
    {
        $case->load([
            'plaintiffs',
            'defendants',
            'lawyer',
            'branch',
            'court',
            'progressUpdates.user',
        ]);

        $user = auth()->user();
        $isAssignedLawyer = $user && $user->id === $case->lawyer_id;
        $isSupervisor = $user && $user->role === 'supervisor';

        // Determine type-specific data relation
        $caseTypeData = null;
        if ($case->caseType) {
            switch ($case->caseType->slug) {
                case 'secured_loan':
                    $case->loadMissing('securedLoanRecovery');
                    $caseTypeData = $case->securedLoanRecovery;
                    break;
                case 'clean_loan':
                    $case->loadMissing('cleanLoanRecovery');
                    $caseTypeData = $case->cleanLoanRecovery;
                    break;
                // add other case types here as needed
            }
        }

        

        $slug = $case->caseType->slug ?? Str::slug($case->caseType->name, '_');
        $viewPath = view()->exists("lawyer.cases.types." . $slug . ".show")
            ? "lawyer.cases.types." . $slug . ".show"
            : 'lawyer.cases.show';

        return view($viewPath, [
            'case' => $case,
            'caseTypeData' => $caseTypeData,
            'updates' => $case->progressUpdates()->orderByDesc('created_at')->get(),
            'isAssignedLawyer' => $isAssignedLawyer,
            'isSupervisor' => $isSupervisor,
        ]);
    }
}






