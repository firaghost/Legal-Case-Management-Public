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
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-white mb-1">Case #{{ $case->file_number }}</h1>
                                <p class="text-white/90 text-lg">{{ $case->title ?? 'Case Details' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <button onclick="openReassignModal()" class="inline-flex items-center gap-2 px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                                Reassign Lawyer
                            </button>
                            <a href="{{ route('supervisor.cases.edit-history', $case) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Edit History
                            </a>
                            <a href="{{ route('supervisor.cases.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Back to Cases
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Case Overview Cards -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <!-- Status Card -->
                <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-3xl overflow-hidden border border-gray-200 dark:border-gray-700">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/50 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Status</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="text-center">
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium 
                                {{ $case->status === 'Open' ? 'bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-200' :
                                   ($case->status === 'Closed' ? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200' : 
                                   'bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-200') }}">
                                {{ $case->status }}
                            </span>
                            @if($case->approved_at)
                                <div class="mt-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/50 text-emerald-800 dark:text-emerald-200">Approved</span>
                                </div>
                            @elseif($case->closure_requested_at)
                                <div class="mt-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-200">Approval Pending</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Type & Priority Card -->
                <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-3xl overflow-hidden border border-gray-200 dark:border-gray-700">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Classification</h3>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Type</label>
                            <p class="text-gray-900 dark:text-white font-semibold">{{ $case->type ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Priority</label>
                            <p class="text-gray-900 dark:text-white font-semibold">{{ $case->priority ?? 'Normal' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Assignment Card -->
                <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-3xl overflow-hidden border border-gray-200 dark:border-gray-700">
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/50 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Assignment</h3>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Lawyer</label>
                            <p class="text-gray-900 dark:text-white font-semibold">{{ $case->lawyer->name ?? 'Unassigned' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Branch</label>
                            <p class="text-gray-900 dark:text-white font-semibold">{{ $case->branch->name ?? 'Not specified' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Case Details Section -->
            <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-3xl overflow-hidden border border-gray-200 dark:border-gray-700 mb-8">
                <div class="bg-gradient-to-r from-gray-50 to-blue-50 dark:from-gray-900/20 dark:to-blue-900/20 px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Case Information</h2>
                    </div>
                </div>
                <div class="p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-6">
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Case Title</label>
                                <p class="text-gray-900 dark:text-white font-semibold text-lg">{{ $case->title ?? 'No title provided' }}</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-6">
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">File Number</label>
                                <p class="text-gray-900 dark:text-white font-semibold">{{ $case->file_number }}</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-6">
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Created By</label>
                                <p class="text-gray-900 dark:text-white font-semibold">{{ $case->creator->name ?? 'Unknown' }}</p>
                            </div>
                        </div>
                        <div class="space-y-6">
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-6">
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Created Date</label>
                                <p class="text-gray-900 dark:text-white font-semibold">{{ $case->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-6">
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Last Updated</label>
                                <p class="text-gray-900 dark:text-white font-semibold">{{ $case->updated_at->format('M d, Y') }}</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-6">
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Due Date</label>
                                <p class="text-gray-900 dark:text-white font-semibold">{{ $case->due_date ? $case->due_date->format('M d, Y') : 'Not set' }}</p>
                            </div>
                        </div>
                    </div>
                    @if($case->description)
                        <div class="mt-8">
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-6">
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-3">Case Description</label>
                                <p class="text-gray-900 dark:text-white leading-relaxed">{{ $case->description }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    {{-- Include the reassignment modal --}}
    @include('supervisor.cases.reassign')
</x-app-layout>






