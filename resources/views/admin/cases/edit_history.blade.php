@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Edit History for Case: {{ $case->title }}</h1>
        <a href="{{ route('admin.cases.show', $case) }}" class="text-blue-600 hover:underline">&larr; Back to Case</a>
    </div>
    <div class="bg-white shadow rounded-lg p-6">
        @forelse($editLogs as $log)
            <div class="mb-8 border-b pb-4">
                <div class="flex justify-between items-center mb-2">
                    <div>
                        <span class="font-semibold text-gray-800">{{ $log->user->name ?? 'Unknown User' }}</span>
                        <span class="text-xs text-gray-500 ml-2">{{ $log->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <span class="text-xs bg-gray-200 text-gray-700 px-2 py-1 rounded">Edit</span>
                </div>
                <div class="mb-2 text-gray-700">{{ $log->description }}</div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm border">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 border-b text-left">Field</th>
                                <th class="px-3 py-2 border-b text-left">Old Value</th>
                                <th class="px-3 py-2 border-b text-left">New Value</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach(($log->old_properties ?? []) as $field => $oldValue)
                            <tr>
                                <td class="px-3 py-2 border-b font-medium">{{ $field }}</td>
                                <td class="px-3 py-2 border-b text-red-700">{{ is_array($oldValue) ? json_encode($oldValue) : $oldValue }}</td>
                                <td class="px-3 py-2 border-b text-green-700">{{ is_array($log->properties[$field] ?? null) ? json_encode($log->properties[$field] ?? null) : ($log->properties[$field] ?? '') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="text-gray-500">No edit history found for this case.</div>
        @endforelse
    </div>
</div>
@endsection 





