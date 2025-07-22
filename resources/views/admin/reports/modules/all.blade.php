<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Modern Page Header -->
            <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-800 dark:to-indigo-800 rounded-3xl shadow-2xl mb-8">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.1"%3E%3Ccircle cx="30" cy="30" r="4"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>
                <div class="relative px-8 py-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-white mb-2">All Cases Report</h1>
                                <p class="text-blue-100 text-lg">Comprehensive analytics for all case types</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.reports') }}" class="inline-flex items-center px-4 py-2 bg-white/20 backdrop-blur-sm text-white font-medium rounded-xl hover:bg-white/30 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to Reports
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filters Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-8 mb-8">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Report Filters</h2>
                    <p class="text-gray-600 dark:text-gray-400">Customize your report by applying filters below</p>
                </div>
                
                <form method="GET" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Date Range -->
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>Date Range</span>
                                </div>
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <input type="text" name="start_date" placeholder="Start Date" 
                                    class="datepicker w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400" 
                                    value="{{ request('start_date') }}" readonly>
                                <input type="text" name="end_date" placeholder="End Date" 
                                    class="datepicker w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400" 
                                    value="{{ request('end_date') }}" readonly>
                            </div>
                        </div>
                        
                        <!-- Branch Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span>Branch</span>
                                </div>
                            </label>
                            <select name="branch_id" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">All Branches</option>
                                @foreach($branches as $id => $name)
                                    <option value="{{ $id }}" @selected(request('branch_id') == $id)>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Case Type Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    <span>Case Type</span>
                                </div>
                            </label>
                            <select name="type_id" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">All Types</option>
                                @foreach($types as $id => $name)
                                    <option value="{{ $id }}" @selected(request('type_id') == $id)>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- Filter Actions -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Apply filters to customize your report data
                        </div>
                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-xl hover:from-blue-700 hover:to-indigo-700 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Apply Filters
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Export Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6 mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Export Options</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Download your report in different formats</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('admin.reports.export', ['module' => 'all', 'format' => 'pdf'] + request()->all()) }}" 
                           class="inline-flex items-center px-4 py-2.5 bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300 text-sm font-medium rounded-lg hover:bg-red-200 dark:hover:bg-red-900/70 transition-colors" 
                           target="_blank">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Export PDF
                        </a>
                        <a href="{{ route('admin.reports.export', ['module' => 'all', 'format' => 'excel'] + request()->all()) }}" 
                           class="inline-flex items-center px-4 py-2.5 bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300 text-sm font-medium rounded-lg hover:bg-green-200 dark:hover:bg-green-900/70 transition-colors" 
                           target="_blank">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2h2a2 2 0 002-2z"/>
                            </svg>
                            Export Excel
                        </a>
                    </div>
                </div>
            </div>
            <!-- Cases Data -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-1">Cases Data</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Detailed information for all matching cases</p>
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $cases->count() }} {{ Str::plural('case', $cases->count()) }} found
                        </div>
                    </div>
                </div>
                
                <!-- Desktop Table View -->
                <div class="hidden xl:block">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span>File No.</span>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                        <span>Type</span>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>Status</span>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                        <span>Branch</span>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        <span>Parties</span>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span>Opened</span>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>Advisory</span>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span>Created By</span>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6"/>
                                        </svg>
                                        <span>Lawyer</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($cases as $case)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $case->file_number }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $case->caseType?->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($case->status === 'Open') bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300
                                            @elseif($case->status === 'Closed') bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300
                                            @elseif($case->status === 'Pending') bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-300
                                            @else bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300 @endif">
                                            {{ $case->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $case->branch?->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white max-w-xs">
                                            @if($case->plaintiffs && $case->plaintiffs->count() > 0)
                                                <div class="mb-1">
                                                    <span class="font-medium text-green-600 dark:text-green-400">Plaintiffs:</span>
                                                    <span class="block text-xs">{{ $case->plaintiffs->pluck('name')->join(', ') }}</span>
                                                </div>
                                            @endif
                                            @if($case->defendants && $case->defendants->count() > 0)
                                                <div>
                                                    <span class="font-medium text-red-600 dark:text-red-400">Defendants:</span>
                                                    <span class="block text-xs">{{ $case->defendants->pluck('name')->join(', ') }}</span>
                                                </div>
                                            @endif
                                            @if((!$case->plaintiffs || $case->plaintiffs->count() === 0) && (!$case->defendants || $case->defendants->count() === 0))
                                                <span class="text-gray-400">No parties listed</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $case->opened_at ? $case->opened_at->format('M d, Y') : 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($case->legalAdvisory)
                                            <div class="text-xs space-y-1">
                                                <div class="flex items-center space-x-1">
                                                    <span class="font-medium text-gray-600 dark:text-gray-400">Type:</span>
                                                    <span class="text-gray-900 dark:text-white">{{ $case->legalAdvisory->advisory_type }}</span>
                                                </div>
                                                <div class="flex items-center space-x-1">
                                                    <span class="font-medium text-gray-600 dark:text-gray-400">Status:</span>
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300">
                                                        {{ $case->legalAdvisory->status }}
                                                    </span>
                                                </div>
                                                <div class="flex items-center space-x-1">
                                                    <span class="font-medium text-gray-600 dark:text-gray-400">Date:</span>
                                                    <span class="text-gray-900 dark:text-white">{{ $case->legalAdvisory->request_date ? \Carbon\Carbon::parse($case->legalAdvisory->request_date)->format('M d, Y') : 'N/A' }}</span>
                                                </div>
                                                @if($case->legalAdvisory->subject)
                                                    <div class="mt-1">
                                                        <span class="font-medium text-gray-600 dark:text-gray-400">Subject:</span>
                                                        <p class="text-gray-900 dark:text-white text-xs mt-0.5 max-w-xs truncate" title="{{ $case->legalAdvisory->subject }}">{{ $case->legalAdvisory->subject }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-sm">No advisory</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $case->creator?->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $case->lawyer?->name ?? 'N/A' }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mb-4">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No cases found</h3>
                                            <p class="text-gray-500 dark:text-gray-400">Try adjusting your filters to see more results</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Mobile/Tablet Card View -->
            <div class="xl:hidden">
                @forelse($cases as $case)
                    <div class="border-b border-gray-200 dark:border-gray-700 p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <div class="space-y-4">
                            <!-- Header with File Number and Status -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $case->file_number ?? 'N/A' }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300">
                                        {{ $case->caseType?->name ?? 'N/A' }}
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($case->status === 'Open') bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300
                                        @elseif($case->status === 'Closed') bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300
                                        @elseif($case->status === 'Pending') bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-300
                                        @else bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300 @endif">
                                        {{ $case->status ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Parties Information -->
                            @if(($case->plaintiffs && $case->plaintiffs->count() > 0) || ($case->defendants && $case->defendants->count() > 0))
                                <div class="space-y-2">
                                    @if($case->plaintiffs && $case->plaintiffs->count() > 0)
                                        <div>
                                            <span class="text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wide">Plaintiffs:</span>
                                            <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $case->plaintiffs->pluck('name')->join(', ') }}</p>
                                        </div>
                                    @endif
                                    @if($case->defendants && $case->defendants->count() > 0)
                                        <div>
                                            <span class="text-xs font-medium text-red-600 dark:text-red-400 uppercase tracking-wide">Defendants:</span>
                                            <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $case->defendants->pluck('name')->join(', ') }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                            
                            <!-- Case Details -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Opened Date</span>
                                    <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $case->opened_at ? $case->opened_at->format('M d, Y') : 'N/A' }}</p>
                                </div>
                                @if($case->branch)
                                    <div>
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Branch</span>
                                        <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $case->branch->name }}</p>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Legal Advisory Details -->
                            @if($case->legalAdvisory)
                                <div class="pt-2 border-t border-gray-100 dark:border-gray-600">
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Legal Advisory</span>
                                    <div class="mt-2 space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Type:</span>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $case->legalAdvisory->advisory_type }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Status:</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300">
                                                {{ $case->legalAdvisory->status }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Date:</span>
                                            <span class="text-sm text-gray-900 dark:text-white">{{ $case->legalAdvisory->request_date ? \Carbon\Carbon::parse($case->legalAdvisory->request_date)->format('M d, Y') : 'N/A' }}</span>
                                        </div>
                                        @if($case->legalAdvisory->subject)
                                            <div class="mt-2">
                                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Subject:</span>
                                                <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $case->legalAdvisory->subject }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No cases found</h3>
                            <p class="text-gray-500 dark:text-gray-400">Try adjusting your filters to see more results</p>
                        </div>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>





