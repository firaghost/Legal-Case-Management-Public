<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkUnit;
use Illuminate\Validation\Rule;

class WorkUnitController extends Controller
{
    public function index()
    {
        $workUnits = WorkUnit::orderBy('name')->get();
        return view('admin.work-units.index', compact('workUnits'));
    }

    public function create()
    {
        return view('admin.work-units.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('work_units', 'name')->whereNull('deleted_at')],
            'code' => ['nullable', 'string', 'max:50', Rule::unique('work_units', 'code')->whereNull('deleted_at')],
            'is_active' => ['nullable'],
        ]);
        $validated['is_active'] = $request->has('is_active');
        WorkUnit::create($validated);
        return redirect()->route('admin.work-units.index')->with('status', 'Work unit created successfully.');
    }

    public function edit(WorkUnit $workUnit)
    {
        return view('admin.work-units.edit', compact('workUnit'));
    }

    public function update(Request $request, WorkUnit $workUnit)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('work_units', 'name')->ignore($workUnit->id)->whereNull('deleted_at')],
            'code' => ['nullable', 'string', 'max:50', Rule::unique('work_units', 'code')->ignore($workUnit->id)->whereNull('deleted_at')],
            'is_active' => ['nullable'],
        ]);
        $validated['is_active'] = $request->has('is_active');
        $workUnit->update($validated);
        return redirect()->route('admin.work-units.index')->with('status', 'Work unit updated successfully.');
    }

    public function destroy(WorkUnit $workUnit)
    {
        $workUnit->delete();
        return redirect()->route('admin.work-units.index')->with('status', 'Work unit deleted successfully.');
    }
} 





