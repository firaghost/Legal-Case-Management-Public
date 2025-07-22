@php
    $logo = public_path('images/company-logo.png');
    $user = auth()->user();
    $brandGreen = '#3ca44c'; // Legal Organization green
    $brandDark = '#1e3a2e';
    $accentBlue = '#2563eb';
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Other Civil Litigation Cases Report - Legal Organization</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #222;
            margin: 12px 18px 12px 18px;
            background: #ffffff;
        }
        
        .header-container {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        }
        
        .header-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        
        .header-table td {
            vertical-align: top;
            padding: 0;
        }
        
        .header-left-cell {
            width: 22%;
            vertical-align: middle;
            padding-right: 8px;
        }
        
        .header-right-cell {
            width: 78%;
            vertical-align: middle;
            padding-left: 8px;
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
            width: auto;
        }
        
        .logo {
            width: 36px;
            height: 36px;
            margin-right: 8px;
            border-radius: 6px;
        }
        
        .company-info h1 {
            font-size: 13px;
            font-weight: 700;
            color: #1e3a2e;
            margin-bottom: 1px;
            letter-spacing: 0.2px;
        }
        
        .company-info p {
            font-size: 9px;
            color: #64748b;
            font-weight: 500;
        }
        
        .header-right {
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: right;
            width: 100%;
        }
        
        .report-title {
            font-size: 17px;
            font-weight: 700;
            color: #1e3a2e;
            margin-bottom: 2px;
            line-height: 1.1;
            word-wrap: break-word;
            hyphens: auto;
        }
        
        .report-subtitle {
            font-size: 11px;
            color: #64748b;
            font-style: italic;
            line-height: 1.1;
            margin-top: 1px;
        }
        
        /* Professional Metadata Section */
        .metadata-section {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 24px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            position: relative;
        }
        
        .metadata-section::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, #3ca44c 0%, #2d7a3d 100%);
            border-radius: 12px 0 0 12px;
        }
        
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 24px;
            font-size: 11px;
            align-items: center;
        }
        
        .meta-item {
            text-align: center;
            padding: 8px 12px;
            background: rgba(255,255,255,0.7);
            border-radius: 8px;
            border: 1px solid rgba(226,232,240,0.5);
            transition: all 0.2s ease;
        }
        
        .meta-item:hover {
            background: rgba(255,255,255,0.9);
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .meta-label {
            font-weight: 700;
            color: #1e3a2e;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 6px;
            font-size: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }
        
        .meta-label::before {
            content: '●';
            color: #3ca44c;
            font-size: 8px;
        }
        
        .meta-value {
            color: #475569;
            font-weight: 600;
            font-size: 12px;
            line-height: 1.3;
        }
        
        .total-cases {
            background: linear-gradient(135deg, #3ca44c 0%, #2d7a3d 100%);
            color: white;
            border: 1px solid #3ca44c;
        }
        
        .total-cases .meta-label {
            color: rgba(255,255,255,0.9);
        }
        
        .total-cases .meta-label::before {
            color: rgba(255,255,255,0.8);
        }
        
        .total-cases .meta-value {
            color: white;
            font-weight: 700;
            font-size: 14px;
        }
        
        .total-cases:hover {
            background: linear-gradient(135deg, #2d7a3d 0%, #3ca44c 100%);
        }
        
        .report-title {
            background: linear-gradient(135deg, #3ca44c 0%, #1e3a2e 100%);
            color: white;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
        }
        
        .report-title h2 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        
        .report-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 9px;
            opacity: 0.9;
        }
        
        .description-section {
            background: #f8fafc;
            padding: 12px 16px;
            border-radius: 6px;
            border-left: 4px solid #3ca44c;
            margin-bottom: 16px;
        }
        
        .description-section h3 {
            font-size: 11px;
            font-weight: 600;
            color: #1e3a2e;
            margin-bottom: 6px;
        }
        
        .description-section p {
            font-size: 10px;
            color: #64748b;
            line-height: 1.4;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 16px;
            background: white;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .data-table th {
            background: linear-gradient(135deg, #1e3a2e 0%, #3ca44c 100%);
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-weight: 600;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #0f172a;
        }
        
        .data-table td {
            padding: 6px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
            font-size: 9px;
        }
        
        .data-table tr:nth-child(even) {
            background: #f8fafc;
        }
        
        .data-table tr:hover {
            background: #f1f5f9;
        }
        
        .file-number {
            font-weight: 600;
            color: #1e3a2e;
        }
        
        .amount-info {
            font-weight: 500;
            color: #059669;
        }
        
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .status-completed {
            background: #dcfce7;
            color: #166534;
        }
        
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-in-progress {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .performance-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: 500;
        }
        
        .performance-high {
            background: #dcfce7;
            color: #166534;
        }
        
        .performance-medium {
            background: #fef3c7;
            color: #92400e;
        }
        
        .performance-low {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .branch-info {
            font-weight: 500;
            color: #4338ca;
        }
        
        .footer-section {
            margin-top: 20px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 8px;
            color: #64748b;
        }
        
        .confidential {
            font-weight: 500;
        }
        
        .page-number {
            font-weight: 500;
        }
        
        .metadata-section {
            margin-bottom: 16px;
        }
        
        .metadata-section .meta-grid {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 9px;
            opacity: 0.9;
        }
        
        .metadata-section .meta-item {
            margin-right: 16px;
        }
        
        .metadata-section .meta-label {
            font-weight: 600;
            color: #1e3a2e;
            margin-bottom: 4px;
        }
        
        .metadata-section .meta-value {
            color: #475569;
        }
    </style>
</head>
<body>
        <!-- Clean Boxed Header Section -->
        <div class="header-container">
            <table class="header-table">
                <tr>
                    <td class="header-left-cell">
                        <div class="header-left">
                            <img src="{{ $logo }}" class="logo" alt="Legal Organization Logo">
                            <div class="company-info">
                                <h1>Legal Organization</h1>
                                <p>Legal Department</p>
                            </div>
                        </div>
                    </td>
                    <td class="header-right-cell">
                        <div class="header-right">
                            <div class="report-title">Other Civil Cases Report</div>
                            <div class="report-subtitle">Comprehensive Other Civil Case Management Analysis</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Report Metadata Section -->
        <div class="metadata-section">
            <div class="meta-grid">
                <div class="meta-item">
                    <div class="meta-label">Generated By</div>
                    <div class="meta-value">{{ $user ? $user->name : 'System' }}</div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">Generated On</div>
                    <div class="meta-value">{{ now()->format('M d, Y \\a\\t H:i') }}</div>
                </div>
                <div class="meta-item total-cases">
                    <div class="meta-label">Total Cases</div>
                    <div class="meta-value">{{ count($cases) }}</div>
                </div>
            </div>
        </div>

    <!-- Description Section -->
    <div class="description-section">
        <h3>Report Overview</h3>
        <p>This comprehensive report provides detailed information on all other civil litigation cases handled by Legal Organization Legal Department. It includes plaintiff and defendant information, claimed and recovered amounts, case status, and associated branch and court details. This report is essential for compliance monitoring, civil case management, and legal performance review across various civil litigation matters.</p>
    </div>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th>File No</th>
                <th>Plaintiff(s)</th>
                <th>Defendant(s)</th>
                <th>Claimed Amount (ETB)</th>
                <th>Status</th>
                <th>Recovered (ETB)</th>
                <th>Performance %</th>
                <th>Branch</th>
                <th>Court</th>
            </tr>
        </thead>
        <tbody>
        @foreach($cases as $c)
            @php 
                $cf = $c->caseFile; 
                $claimed = $c->claim_amount ?? 0;
                $recovered = $c->recovered_amount ?? 0;
                $performance = ($claimed > 0) ? round($recovered * 100 / $claimed, 2) : 0;
            @endphp
            <tr>
                <td><span class="file-number">{{ $cf->file_number }}</span></td>
                <td>{{ $cf->plaintiffs->pluck('name')->join('; ') ?: 'N/A' }}</td>
                <td>{{ $cf->defendants->pluck('name')->join('; ') ?: 'N/A' }}</td>
                <td>
                    @if($claimed > 0)
                        <span class="amount-info">{{ number_format($claimed, 2) }}</span>
                    @else
                        <span>{{ $c->claim_material_desc ?: 'N/A' }}</span>
                    @endif
                </td>
                <td>
                    @if(strtolower($cf->status) === 'completed')
                        <span class="status-badge status-completed">{{ ucfirst($cf->status) }}</span>
                    @elseif(strtolower($cf->status) === 'pending')
                        <span class="status-badge status-pending">{{ ucfirst($cf->status) }}</span>
                    @else
                        <span class="status-badge status-in-progress">{{ ucfirst($cf->status) }}</span>
                    @endif
                </td>
                <td>
                    @if($recovered > 0)
                        <span class="amount-info">{{ number_format($recovered, 2) }}</span>
                    @else
                        <span>N/A</span>
                    @endif
                </td>
                <td>
                    @if($performance >= 80)
                        <span class="performance-badge performance-high">{{ $performance }}%</span>
                    @elseif($performance >= 50)
                        <span class="performance-badge performance-medium">{{ $performance }}%</span>
                    @elseif($performance > 0)
                        <span class="performance-badge performance-low">{{ $performance }}%</span>
                    @else
                        <span>N/A</span>
                    @endif
                </td>
                <td><span class="branch-info">{{ optional($cf->branch)->name ?: 'N/A' }}</span></td>
                <td>{{ optional($cf->court)->name ?: 'N/A' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <!-- Footer Section -->
    <div class="footer-section">
        <span class="confidential">Confidential – For internal use only</span>
        <span class="page-number">Page {PAGE_NUM}</span>
    </div>
</body>
</html> 





