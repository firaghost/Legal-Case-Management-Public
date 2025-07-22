<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Audit Logs Report - {{ now()->format('Y-m-d') }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .filters {
            margin: 10px 0;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 4px;
        }
        .filters p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
            color: #777;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-create { background-color: #d4edda; color: #155724; }
        .badge-update { background-color: #cce5ff; color: #004085; }
        .badge-delete { background-color: #f8d7da; color: #721c24; }
        .badge-login { background-color: #e2e3e5; color: #383d41; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Audit Logs Report</h1>
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    @if(count($filters) > 0)
    <div class="filters">
        <h3>Applied Filters:</h3>
        @if(isset($filters['start_date']) || isset($filters['end_date']))
        <p><strong>Date Range:</strong> 
            {{ $filters['start_date'] ?? 'Start' }} to {{ $filters['end_date'] ?? 'End' }}
        </p>
        @endif
        @if(isset($filters['user_id']))
        <p><strong>User ID:</strong> {{ $filters['user_id'] }}</p>
        @endif
        @if(isset($filters['action']))
        <p><strong>Action:</strong> {{ $actionTypes[$filters['action']] ?? $filters['action'] }}</p>
        @endif
        @if(isset($filters['auditable_type']))
        <p><strong>Model Type:</strong> {{ class_basename($filters['auditable_type']) }}</p>
        @endif
        @if(isset($filters['search']))
        <p><strong>Search Term:</strong> {{ $filters['search'] }}</p>
        @endif
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Date/Time</th>
                <th>User</th>
                <th>Action</th>
                <th>Model</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                <td>{{ optional($log->user)->name ?? 'System' }}</td>
                <td>
                    @php
                        $actionClass = 'badge ';
                        if (Str::startsWith($log->action, 'CREATE')) {
                            $actionClass .= 'badge-create';
                        } elseif (Str::startsWith($log->action, 'UPDATE')) {
                            $actionClass .= 'badge-update';
                        } elseif (Str::startsWith($log->action, 'DELETE')) {
                            $actionClass .= 'badge-delete';
                        } else {
                            $actionClass .= 'badge-login';
                        }
                        $actionLabel = $actionTypes[collect(explode('_', $log->action))->first() . '_'] ?? $log->action;
                    @endphp
                    <span class="{{ $actionClass }}">{{ $actionLabel }}</span>
                </td>
                <td>
                    @if($log->auditable_type)
                        {{ class_basename($log->auditable_type) }}
                        @if($log->auditable_id)
                            #{{ $log->auditable_id }}
                        @endif
                    @else
                        System
                    @endif
                </td>
                <td>
                    @if(is_array($log->changes))
                        @foreach($log->changes as $field => $value)
                            <strong>{{ $field }}:</strong> 
                            @if(is_array($value))
                                {{ json_encode($value) }}
                            @else
                                {{ Str::limit($value, 50) }}
                            @endif
                            <br>
                        @endforeach
                    @else
                        {{ Str::limit($log->changes, 100) }}
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center;">No logs found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Page {{ $loop->iteration ?? 1 }} - Generated by {{ config('app.name') }}</p>
    </div>
</body>
</html>






