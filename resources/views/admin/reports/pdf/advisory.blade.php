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
    <title>Legal Advisory Cases Report - Legal Organization</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', 'DejaVu Sans', Arial, sans-serif;
            font-size: 14px;
            color: #222;
            margin: 12px 18px 12px 18px;
        }
        
        .document-container {
            max-width: 100%;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 10px;
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
            gap: 10px;
            gap: 12px;
            flex-shrink: 0;
            width: auto;
        }
        
        .logo {
            height: 40px;
            width: auto;
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
            margin-bottom: 3px;
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
        
        /* Metadata Section Outside Header */
        .metadata-section {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 14px 20px;
            border-radius: 10px;
            margin-bottom: 18px;
            border-left: 4px solid #3ca44c;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px;
            font-size: 10px;
        }
        
        .meta-item {
            text-align: center;
        }
        
        .meta-label {
            font-weight: 600;
            color: #1e3a2e;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            font-size: 9px;
        }
        
        .meta-value {
            color: #64748b;
            font-weight: 500;
            font-size: 11px;
        }
        
        .total-cases .meta-value {
            color: #3ca44c;
            font-weight: 700;
            font-size: 12px;
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
            font-size: 10px;
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
            color: {{ $accentBlue }};
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
                            <div class="report-title">Legal Advisory Cases Report</div>
                            <div class="report-subtitle">Comprehensive Analysis of Legal Advisory Services</div>
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
                    This comprehensive report provides detailed information about all legal advisory cases, including branch/unit assignments, service types, current status, and key milestone dates. The report is designed for advisory workload tracking, performance review, and strategic planning purposes.
                </p>
            </div>
            
            <!-- Data Table -->
            <div class="table-container">
                <div class="table-header">
                    <h3 class="table-title">Legal Advisory Cases Data</h3>
                </div>
                
                <table class="data-table">
        <thead>
            <tr>
                <th>File No</th>
                <th>Branch/Unit & Date</th>
                <th>Service Type</th>
                <th>Status</th>
                <th>Written Advice</th>
                <th>Document Review</th>
                <th>Drafting</th>
                <th>Input Collection</th>
                <th>Submitted/Sent</th>
            </tr>
        </thead>
        <tbody>
        @foreach($cases as $c)
            @php $cf = $c->caseFile; @endphp
            <tr>
                <td><span class="file-number">{{ $cf?->file_number ?: 'N/A' }}</span></td>
                <td>
                    <div class="branch-info">{{ optional($cf?->branch)->name ?: 'N/A' }}</div>
                    <div class="date-info">{{ $c->request_date?->format('M d, Y') ?: 'N/A' }}</div>
                </td>
                <td>
                    @if($c->advisory_type === 'written_advice') 
                        <span class="status-badge status-completed">Written Advice</span>
                    @elseif($c->advisory_type === 'document_review') 
                        <span class="status-badge status-in-progress">Document Review</span>
                    @else 
                        <span class="status-badge status-pending">Legal Drafting</span>
                    @endif
                </td>
                <td>
                    @if(strtolower($c->status) === 'completed')
                        <span class="status-badge status-completed">{{ ucfirst($c->status) }}</span>
                    @elseif(strtolower($c->status) === 'pending')
                        <span class="status-badge status-pending">{{ ucfirst($c->status) }}</span>
                    @else
                        <span class="status-badge status-in-progress">{{ ucfirst($c->status) }}</span>
                    @endif
                </td>
                <td class="text-center">@if($c->advisory_type==='written_advice') ✓ @else — @endif</td>
                <td class="text-center">@if($c->advisory_type==='document_review') ✓ @else — @endif</td>
                <td class="text-center">@if($c->advisory_type==='drafting') ✓ @else — @endif</td>
                <td class="date-info">{{ $c->request_date?->format('M d, Y') ?: 'N/A' }}</td>
                <td class="date-info">{{ $c->submission_date?->format('M d, Y') ?: 'Pending' }}</td>
            </tr>
            @if($c->description)
            <tr class="description-row">
                <td class="font-bold">Description:</td>
                <td colspan="8" class="description-cell">{{ $c->description }}</td>
            </tr>
            @endif
            @if($c->actionLogs()->where('action', 'edit')->exists())
            <tr class="description-row">
                <td class="font-bold">Edit History:</td>
                <td colspan="8" class="edit-history">
                    <ul>
                        @foreach($c->actionLogs()->where('action', 'edit')->with('user')->latest()->limit(3)->get() as $log)
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





