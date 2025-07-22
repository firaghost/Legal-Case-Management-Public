<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Court;
use App\Models\Branch;
use App\Models\CaseType;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SystemSettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings', [
            'courtCodes'  => Court::orderBy('id')->pluck('name', 'code'), // returns [code => name]
            'branches'    => Branch::orderBy('name')->pluck('name'),
            'caseTypes'   => CaseType::orderBy('name')->pluck('name'),
            'roles'       => Role::pluck('name'),
            'permissions' => Permission::orderBy('name')->pluck('name'),
            'matrix'      => [], // can be filled with current permissions if desired
        ]);
    }

    public function updateCourtCodes(Request $request)
    {
        $data = $request->input('codes', []);
        // Remove existing courts then re-insert
        Court::query()->delete();
        foreach ($data as $row) {
            if (empty($row['code']) || empty($row['name'])) {
                continue;
            }
            Court::create([
                'code' => $row['code'],
                'name' => $row['name'],
            ]);
        }
        return back()->with('status', 'Court codes updated');
    }

    public function updateBranches(Request $request)
    {
        $branches = array_filter($request->input('branches', []));
        Branch::query()->delete();
        foreach ($branches as $name) {
            Branch::create(['name' => $name, 'code' => strtoupper(str_replace(' ', '_', $name))]);
        }
        return back()->with('status', 'Branches updated');
    }

    public function updateCaseTypes(Request $request)
    {
        $types = array_filter($request->input('case_types', []));
        CaseType::query()->delete();
        foreach ($types as $name) {
            CaseType::create(['name' => $name]);
        }
        return back()->with('status', 'Case types updated');
    }

    public function updatePermissions(Request $request)
    {
        $matrix = $request->input('matrix', []);
        foreach ($matrix as $roleName => $perms) {
            $role = Role::where('name', $roleName)->first();
            if (!$role) continue;
            $allowed = array_keys(array_filter($perms));
            $role->syncPermissions($allowed);
        }
        return back()->with('status', 'Permissions updated');
    }

    public function backup()
    {
        // Placeholder – implement actual backup logic (e.g., database dump) here.
        return back()->with('status', 'Backup executed');
    }

    public function export()
    {
        // Placeholder – implement export logic (e.g., CSV / Excel download) here.
        return back()->with('status', 'Export generated');
    }
}






