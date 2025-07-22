<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use App\Models\ProgressUpdate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProgressUpdateController extends Controller
{
    /**
     * Show the form for creating a new progress update.
     */
    public function create(CaseFile $case)
    {
        // Only assigned lawyer can add updates
        abort_if(auth()->id() !== $case->lawyer_id, 403);

        return view('lawyer.progress.create', [
            'case' => $case->load('caseType')
        ]);
    }
    /**
     * Store a new progress update for a case.
     */
    public function store(Request $request, CaseFile $case): RedirectResponse
    {
        $user = $request->user();
        
        \Log::info('Starting progress update for case', [
            'case_id' => $case->id,
            'user_id' => $user->id,
            'user_name' => $user->name,
        ]);

        try {
            // Only assigned lawyer can add updates
            if ($user->id !== $case->lawyer_id) {
                \Log::warning('Unauthorized attempt to add progress update', [
                    'case_id' => $case->id,
                    'user_id' => $user->id,
                ]);
                abort(403);
            }

            $validated = $request->validate([
                'status' => ['required', 'string', 'max:255'],
                'notes'  => ['nullable', 'string'],
                'attachment' => ['nullable', 'file', 'max:8192'], // 8 MB
            ]);

            \Log::debug('Progress update validation passed', [
                'case_id' => $case->id,
                'status' => $validated['status'],
                'has_notes' => !empty($validated['notes']),
                'has_attachment' => $request->hasFile('attachment'),
            ]);

            $update = new ProgressUpdate([
                'status' => $validated['status'],
                'notes'  => $validated['notes'] ?? null,
            ]);
            $update->user()->associate($user);
            $update->caseFile()->associate($case);

            if ($request->hasFile('attachment')) {
                try {
                    $path = $request->file('attachment')->store('progress_attachments');
                    $update->attachment_path = $path;
                    \Log::debug('Attachment stored successfully', [
                        'path' => $path,
                        'original_name' => $request->file('attachment')->getClientOriginalName(),
                        'size' => $request->file('attachment')->getSize(),
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to store attachment', [
                        'case_id' => $case->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    return back()
                        ->withInput()
                        ->withErrors(['attachment' => 'Failed to upload attachment. Please try again.']);
                }
            }

            $update->save();

            \Log::info('Progress update saved successfully', [
                'update_id' => $update->id,
                'case_id' => $case->id,
                'status' => $update->status,
            ]);

            return back()->with([
                'success' => 'Progress update added successfully.',
                'scroll_to' => 'progress-updates',
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to save progress update', [
                'case_id' => $case->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to save progress update. Please try again.']);
        }
    }
}






