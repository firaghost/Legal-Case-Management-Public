<?php

namespace App\Http\Controllers;

use App\Models\Appeal;
use App\Models\CaseFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppealController extends Controller
{
    /**
     * Show form to create a new appeal / second appeal / cassation entry.
     */
    public function create(CaseFile $case): View
    {
        // Only assigned lawyer or admin can create appeal entries
        abort_if(! $this->canModify($case), 403);

        return view('lawyer.appeals.create', [
            'case' => $case,
        ]);
    }

    /**
     * Persist new appeal entry.
     */
    public function store(Request $request, CaseFile $case): RedirectResponse
    {
        abort_if(! $this->canModify($case), 403);

        // Validate inputs
        $validated = $request->validate([
            'level'            => ['required', 'in:Appeal,Second,Cassation'],
            'file_number'      => ['required', 'string', 'max:255'],
            'notes'            => ['nullable', 'string'],
            'decided_at'       => ['nullable', 'date'],
        ]);

        // Prevent duplicates and enforce order
        $order = ['Appeal', 'Second', 'Cassation'];
        $existingLevels = $case->appeals()->pluck('level')->toArray();

        // Duplicate check
        if (in_array($validated['level'], $existingLevels, true)) {
            return back()->withInput()->withErrors(['level' => 'This stage has already been recorded for this case.']);
        }
        // Order check: next expected level should match
        $expectedLevel = $order[count($existingLevels)] ?? null;
        if ($validated['level'] !== $expectedLevel) {
            return back()->withInput()->withErrors(['level' => "Invalid stage sequence. Next expected stage is {$expectedLevel}."]);
        }

        $appeal = new Appeal($validated);
        $appeal->caseFile()->associate($case);
        $appeal->save();

        // Add timeline / progress update entry automatically.
        $statusMap = [
            'Appeal'   => 'Appeal Filed',
            'Second'   => 'Second Appeal Filed',
            'Cassation' => 'Cassation Requested',
        ];

        $case->progressUpdates()->create([
            'updated_by' => $request->user()->id,
            'status'  => $statusMap[$validated['level']] ?? 'Appeal Stage',
            'notes'   => $validated['notes'] ?? null,
        ]);

        return redirect()->route('lawyer.cases.show', $case)->with('success', 'Appeal stage added.');
    }

    /**
     * Check if signed-in user can modify appeal data for a case.
     */
    private function canModify(CaseFile $case): bool
    {
        $user = auth()->user();
        return $user?->id === $case->lawyer_id || $user?->hasRole('admin');
    }
}






