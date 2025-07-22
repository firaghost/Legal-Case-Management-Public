<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Modern Page Header -->
            <div class="relative overflow-hidden bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-800 dark:to-purple-800 rounded-3xl shadow-2xl mb-8">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.1"%3E%3Ccircle cx="30" cy="30" r="4"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>
                <div class="relative px-8 py-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-white mb-1">Edit History</h1>
                                <p class="text-white/90 text-lg">Case: {{ $case->title }}</p>
                                <p class="text-white/70 text-sm">File #{{ $case->file_number }}</p>
                            </div>
                        </div>
                        <a href="{{ route('supervisor.cases.show', $case) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl font-medium transition-all duration-200 transform hover:scale-105 shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Case
                        </a>
                    </div>
                </div>
            </div>

            <!-- Edit History Content -->
            <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-3xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Modification Timeline</h3>
                    </div>
                </div>

                <div class="p-8">
                    @forelse($editLogs as $log)
                        <div class="relative mb-8 last:mb-0">
                            <!-- Timeline Line -->
                            @if(!$loop->last)
                                <div class="absolute left-6 top-16 w-0.5 h-full bg-gradient-to-b from-indigo-200 to-purple-200 dark:from-indigo-800 dark:to-purple-800"></div>
                            @endif
                            
                            <!-- Edit Entry -->
                            <div class="relative bg-gradient-to-r from-gray-50 to-indigo-50 dark:from-gray-800 dark:to-indigo-900/20 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                                <!-- Timeline Dot -->
                                <div class="absolute -left-2 top-6 w-4 h-4 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full border-4 border-white dark:border-gray-800 shadow-lg"></div>
                                
                                <!-- Header -->
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/50 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900 dark:text-white">{{ $log->user->name ?? 'Unknown User' }}</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $log->created_at->format('M d, Y \\a\\t H:i') }}</p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </span>
                                </div>

                                <!-- Description -->
                                @if($log->description)
                                    <div class="mb-4 p-3 bg-white dark:bg-gray-700 rounded-xl border border-gray-200 dark:border-gray-600">
                                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $log->description }}</p>
                                    </div>
                                @endif

                                <!-- Changes Table -->
                                @if($log->old_properties && count($log->old_properties) > 0)
                                    <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-600">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                            <thead class="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Field</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Previous Value</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">New Value</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                                @foreach(($log->old_properties ?? []) as $field => $oldValue)
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white capitalize">{{ str_replace('_', ' ', $field) }}</td>
                                                        <td class="px-4 py-3 text-sm">
                                                            <div class="flex items-center gap-2">
                                                                <div class="w-3 h-3 bg-red-100 dark:bg-red-900/50 rounded-full flex items-center justify-center">
                                                                    <div class="w-1.5 h-1.5 bg-red-500 rounded-full"></div>
                                                                </div>
                                                                <span class="text-red-700 dark:text-red-400 font-mono text-xs bg-red-50 dark:bg-red-900/20 px-2 py-1 rounded">
                                                                    {{ is_array($oldValue) ? json_encode($oldValue) : ($oldValue ?: 'Empty') }}
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-3 text-sm">
                                                            <div class="flex items-center gap-2">
                                                                <div class="w-3 h-3 bg-green-100 dark:bg-green-900/50 rounded-full flex items-center justify-center">
                                                                    <div class="w-1.5 h-1.5 bg-green-500 rounded-full"></div>
                                                                </div>
                                                                <span class="text-green-700 dark:text-green-400 font-mono text-xs bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded">
                                                                    {{ is_array($log->properties[$field] ?? null) ? json_encode($log->properties[$field] ?? null) : (($log->properties[$field] ?? '') ?: 'Empty') }}
                                                                </span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Edit History</h3>
                            <p class="text-gray-500 dark:text-gray-400">This case has not been modified since its creation.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 





