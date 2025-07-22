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
    <title>All Cases Report - Legal Organization</title>
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
            margin: 10px 15px 10px 15px;
        }
        
        .document-container {
            max-width: 100%;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
        }
        
        /* Boxed Header Design */
        .header-container {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 12px;
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
            width: 25%;
            vertical-align: middle;
            padding-right: 10px;
        }
        
        .header-right-cell {
            width: 75%;
            vertical-align: middle;
            padding-left: 10px;
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
            width: auto;
        }
        
        .logo {
            height: 40px;
            width: auto;
        }
        
        .company-info h1 {
            font-size: 12px;
            font-weight: 700;
            color: #1e3a2e;
            margin-bottom: 1px;
            letter-spacing: 0.2px;
        }
        
        .company-info p {
            font-size: 8px;
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
            font-size: 18px;
            font-weight: 700;
            color: #1e3a2e;
            margin-bottom: 3px;
            line-height: 1.0;
            word-wrap: break-word;
            hyphens: auto;
        }
        
        .report-subtitle {
            font-size: 10px;
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
        
        .contact-item {
            margin-bottom: 1px;
        }
        
        /* Report Title Section */
        .title-section {
            background: linear-gradient(to right, #f8fafc, #e2e8f0);
            padding: 12px 20px;
            text-align: center;
            border-bottom: 3px solid {{ $brandGreen }};
        }
        
        .report-title {
            font-size: 20px;
            font-weight: 700;
            color: {{ $brandDark }};
            margin-bottom: 6px;
            letter-spacing: -0.3px;
        }
        
        .report-subtitle {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 12px;
            font-weight: 500;
        }
        
        .meta-info {
            display: flex;
            justify-content: center;
            gap: 24px;
            margin-top: 8px;
        }
        
        .meta-item {
            text-align: center;
        }
        
        .meta-label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        
        .meta-value {
            font-size: 11px;
            color: {{ $brandDark }};
            font-weight: 600;
        }
        
        /* Content Section */
        .content-section {
            padding: 32px;
        }
        
        .description {
            background: #f1f5f9;
            border-left: 4px solid {{ $accentBlue }};
            padding: 16px 20px;
            margin-bottom: 24px;
            border-radius: 0 8px 8px 0;
        }
        
        .description-text {
            font-size: 12px;
            color: #475569;
            line-height: 1.6;
            margin: 0;
        }
        
        /* Modern Table Design */
        .table-container {
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }
        
        .table-header {
            background: linear-gradient(135deg, {{ $brandGreen }}, {{ $brandDark }});
            color: white;
            padding: 16px 20px;
        }
        
        .table-title {
            font-size: 14px;
            font-weight: 600;
            margin: 0;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th {
            background: #f8fafc;
            color: {{ $brandDark }};
            font-weight: 600;
            padding: 12px 8px;
            text-align: left;
            border-bottom: 2px solid #e2e8f0;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .data-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
            font-size: 10px;
            line-height: 1.4;
        }
        
        .data-table tr:nth-child(even) {
            background: #fafbfc;
        }
        
        .data-table tr:hover {
            background: #f0f9ff;
        }
        
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: 600;
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
        
        .file-number {
            font-weight: 600;
            color: {{ $brandDark }};
        }
        
        .branch-info {
            font-weight: 500;
            color: {{ $brandDark }};
        }
        
        .date-info {
            font-size: 9px;
            color: #64748b;
        }
        
        .description-row {
            background: #f8fafc !important;
        }
        
        .description-cell {
            font-style: italic;
            color: #475569;
            padding: 8px !important;
        }
        
        .edit-history {
            font-size: 9px;
            color: #64748b;
        }
        
        .edit-history ul {
            margin: 4px 0;
            padding-left: 12px;
        }
        
        .edit-history li {
            margin-bottom: 2px;
        }
        
        /* Footer */
        .footer-section {
            background: #f8fafc;
            padding: 20px 32px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .footer-left {
            font-size: 9px;
            color: #64748b;
        }
        
        .footer-right {
            font-size: 9px;
            color: #64748b;
            font-weight: 500;
        }
        
        .confidential {
            color: #dc2626;
            font-weight: 600;
        }
        
        /* Utility Classes */
        .text-center { text-align: center; }
        .font-bold { font-weight: 700; }
        .text-sm { font-size: 10px; }
        .text-xs { font-size: 9px; }
        
        @media print {
            body { margin: 0; padding: 0; }
            .document-container { border-radius: 0; }
        }
    </style>
</head>
<body>
    <div class="document-container">
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
                            <div class="report-title">All Cases Report</div>
                            <div class="report-subtitle">Comprehensive Legal Case Management Overview</div>
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
        
        <!-- Content Section -->
        <div class="content-section">
            <div class="description">
                <p class="description-text">
                    This comprehensive report provides detailed information about all legal cases managed by Legal Organization, including case types, parties involved, current status, assigned branches, courts, and responsible lawyers. The report is designed for internal management, audit, and compliance purposes.
                </p>
            </div>
            
            <!-- Data Table -->
            <div class="table-container">
                <div class="table-header">
                    <h3 class="table-title">Legal Cases Overview</h3>
                </div>
                
                <table class="data-table">
        <thead>
            <tr>
                <th>File No</th>
                <th>Type</th>
                <th>Plaintiff(s)</th>
                <th>Defendant(s)</th>
                <th>Status</th>
                <th>Branch</th>
                <th>Court</th>
                <th>Lawyer</th>
                <th>Opened At</th>
            </tr>
        </thead>
        <tbody>
        @foreach($cases as $cf)
            <tr>
                <td><span class="file-number">{{ $cf->file_number }}</span></td>
                <td><span class="branch-info">{{ optional($cf->caseType)->name }}</span></td>
                <td>{{ $cf->plaintiffs->pluck('name')->join('; ') }}</td>
                <td>{{ $cf->defendants->pluck('name')->join('; ') }}</td>
                <td>
                    @if(strtolower($cf->status) === 'completed')
                        <span class="status-badge status-completed">{{ ucfirst($cf->status) }}</span>
                    @elseif(strtolower($cf->status) === 'pending')
                        <span class="status-badge status-pending">{{ ucfirst($cf->status) }}</span>
                    @else
                        <span class="status-badge status-in-progress">{{ ucfirst($cf->status) }}</span>
                    @endif
                </td>
                <td><span class="branch-info">{{ optional($cf->branch)->name }}</span></td>
                <td>{{ optional($cf->court)->name }}</td>
                <td>{{ optional($cf->lawyer)->name }}</td>
                <td class="date-info">{{ $cf->opened_at?->format('M d, Y') ?: 'N/A' }}</td>
            </tr>
            @if($cf->actionLogs()->where('action', 'edit')->exists())
            <tr class="description-row">
                <td class="font-bold">Edit History:</td>
                <td colspan="8" class="edit-history">
                    <ul>
                        @foreach($cf->actionLogs()->where('action', 'edit')->with('user')->latest()->limit(3)->get() as $log)
                            <li>
                                <strong>{{ $log->user->name ?? 'Unknown User' }}</strong> 
                                <span class="date-info">({{ $log->created_at->format('M d, Y H:i') }})</span>: 
                                {{ $log->description }}
                            </li>
                        @endforeach
                    </ul>
                </td>
            </tr>
            @endif
        @endforeach
        </tbody>
                </table>
            </div>
        </div>
        
        <!-- Modern Footer Section -->
        <div class="footer-section">
            <div class="footer-left">
                <span class="confidential">CONFIDENTIAL</span> - For internal use only<br>
                Generated on {{ now()->format('F j, Y \a\t g:i A') }}
            </div>
            <div class="footer-right">
                Legal Organization Legal Department<br>
                Page {PAGE_NUM}
            </div>
        </div>
    </div>
</body>
</html> 





