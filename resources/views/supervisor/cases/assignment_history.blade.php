<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-800 dark:to-purple-800 rounded-3xl shadow-2xl mb-8">
                <div class="px-8 py-6 text-white">
                    <h1 class="text-3xl font-bold mb-2">Case Assignment History</h1>
                    <p class="text-blue-100">Monitor and track all case reassignments across the system</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-3xl mb-8 p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Case Search</label>
                        <input type="text" name="case_search" value="{{ request('case_search') }}" 
                               placeholder="Search by case number..."
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Supervisor</label>
                        <select name="supervisor" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                            <option value="">All Supervisors</option>
                            @foreach($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}" {{ request('supervisor') == $supervisor->id ? 'selected' : '' }}>
                                    {{ $supervisor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From Date</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">To Date</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                            Filter
                        </button>
                        <a href="{{ route('supervisor.cases.assignment-history') }}" 
                           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                            Clear
                        </a>
                        <a href="?export=1{{ request()->getQueryString() ? '&' . request()->getQueryString() : '' }}" 
                           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
                            Export CSV
                        </a>
                    </div>
                </form>
            </div>

            <!-- Assignment History Table -->
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-3xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                        Assignment History ({{ is_object($assignmentLogs) && method_exists($assignmentLogs, 'total') ? $assignmentLogs->total() : $assignmentLogs->count() }} records)
                    </h2>
                </div>

                @if($assignmentLogs->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Date/Time
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Case
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Supervisor
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Reassignment
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Reason
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($assignmentLogs as $log)
                                    @php
                                        // Get old and new lawyer IDs from the stored values
                                        $oldLawyerId = null;
                                        $newLawyerId = null;
                                        
                                        // Handle different storage formats
                                        if (is_array($log->old_values)) {
                                            $oldLawyerId = $log->old_values['lawyer_id'] ?? null;
                                        } elseif (is_string($log->old_values)) {
                                            $oldValues = json_decode($log->old_values, true);
                                            $oldLawyerId = $oldValues['lawyer_id'] ?? null;
                                        }
                                        
                                        if (is_array($log->new_values)) {
                                            $newLawyerId = $log->new_values['lawyer_id'] ?? null;
                                        } elseif (is_string($log->new_values)) {
                                            $newValues = json_decode($log->new_values, true);
                                            $newLawyerId = $newValues['lawyer_id'] ?? null;
                                        }
                                        
                                        // Get lawyer names with caching to avoid N+1 queries
                                        $oldLawyerName = 'Unassigned';
                                        $newLawyerName = 'Unknown';
                                        
                                        if ($oldLawyerId) {
                                            $oldLawyer = \App\Models\User::find($oldLawyerId);
                                            $oldLawyerName = $oldLawyer ? $oldLawyer->name : 'User #' . $oldLawyerId;
                                        }
                                        
                                        if ($newLawyerId) {
                                            $newLawyer = \App\Models\User::find($newLawyerId);
                                            $newLawyerName = $newLawyer ? $newLawyer->name : 'User #' . $newLawyerId;
                                        }
                                        
                                        // Extract reason from description
                                        $reason = '';
                                        if (str_contains($log->description ?? '', ' - Reason: ')) {
                                            $parts = explode(' - Reason: ', $log->description);
                                            $reason = end($parts);
                                        }
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            <div>
                                                <div class="font-medium">{{ $log->created_at->format('M j, Y') }}</div>
                                                <div class="text-gray-500 dark:text-gray-400">{{ $log->created_at->format('g:i A') }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm">
                                                <div class="font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $log->model->file_number ?? 'N/A' }}
                                                </div>
                                                @if($log->model->title)
                                                    <div class="text-gray-500 dark:text-gray-400 truncate max-w-xs">
                                                        {{ $log->model->title }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400 text-xs font-semibold mr-3">
                                                    {{ strtoupper(substr($log->user->name ?? 'U', 0, 1)) }}
                                                </div>
                                                {{ $log->user->name ?? 'Unknown' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                            <div class="flex items-center space-x-2">
                                                <span class="px-2 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded text-xs">
                                                    {{ $oldLawyerName }}
                                                </span>
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                                <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded text-xs">
                                                    {{ $newLawyerName }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $reason ?: 'No reason provided' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if(is_object($assignmentLogs) && method_exists($assignmentLogs, 'links'))
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900">
                        {{ $assignmentLogs->appends(request()->query())->links() }}
                    </div>
                    @endif
                @else
                    <div class="px-6 py-12 text-center">
                        <div class="mx-auto h-12 w-12 text-gray-400">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No assignment history found</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No case reassignments match your current filters.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>






