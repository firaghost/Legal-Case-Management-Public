<?php

namespace App\Http\Controllers;

use App\Http\Requests\CaseFileRequest;
use App\Models\CaseFile;
use App\Models\ActionLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CaseFileController extends Controller
{
    /**
     * Display a listing of case files.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $this->authorize('viewAny', CaseFile::class);
        
        $perPage = $this->getPerPage();
        [$sort, $direction] = $this->getSortParams();
        
        $query = CaseFile::with(['lawyer', 'court', 'plaintiffs', 'defendants'])
            ->orderBy($sort, $direction);
            
        // If user is not admin, only show their cases or cases they have access to
        if (!auth()->user()->isAdmin()) {
            $query->where('lawyer_id', auth()->id())
                ->orWhereHas('collaborators', function($q) {
                    $q->where('user_id', auth()->id());
                });
        }
        
        $caseFiles = $query->paginate($perPage);
        
        return $this->success($caseFiles, 'Case files retrieved successfully.');
    }

    /**
     * Store a newly created case file in storage.
     *
     * @param  \App\Http\Requests\CaseFileRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CaseFileRequest $request)
    {
        $this->authorize('create', CaseFile::class);
        
        $caseFile = DB::transaction(function () use ($request) {
            // Create the case file
            $caseFile = new CaseFile($request->validated());
            $caseFile->save();
            
            // Sync plaintiffs if provided
            if ($request->has('plaintiffs')) {
                $plaintiffs = collect($request->plaintiffs)->map(function ($plaintiff) {
                    return [
                        'id' => (string) Str::uuid(),
                        'name' => $plaintiff['name'],
                        'type' => $plaintiff['type'],
                        'contact_info' => $plaintiff['contact_info'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->toArray();
                
                $caseFile->plaintiffs()->createMany($plaintiffs);
            }
            
            // Sync defendants if provided
            if ($request->has('defendants')) {
                $defendants = collect($request->defendants)->map(function ($defendant) {
                    return [
                        'id' => (string) Str::uuid(),
                        'name' => $defendant['name'],
                        'type' => $defendant['type'],
                        'contact_info' => $defendant['contact_info'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->toArray();
                
                $caseFile->defendants()->createMany($defendants);
            }
            
            // Handle file uploads if provided
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $document) {
                    $path = $document['file']->store('case_documents');
                    
                    $caseFile->documents()->create([
                        'title' => $document['title'],
                        'description' => $document['description'] ?? null,
                        'file_path' => $path,
                        'file_name' => $document['file']->getClientOriginalName(),
                        'mime_type' => $document['file']->getClientMimeType(),
                        'size' => $document['file']->getSize(),
                    ]);
                }
            }
            
            // Log the action
            $this->logAction('create', $caseFile, null, 'Case file created');
            
            return $caseFile->load(['lawyer', 'court', 'plaintiffs', 'defendants', 'documents']);
        });
        
        return $this->success($caseFile, 'Case file created successfully.', Response::HTTP_CREATED);
    }

    /**
     * Display the specified case file.
     *
     * @param  \App\Models\CaseFile  $case
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(CaseFile $case)
    {
        $this->authorize('view', $case);
        
        $case->load(['lawyer', 'court', 'plaintiffs', 'defendants', 'documents', 'actionLogs.user']);
        
        return $this->success($case, 'Case file retrieved successfully.');
    }

    /**
     * Update the specified case file in storage.
     *
     * @param  \App\Http\Requests\CaseFileRequest  $request
     * @param  \App\Models\CaseFile  $case
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CaseFileRequest $request, CaseFile $case)
    {
        $this->authorize('update', $case);
        
        $originalData = $case->toArray();
        $changes = [];
        
        $case = DB::transaction(function () use ($request, $case, &$changes) {
            // Track changes
            $changes = $case->getDirty();
            
            // Update the case file
            $case->fill($request->validated());
            $case->save();
            
            // Sync plaintiffs if provided
            if ($request->has('plaintiffs')) {
                $case->plaintiffs()->delete(); // Remove existing plaintiffs
                $plaintiffs = collect($request->plaintiffs)->map(function ($plaintiff) {
                    return [
                        'id' => (string) Str::uuid(),
                        'name' => $plaintiff['name'],
                        'type' => $plaintiff['type'],
                        'contact_info' => $plaintiff['contact_info'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->toArray();
                
                $case->plaintiffs()->createMany($plaintiffs);
            }
            
            // Sync defendants if provided
            if ($request->has('defendants')) {
                $case->defendants()->delete(); // Remove existing defendants
                $defendants = collect($request->defendants)->map(function ($defendant) {
                    return [
                        'id' => (string) Str::uuid(),
                        'name' => $defendant['name'],
                        'type' => $defendant['type'],
                        'contact_info' => $defendant['contact_info'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->toArray();
                
                $case->defendants()->createMany($defendants);
            }
            
            // Handle file uploads if provided
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $document) {
                    $path = $document['file']->store('case_documents');
                    
                    $case->documents()->create([
                        'title' => $document['title'],
                        'description' => $document['description'] ?? null,
                        'file_path' => $path,
                        'file_name' => $document['file']->getClientOriginalName(),
                        'mime_type' => $document['file']->getClientMimeType(),
                        'size' => $document['file']->getSize(),
                    ]);
                }
            }
            
            return $case->load(['lawyer', 'court', 'plaintiffs', 'defendants', 'documents']);
        });
        
        // Log the action with changes
        $this->logAction('update', $case, $changes, 'Case file updated');
        
        return $this->success($case, 'Case file updated successfully.');
    }

    /**
     * Remove the specified case file from storage.
     *
     * @param  \App\Models\CaseFile  $case
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(CaseFile $case)
    {
        $this->authorize('delete', $case);
        
        DB::transaction(function () use ($case) {
            // Soft delete related records
            $case->plaintiffs()->delete();
            $case->defendants()->delete();
            $case->documents()->delete();
            
            // Soft delete the case file
            $case->delete();
            
            // Log the action
            $this->logAction('delete', $case, null, 'Case file deleted');
        });
        
        return $this->success(null, 'Case file deleted successfully.');
    }
    
    /**
     * Restore the specified case file.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $caseFile = CaseFile::withTrashed()->findOrFail($id);
        
        $this->authorize('restore', $caseFile);
        
        DB::transaction(function () use ($caseFile) {
            // Restore related records
            $caseFile->plaintiffs()->withTrashed()->restore();
            $caseFile->defendants()->withTrashed()->restore();
            $caseFile->documents()->withTrashed()->restore();
            
            // Restore the case file
            $caseFile->restore();
            
            // Log the action
            $this->logAction('restore', $caseFile, null, 'Case file restored');
        });
        
        return $this->success($caseFile->load(['lawyer', 'court', 'plaintiffs', 'defendants', 'documents']), 'Case file restored successfully.');
    }
    
    /**
     * Get the case file's action logs.
     *
     * @param  \App\Models\CaseFile  $case
     * @return \Illuminate\Http\JsonResponse
     */
    public function logs(CaseFile $case)
    {
        $this->authorize('view', $case);
        
        $logs = $case->actionLogs()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($this->getPerPage());
            
        return $this->success($logs, 'Action logs retrieved successfully.');
    }
}






