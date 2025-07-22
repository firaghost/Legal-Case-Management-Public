<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use App\Models\CaseType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    protected $periods = [
        'monthly' => 1,
        'quarterly' => 3,
        'semi_annual' => 6,
        'nine_months' => 9,
        'annual' => 12,
    ];

    public function generateReport(Request $request, string $period)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'case_type_id' => 'nullable|exists:case_types,id',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        if (!array_key_exists($period, $this->periods)) {
            return response()->json(['error' => 'Invalid report period'], 400);
        }

        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = $request->has('end_date') 
            ? Carbon::parse($request->input('end_date'))
            : $startDate->copy()->addMonths($this->periods[$period])->subDay();

        $query = CaseFile::with([
            'caseType',
            'court',
            'plaintiffs',
            'defendants',
            'litigation',
            'laborLitigation',
            'otherCivilLitigation',
            'criminalLitigation',
            'securedLoanRecovery',
            'branch',
            'workUnit',
            'progressUpdates' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->orderBy('created_at', 'desc');
            }
        ])
        ->whereBetween('opened_at', [$startDate, $endDate])
        ->when($request->has('case_type_id'), function($q) use ($request) {
            $q->where('case_type_id', $request->input('case_type_id'));
        })
        ->when($request->has('branch_id'), function($q) use ($request) {
            $q->where('branch_id', $request->input('branch_id'));
        });

        $cases = $query->get()->map(function($case) use ($startDate, $endDate) {
            return $this->formatCaseForReport($case, $startDate, $endDate);
        });

        $summary = $this->generateSummary($cases);

        return response()->json([
            'meta' => [
                'period' => $period,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'total_cases' => $cases->count(),
                'generated_at' => now()->toDateTimeString(),
            ],
            'summary' => $summary,
            'cases' => $cases,
        ]);
    }

    protected function formatCaseForReport($case, $startDate, $endDate)
    {
        $caseData = [
            'id' => $case->id,
            'file_number' => $case->file_number,
            'case_type' => $case->caseType->name ?? 'N/A',
            'court_name' => $case->court->name ?? 'N/A',
            'branch_name' => $case->branch->name ?? 'N/A',
            'work_unit' => $case->workUnit->name ?? 'N/A',
            'plaintiffs' => $case->plaintiffs->pluck('name')->implode(', '),
            'defendants' => $case->defendants->pluck('name')->implode(', '),
            'opened_at' => $case->opened_at->toDateString(),
            'status' => $case->status,
            'status_updates' => $case->progressUpdates->map(function($update) {
                return [
                    'date' => $update->created_at->toDateString(),
                    'status' => $update->status,
                    'notes' => $update->notes,
                ];
            }),
        ];

        // Add financial data based on case type
        $caseData = array_merge($caseData, $this->getFinancialData($case));

        return $caseData;
    }

    protected function getFinancialData($case)
    {
        $financials = [
            'claimed_amount' => 0,
            'recovered_amount' => 0,
            'outstanding_amount' => 0,
            'performance_percentage' => 0,
        ];

        // Handle different case types
        if ($case->litigation) {
            $financials['claimed_amount'] = $case->litigation->claim_amount ?? 0;
            $financials['recovered_amount'] = $case->litigation->recovered_amount ?? 0;
        } elseif ($case->laborLitigation) {
            $financials['claimed_amount'] = $case->laborLitigation->claim_amount ?? 0;
            $financials['recovered_amount'] = $case->laborLitigation->recovered_amount ?? 0;
        } elseif ($case->otherCivilLitigation) {
            $financials['claimed_amount'] = $case->otherCivilLitigation->claim_amount ?? 0;
            $financials['recovered_amount'] = $case->otherCivilLitigation->recovered_amount ?? 0;
        } elseif ($case->securedLoanRecovery) {
            $financials['claimed_amount'] = $case->securedLoanRecovery->loan_amount ?? 0;
            $financials['recovered_amount'] = $case->securedLoanRecovery->recovered_amount ?? 0;
        }

        $financials['outstanding_amount'] = $financials['claimed_amount'] - $financials['recovered_amount'];
        $financials['performance_percentage'] = $financials['claimed_amount'] > 0 
            ? round(($financials['recovered_amount'] / $financials['claimed_amount']) * 100, 2)
            : 0;

        return $financials;
    }

    protected function generateSummary($cases)
    {
        $totalClaimed = $cases->sum('claimed_amount');
        $totalRecovered = $cases->sum('recovered_amount');
        $totalOutstanding = $cases->sum('outstanding_amount');
        $performancePercentage = $totalClaimed > 0 
            ? round(($totalRecovered / $totalClaimed) * 100, 2) 
            : 0;

        $statusCounts = $cases->groupBy('status')->map->count();
        $caseTypeCounts = $cases->groupBy('case_type')->map->count();

        return [
            'total_cases' => $cases->count(),
            'total_claimed' => $totalClaimed,
            'total_recovered' => $totalRecovered,
            'total_outstanding' => $totalOutstanding,
            'performance_percentage' => $performancePercentage,
            'status_summary' => $statusCounts,
            'case_type_summary' => $caseTypeCounts,
        ];
    }

    public function exportReport(Request $request, string $format, string $period)
    {
        // Force export flag so generateReport fetches full dataset without pagination limits
        $request->merge(['export' => true]);
        $response = $this->generateReport($request, $period);

        if ($response->status() !== 200) {
            return $response;
        }

        $data = $response->getData(true);
        $filename = "report_{$period}_" . now()->format('Y-m-d_His');
        $format = strtolower($format);

        switch ($format) {
            case 'csv':
            case 'excel': // Excel can open CSV files seamlessly
                $filename .= '.csv';
                $headers = [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                ];

                $callback = function () use ($data) {
                    $handle = fopen('php://output', 'w');
                    // Write header row if cases exist
                    if (!empty($data['cases'])) {
                        fputcsv($handle, array_keys($data['cases'][0]));
                        foreach ($data['cases'] as $row) {
                            // Ensure any nested arrays/objects are stringified
                            array_walk($row, function (&$value) {
                                if (is_array($value) || is_object($value)) {
                                    $value = json_encode($value);
                                }
                            });
                            fputcsv($handle, $row);
                        }
                    }
                    fclose($handle);
                };

                return response()->stream($callback, 200, $headers);

            case 'pdf':
                if (!class_exists(Pdf::class)) {
                    return response()->json([
                        'error' => 'PDF export requires barryvdh/laravel-dompdf. Please run "composer require barryvdh/laravel-dompdf".'
                    ], 500);
                }
                // You will need a Blade view resources/views/reports/pdf.blade.php
                // providing a presentable layout. For now, render a simple HTML.
                $html = view('reports.pdf', ['report' => $data])->render();
                $pdf = Pdf::loadHTML($html);
                return $pdf->download("{$filename}.pdf");

            default:
                return response()->json(['error' => 'Unsupported export format'], 400);
        }
    }
}






