<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use App\Models\Evidence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class EvidenceController extends Controller
{
    /**
     * Upload evidence (PDF/Image) for a case file.
     */
    public function store(Request $request, CaseFile $caseFile)
    {
        $this->authorize('update', $caseFile);

        $data = $request->validate([
            'file' => ['required', 'file', 'max:10240', Rule::mimes(['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff'])],
        ]);

        $file = $data['file'];
        $original = $file->getClientOriginalName();
        $storedName = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('evidences', $storedName);

        // Calculate SHA-256 for tamper detection
        $hash = hash_file('sha256', Storage::path($path));

        $evidence = Evidence::create([
            'case_file_id' => $caseFile->id,
            'original_name' => $original,
            'stored_name' => $storedName,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'hash' => $hash,
            'uploaded_by' => $request->user()->id,
        ]);

        return response()->json($evidence, 201);
    }
}






