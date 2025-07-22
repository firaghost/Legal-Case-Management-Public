@php use Illuminate\Support\Str; @endphp

<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Modern Page Header -->
            <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-800 dark:to-indigo-800 rounded-3xl shadow-2xl mb-8">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.1"%3E%3Ccircle cx="30" cy="30" r="4"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>
                <div class="relative px-8 py-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-white mb-1">Edit History</h1>
                                <p class="text-white/90 text-lg">Case: {{ $case->title }}</p>
                            </div>
                        </div>
                        <a href="{{ route('lawyer.cases.show', $case) }}" 
                           class="inline-flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm border border-white/30 rounded-xl font-semibold text-sm text-white hover:bg-white/30 focus:outline-none focus:ring-2 focus:ring-white/50 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                            </svg>
                            Back to Case
                        </a>
                    </div>
                </div>
            </div>

            <!-- Edit History Timeline -->
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-3xl overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white">Change History</h2>
                    </div>
                </div>
                <div class="p-8">
                    <div class="relative">
                        <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gradient-to-b from-blue-500 to-indigo-500"></div>
                        @forelse($editLogs as $log)
                            <div class="relative flex items-start mb-8">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center shadow-lg border-4 border-white dark:border-gray-800">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                    </svg>
                                </div>
                                <div class="ml-6 flex-1">
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-600">
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center">
                                                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400">{{ substr($log->user->name ?? 'U', 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $log->user->name ?? 'Unknown User' }}</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $log->created_at->format('M d, Y \\a\\t H:i') }}</p>
                                                </div>
                                            </div>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-200">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                                </svg>
                                                Edit
                                            </span>
                                        </div>
                                        <p class="text-gray-700 dark:text-gray-300 mb-4 font-medium">{{ $log->description }}</p>
                                        <div class="overflow-x-auto">
                                            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-600 overflow-hidden">
                                                <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-3 border-b border-gray-200 dark:border-gray-600">
                                                    <div class="grid grid-cols-3 gap-4">
                                                        <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">Field</div>
                                                        <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">Old Value</div>
                                                        <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">New Value</div>
                                                    </div>
                                                </div>
                                                <div class="divide-y divide-gray-200 dark:divide-gray-600">
                                                    @php
                                                        $fields = array_unique(array_merge(
                                                            array_keys($log->old_properties ?? []),
                                                            array_keys($log->properties ?? [])
                                                        ));
                                                    @endphp
                                                    @foreach($fields as $field)
                                                        <div class="px-6 py-4 grid grid-cols-3 gap-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                                            <div class="font-medium text-gray-900 dark:text-white text-sm">{{ ucwords(str_replace('_', ' ', $field)) }}</div>
                                                            <div class="text-sm">
                                                                @php $oldVal = $log->old_properties[$field] ?? null; @endphp
                                                                @php
                                                                    $displayOld = $oldVal;
                                                                    if(is_string($oldVal) && Str::contains($oldVal, '/')) {
                                                                        $displayOld = basename($oldVal);
                                                                    }
                                                                @endphp
                                                                @if($displayOld !== null)
                                                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                                                        {{ is_array($oldVal) ? json_encode($oldVal) : $displayOld }}
                                                                    </span>
                                                                @else
                                                                    <span class="text-gray-400 dark:text-gray-500">—</span>
                                                                @endif
                                                            </div>
                                                            <div class="text-sm">
                                                                @php $newVal = $log->properties[$field] ?? null; @endphp
                                                                @php
                                                                    $displayNew = $newVal;
                                                                    if(is_string($newVal) && Str::contains($newVal, '/')) {
                                                                        $displayNew = basename($newVal);
                                                                    }
                                                                @endphp
                                                                @if($displayNew !== null)
                                                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                                                        {{ is_array($newVal) ? json_encode($newVal) : $displayNew }}
                                                                    </span>
                                                                @else
                                                                    <span class="text-gray-400 dark:text-gray-500">—</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="text-center">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">No Edit History</h3>
                                        <p class="text-gray-500 dark:text-gray-400">No changes have been recorded for this case yet.</p>
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 





