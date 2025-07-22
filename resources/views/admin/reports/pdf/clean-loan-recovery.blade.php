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
    <title>Clean Loan Recovery Cases Report - Legal Organization</title>
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
        
        .header-content {
            display: flex;
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
            text-align: right;
            font-size: 7px;
            opacity: 0.95;
            line-height: 1.3;
            min-width: 200px;
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
        
        .amount-info {
            font-weight: 600;
            color: #059669;
        }
        
        .performance-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 8px;
            font-weight: 600;
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
            color: #dc2626;
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
        <!-- Modern Header Section -->
        <div class="header-section">
            <div class="header-content">
                <div class="header-left">
                    <img src="{{ $logo }}" class="logo" alt="Legal Organization Logo">
                    <div class="company-info">
                        <div class="company-name">Legal Organization</div>
                        <div class="company-tagline">Excellence in ORGANIZATIONing Services</div>
                    </div>
                </div>
                <div class="header-right">
                    <div class="contact-item"><strong>Address:</strong> Addis Ababa, Sera Leone Avenue</div>
                    <div class="contact-item">Lancha Mekor Plaza 3rd Floor</div>
                    <div class="contact-item"><strong>P.O.Box:</strong> 1157</div>
                    <div class="contact-item"><strong>Phone:</strong> +1-555-0100</div>
                    <div class="contact-item"><strong>Email:</strong> info@legalorg.example.com</div>
                    <div class="contact-item"><strong>Website:</strong> www.legalorg.example.com</div>
                </div>
            </div>
        </div>
        
        <!-- Report Title Section -->
        <div class="title-section">
            <h1 class="report-title">Clean Loan Recovery Cases Report</h1>
            <p class="report-subtitle">Financial Recovery Performance Analysis</p>
            
            <div class="meta-info">
                <div class="meta-item">
                    <div class="meta-label">Generated By</div>
                    <div class="meta-value">{{ $user ? $user->name : 'System Administrator' }}</div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">Generated On</div>
                    <div class="meta-value">{{ now()->format('M d, Y \a\t H:i') }}</div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">Total Cases</div>
                    <div class="meta-value">{{ count($cases) }}</div>
                </div>
            </div>
        </div>
        
        <!-- Content Section -->
        <div class="content-section">
            <div class="description">
                <p class="description-text">
                    This comprehensive report lists all clean loan recovery cases, including parties involved, outstanding and recovered amounts, current status, and related branch/court information. The report is designed for financial tracking and recovery performance analysis to support strategic decision-making.
                </p>
            </div>
            
            <!-- Data Table -->
            <div class="table-container">
                <div class="table-header">
                    <h3 class="table-title">Clean Loan Recovery Cases Data</h3>
                </div>
                
                <table class="data-table">
        <thead>
            <tr>
                <th>File No</th>
                <th>Plaintiff(s)</th>
                <th>Defendant(s)</th>
                <th>Outstanding (ETB)</th>
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
                $performance = $c->outstanding_amount ? round($c->recovered_amount*100/$c->outstanding_amount,2) : 0;
            @endphp
            <tr>
                <td><span class="file-number">{{ $cf->file_number }}</span></td>
                <td>{{ $cf->plaintiffs->pluck('name')->join('; ') }}</td>
                <td>{{ $cf->defendants->pluck('name')->join('; ') }}</td>
                <td><span class="amount-info">{{ number_format($c->outstanding_amount,2) }}</span></td>
                <td>
                    @if(strtolower($cf->status) === 'completed')
                        <span class="status-badge status-completed">{{ ucfirst($cf->status) }}</span>
                    @elseif(strtolower($cf->status) === 'pending')
                        <span class="status-badge status-pending">{{ ucfirst($cf->status) }}</span>
                    @else
                        <span class="status-badge status-in-progress">{{ ucfirst($cf->status) }}</span>
                    @endif
                </td>
                <td><span class="amount-info">{{ number_format($c->recovered_amount,2) }}</span></td>
                <td>
                    @if($performance >= 80)
                        <span class="performance-badge performance-high">{{ $performance }}%</span>
                    @elseif($performance >= 50)
                        <span class="performance-badge performance-medium">{{ $performance }}%</span>
                    @else
                        <span class="performance-badge performance-low">{{ $performance }}%</span>
                    @endif
                </td>
                <td><span class="branch-info">{{ optional($cf->branch)->name }}</span></td>
                <td>{{ optional($cf->court)->name }}</td>
            </tr>
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






