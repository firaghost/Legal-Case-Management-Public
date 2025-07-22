<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\CleanLoanRecoveryCase;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Show professional report page for a given module key.
     */
    public function show(string $module)
    {
        $view = "admin.reports.modules.$module";
        if (!view()->exists($view)) {
            // Fallback placeholder until real template is provided
            return view('admin.reports.placeholder', [
                'module' => Str::headline($module),
            ]);
        }
        $data = [];
        if ($module === 'clean-loan-recovery') {
            $query = CleanLoanRecoveryCase::with(['caseFile.branch','caseFile.court','caseFile.plaintiffs','caseFile.defendants']);
            if ($request = request()) {
                if ($request->filled('start_date') && $request->filled('end_date')) {
                    $query->whereHas('caseFile', function($q) use ($request) {
                        $q->whereBetween('opened_at', [$request->start_date, $request->end_date]);
                    });
                }
                if ($request->filled('branch_id')) {
                    $query->whereHas('caseFile', function($q) use ($request) {
                        $q->where('branch_id', $request->branch_id);
                    });
                }
            }
            $data['cases'] = $query->get();
            $data['branches'] = \App\Models\Branch::orderBy('name')->pluck('name', 'id');
        } elseif ($module === 'labor') {
            $query = \App\Models\LaborLitigationCase::with(['caseFile.branch','caseFile.court','caseFile.plaintiffs','caseFile.defendants']);
            $request = request();
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereHas('caseFile', function($q) use ($request){
                    $q->whereBetween('opened_at', [$request->start_date, $request->end_date]);
                });
            }
            if ($request->filled('branch_id')) {
                $query->whereHas('caseFile', function($q) use ($request){
                    $q->where('branch_id', $request->branch_id);
                });
            }
            $data['cases'] = $query->get();
            $data['branches'] = \App\Models\Branch::orderBy('name')->pluck('name','id');
        } elseif ($module === 'criminal') {
            $query = \App\Models\CriminalLitigationCase::with(['caseFile.branch','caseFile.court','caseFile.plaintiffs','caseFile.defendants']);
            $request = request();
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereHas('caseFile', function($q) use ($request){
                    $q->whereBetween('opened_at', [$request->start_date, $request->end_date]);
                });
            }
            if ($request->filled('branch_id')) {
                $query->whereHas('caseFile', function($q) use ($request){
                    $q->where('branch_id', $request->branch_id);
                });
            }
            $cases = $query->get();
            $data['cases'] = $cases;
            $data['branches'] = \App\Models\Branch::orderBy('name')->pluck('name','id');
            $data['statusCounts'] = $cases->groupBy('status')->map->count();
        } elseif ($module === 'secured-loan-recovery') {
            $query = \App\Models\SecuredLoanRecoveryCase::with(['caseFile.branch','caseFile.plaintiffs']);
            $request = request();
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereHas('caseFile', function($q) use ($request){
                    $q->whereBetween('opened_at', [$request->start_date, $request->end_date]);
                });
            }
            if ($request->filled('branch_id')) {
                $query->whereHas('caseFile', function($q) use ($request){
                    $q->where('branch_id', $request->branch_id);
                });
            }
            $cases = $query->get();
            $data['cases'] = $cases;
            $data['branches'] = \App\Models\Branch::orderBy('name')->pluck('name','id');
        } elseif ($module === 'advisory') {
            $query = \App\Models\LegalAdvisoryCase::with(['caseFile.branch']);
            $request = request();
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('request_date', [$request->start_date, $request->end_date]);
            }
            if ($request->filled('branch_id')) {
                $query->whereHas('caseFile', function($q) use ($request){
                    $q->where('branch_id', $request->branch_id);
                });
            }
            $cases = $query->get();
            $data['cases'] = $cases;
            $data['branches'] = \App\Models\Branch::orderBy('name')->pluck('name','id');
            // performance metric
            $data['performance'] = [
                'completed' => $cases->where('status','completed')->count(),
                'total' => $cases->count()
            ];
        } elseif ($module === 'other-civil') {
            $query = \App\Models\OtherCivilLitigationCase::with(['caseFile.branch','caseFile.court','caseFile.plaintiffs','caseFile.defendants']);
            $request = request();
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereHas('caseFile', function($q) use ($request){
                    $q->whereBetween('opened_at', [$request->start_date, $request->end_date]);
                });
            }
            if ($request->filled('branch_id')) {
                $query->whereHas('caseFile', function($q) use ($request){
                    $q->where('branch_id', $request->branch_id);
                });
            }
            $data['cases'] = $query->get();
            $data['branches'] = \App\Models\Branch::orderBy('name')->pluck('name','id');
        } elseif ($module === 'loan-recovery') {
            // legacy alias
            return redirect()->route('admin.reports.show', 'clean-loan-recovery');
        } elseif ($module === 'all') {
            $query = \App\Models\CaseFile::with(['caseType', 'branch', 'court', 'plaintiffs', 'defendants', 'lawyer']);
            $request = request();
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('opened_at', [$request->start_date, $request->end_date]);
            }
            if ($request->filled('branch_id')) {
                $query->where('branch_id', $request->branch_id);
            }
            if ($request->filled('type_id')) {
                $query->where('case_type_id', $request->type_id);
            }
            $cases = $query->get();
            $data['cases'] = $cases;
            $data['branches'] = \App\Models\Branch::orderBy('name')->pluck('name','id');
            $data['types'] = \App\Models\CaseType::orderBy('name')->pluck('name','id');
        } else {
            // default: pass branches list only
            $data['branches'] = \App\Models\Branch::orderBy('name')->pluck('name', 'id');
        }

        return view($view, $data);
    }

    /**
     * Export report (e.g., PDF) for a given module key.
     */
    public function export(string $module): StreamedResponse
    {
        $format = request('format','pdf');
        if ($module === 'clean-loan-recovery') {
            $cases = CleanLoanRecoveryCase::with(['caseFile.branch','caseFile.court','caseFile.plaintiffs','caseFile.defendants'])->get();
            if ($format === 'excel') {
                $headers = [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="clean-loan-recovery.csv"',
                ];
                return response()->streamDownload(function() use ($cases) {
                    $out = fopen('php://output', 'w');
                    fputcsv($out, ['File No','Plaintiff','Defendant','Outstanding','Status','Recovered','Performance %','Branch','Court & File No.']);
                    foreach ($cases as $c) {
                        $cf = $c->caseFile;
                        fputcsv($out, [
                            $cf->file_number,
                            $cf->plaintiffs->pluck('name')->join('; '),
                            $cf->defendants->pluck('name')->join('; '),
                            $c->outstanding_amount,
                            $cf->status,
                            $c->recovered_amount,
                            $c->outstanding_amount? round($c->recovered_amount*100/$c->outstanding_amount,2):0,
                            optional($cf->branch)->name,
                            optional($cf->court)->name.' — '.$cf->court_file_no,
                        ]);
                    }
                }, 'clean-loan-recovery.csv', $headers);
            }
            $pdf = Pdf::loadView('admin.reports.pdf.clean-loan-recovery', ['cases'=>$cases]);
            return response()->streamDownload(fn() => print($pdf->output()), 'clean-loan-recovery.pdf', ['Content-Type'=>'application/pdf']);
        } elseif ($module === 'labor') {
            $cases = \App\Models\LaborLitigationCase::with(['caseFile.branch','caseFile.court','caseFile.plaintiffs','caseFile.defendants'])->get();
            if ($format === 'excel') {
                $headers = [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="labor-litigation.csv"',
                ];
                return response()->streamDownload(function() use ($cases) {
                    $out = fopen('php://output', 'w');
                    fputcsv($out, ['File No','Plaintiff','Defendant','Claimed Thing','Status','Recovered Thing','Performance %','Branch','Court & File No.']);
                    foreach ($cases as $c) {
                        $cf = $c->caseFile;
                        $claimed = $c->claim_material_desc ?: $c->claim_amount;
                        $recovered = $c->recovered_amount ?? '';
                        $performance = ($claimed && is_numeric($claimed) && $claimed>0) ? round(($recovered??0)*100/$claimed,2) : '';
                        fputcsv($out, [
                            $cf?->file_number,
                            $cf?->plaintiffs->pluck('name')->join('; '),
                            $cf?->defendants->pluck('name')->join('; '),
                            $claimed,
                            $cf?->status,
                            $recovered,
                            $performance,
                            optional($cf?->branch)->name,
                            optional($cf?->court)->name.' — '.$cf?->court_file_no,
                        ]);
                    }
                }, 'labor-litigation.csv', $headers);
            }
            $pdf = Pdf::loadView('admin.reports.pdf.labor', ['cases'=>$cases]);
            return response()->streamDownload(fn() => print($pdf->output()), 'labor-litigation.pdf', ['Content-Type'=>'application/pdf']);
        } elseif ($module === 'criminal') {
            $cases = \App\Models\CriminalLitigationCase::with(['caseFile.branch','caseFile.court','caseFile.plaintiffs','caseFile.defendants'])->get();
            if ($format === 'excel') {
                $headers = [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="criminal-cases.csv"',
                ];
                return response()->streamDownload(function() use ($cases) {
                    $out = fopen('php://output', 'w');
                    fputcsv($out, ['File No','Police Division','Prosecutor Office','Charged Person','Claimed Thing','Status','Recovered Thing','Performance %','Branch','Court & File No.']);
                    foreach ($cases as $c) {
                        $cf = $c->caseFile;
                        $claimed = $c->claim_material_desc ?? '';
                        $recovered = $c->recovered_amount ?? '';
                        $performance = ($claimed && is_numeric($claimed) && $claimed>0) ? round(($recovered??0)*100/$claimed,2) : '';
                        fputcsv($out, [
                            $cf?->file_number,
                            $c->police_ref_no,
                            $c->prosecutor_ref_no,
                            $cf?->defendants->pluck('name')->join('; '),
                            $claimed,
                            $c->status,
                            $recovered,
                            $performance,
                            optional($cf?->branch)->name,
                            optional($cf?->court)->name.' — '.$cf?->court_file_no,
                        ]);
                    }
                }, 'criminal-cases.csv', $headers);
            }
            $pdf = Pdf::loadView('admin.reports.pdf.criminal', ['cases'=>$cases]);
            return response()->streamDownload(fn() => print($pdf->output()), 'criminal-cases.pdf', ['Content-Type'=>'application/pdf']);
        } elseif ($module === 'secured-loan-recovery') {
            $cases = \App\Models\SecuredLoanRecoveryCase::with(['caseFile.branch','caseFile.court','caseFile.plaintiffs'])->get();
            if ($format === 'excel') {
                $headers = [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="secured-loan-recovery.csv"',
                ];
                return response()->streamDownload(function() use ($cases) {
                    $out = fopen('php://output', 'w');
                    fputcsv($out, ['File No','Plaintiff','Defendant','Outstanding','Status','Recovered','Performance %','Branch','Court & File No.']);
                    foreach ($cases as $c) {
                        $cf = $c->caseFile;
                        fputcsv($out, [
                            $cf->file_number,
                            $cf->plaintiffs->pluck('name')->join('; '),
                            $cf->defendants->pluck('name')->join('; '),
                            $c->outstanding_amount,
                            $cf->status,
                            $c->recovered_amount,
                            $c->outstanding_amount? round($c->recovered_amount*100/$c->outstanding_amount,2):0,
                            optional($cf->branch)->name,
                            optional($cf->court)->name.' — '.$cf->court_file_no,
                        ]);
                    }
                }, 'secured-loan-recovery.csv', $headers);
            }
            $pdf = Pdf::loadView('admin.reports.pdf.secured-loan-recovery', ['cases'=>$cases]);
            return response()->streamDownload(fn() => print($pdf->output()), 'secured-loan-recovery.pdf', ['Content-Type'=>'application/pdf']);
        } elseif ($module === 'advisory') {
            $cases = \App\Models\LegalAdvisoryCase::with(['caseFile.branch'])->get();
            if ($format === 'excel') {
                $headers = [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="legal-advisory.csv"',
                ];
                return response()->streamDownload(function () use ($cases) {
                    $out = fopen('php://output', 'w');
                    fputcsv($out, ['File No','Branch/Unit & Date','Service Type','Status','Written Advice','Document Review','Drafting','Input Collection','Submitted/Sent']);
                    foreach ($cases as $c) {
                        $cf = $c->caseFile;
                        $serviceType = match($c->advisory_type) {
                            'written_advice' => 'Advice',
                            'document_review' => 'Review',
                            default => 'Drafting'
                        };
                        fputcsv($out, [
                            $cf?->file_number,
                            optional($cf?->branch)->name.' ('.$c->request_date?->format('Y-m-d').')',
                            $serviceType,
                            ucfirst($c->status),
                            $c->advisory_type==='written_advice' ? 'Y' : '',
                            $c->advisory_type==='document_review' ? 'Y' : '',
                            $serviceType==='Drafting' ? 'Y' : '',
                            $c->request_date?->format('Y-m-d'),
                            $c->submission_date?->format('Y-m-d')
                        ]);
                    }
                }, 'legal-advisory.csv', $headers);
            }
            $pdf = Pdf::loadView('admin.reports.pdf.advisory', ['cases'=>$cases]);
            return response()->streamDownload(fn() => print($pdf->output()), 'legal-advisory.pdf', ['Content-Type'=>'application/pdf']);
        } elseif ($module === 'other-civil') {
            $cases = \App\Models\OtherCivilLitigationCase::with(['caseFile.branch','caseFile.court','caseFile.plaintiffs','caseFile.defendants'])->get();
            if ($format === 'excel') {
                $headers = [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="other-civil-cases.csv"',
                ];
                return response()->streamDownload(function() use ($cases) {
                    $out = fopen('php://output', 'w');
                    fputcsv($out, ['File No','Plaintiff','Defendant','Claimed Thing','Status','Recovered Thing','Performance %','Branch','Court & File No.']);
                    foreach ($cases as $c) {
                        $cf = $c->caseFile;
                        $claimed = $c->claim_material_desc ?: ($c->claim_amount ? number_format($c->claim_amount,2) : '');
                        $recovered = $c->recovered_amount ? number_format($c->recovered_amount,2) : '';
                        $performance = ($c->claim_amount && $c->claim_amount>0) ? round(($c->recovered_amount??0)*100/$c->claim_amount,2) : '';
                        fputcsv($out, [
                            $cf?->file_number,
                            $cf?->plaintiffs->pluck('name')->join('; '),
                            $cf?->defendants->pluck('name')->join('; '),
                            $claimed,
                            $cf?->status,
                            $recovered,
                            $performance,
                            optional($cf?->branch)->name,
                            optional($cf?->court)->name.' — '.$cf?->court_file_no,
                        ]);
                    }
                }, 'other-civil-cases.csv', $headers);
            }
            $pdf = Pdf::loadView('admin.reports.pdf.other-civil', ['cases'=>$cases]);
            return response()->streamDownload(fn() => print($pdf->output()), 'other-civil-cases.pdf', ['Content-Type'=>'application/pdf']);
        } elseif ($module === 'all') {
            $query = \App\Models\CaseFile::with(['caseType', 'branch', 'court', 'plaintiffs', 'defendants', 'lawyer']);
            $request = request();
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('opened_at', [$request->start_date, $request->end_date]);
            }
            if ($request->filled('branch_id')) {
                $query->where('branch_id', $request->branch_id);
            }
            if ($request->filled('type_id')) {
                $query->where('case_type_id', $request->type_id);
            }
            $cases = $query->get();
            if ($format === 'excel') {
                $headers = [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="all-cases.csv"',
                ];
                return response()->streamDownload(function() use ($cases) {
                    $out = fopen('php://output', 'w');
                    fputcsv($out, ['File No','Type','Plaintiff(s)','Defendant(s)','Status','Branch','Court','Lawyer','Opened At']);
                    foreach ($cases as $cf) {
                        fputcsv($out, [
                            $cf->file_number,
                            optional($cf->caseType)->name,
                            $cf->plaintiffs->pluck('name')->join('; '),
                            $cf->defendants->pluck('name')->join('; '),
                            $cf->status,
                            optional($cf->branch)->name,
                            optional($cf->court)->name,
                            optional($cf->lawyer)->name,
                            $cf->opened_at?->format('Y-m-d'),
                        ]);
                    }
                }, 'all-cases.csv', $headers);
            }
            $pdf = Pdf::loadView('admin.reports.pdf.all', ['cases'=>$cases]);
            return response()->streamDownload(fn() => print($pdf->output()), 'all-cases.pdf', ['Content-Type'=>'application/pdf']);
        }
 
         // default fallback
        return response()->streamDownload(fn() => print('Export coming soon'), 'report.txt');
    }
}






