<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Modern Page Header -->
            <div class="relative overflow-hidden bg-gradient-to-r from-slate-600 to-gray-600 dark:from-slate-800 dark:to-gray-800 rounded-3xl shadow-2xl mb-8">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.1"%3E%3Ccircle cx="30" cy="30" r="4"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>
                <div class="relative px-8 py-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-white mb-2">Other Civil Cases Report</h1>
                                <p class="text-slate-100 text-lg">Comprehensive civil litigation tracking and case management analytics</p>
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
                    <p class="text-gray-600 dark:text-gray-400">Customize your civil cases report parameters</p>
                </div>
                
                <form method="GET" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Date Range -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Date Range
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="text" name="start_date" placeholder="Start Date" 
                                       class="datepicker w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-slate-500 focus:border-slate-500 dark:bg-gray-700 dark:text-white transition-colors" 
                                       value="{{ request('start_date') }}" readonly>
                                <input type="text" name="end_date" placeholder="End Date" 
                                       class="datepicker w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-slate-500 focus:border-slate-500 dark:bg-gray-700 dark:text-white transition-colors" 
                                       value="{{ request('end_date') }}" readonly>
                            </div>
                        </div>

                        <!-- Base Branch -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Base Branch
                            </label>
                            <select name="branch_id" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-slate-500 focus:border-slate-500 dark:bg-gray-700 dark:text-white transition-colors">
                                <option value="">All Branches</option>
                                @foreach($branches as $id => $name)
                                    <option value="{{ $id }}" @selected(request('branch_id') == $id)>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Report Type -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                Report Period
                            </label>
                            <select name="type" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-slate-500 focus:border-slate-500 dark:bg-gray-700 dark:text-white transition-colors">
                                <option value="">Select Period</option>
                                @foreach(['monthly'=>'Monthly','quarterly'=>'Quarterly','semi-annual'=>'Semi-Annual','9-month'=>'9-Month','annual'=>'Annual'] as $k=>$v)
                                    <option value="{{ $k }}" @selected(request('type')==$k)>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex justify-end mt-8">
                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-slate-600 to-gray-600 hover:from-slate-700 hover:to-gray-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"/>
                            </svg>
                            Apply Filters
                        </button>
                    </div>
                </form>
            </div>

            <!-- Export Options -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Export Options</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Download your civil cases report</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('admin.reports.export', ['module'=>'other-civil'] + request()->all()) }}&format=pdf" 
                           class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Export PDF
                        </a>
                        <a href="{{ route('admin.reports.export', ['module'=>'other-civil'] + request()->all()) }}&format=excel" 
                           class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Export Excel
                        </a>
                    </div>
                </div>
            </div>

            <!-- Other Civil Cases Data -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-1">Other Civil Cases</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Civil litigation cases tracking and performance analytics</p>
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
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        <span>Parties</span>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                        </svg>
                                        <span>Claimed Amount</span>
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
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                        </svg>
                                        <span>Recovery</span>
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
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                                        </svg>
                                        <span>Court Info</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($cases as $case)
                                @php
                                    $cf = $case->caseFile;
                                    $plaintiffs = $cf?->plaintiffs->pluck('name')->join('; ');
                                    $defendants = $cf?->defendants->pluck('name')->join('; ');
                                    $claimed = $case->claimed_amount ?? '';
                                    $recovered = $case->recovered_amount;
                                    $performance = ($claimed && is_numeric($claimed) && $claimed > 0) ? round(($recovered ?? 0) * 100 / $claimed, 2) : '';
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $cf?->file_number ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm space-y-1">
                                            @if($plaintiffs)
                                                <div>
                                                    <span class="font-medium text-green-600 dark:text-green-400">Plaintiffs:</span>
                                                    <span class="block text-xs text-gray-900 dark:text-white max-w-xs truncate" title="{{ $plaintiffs }}">{{ $plaintiffs }}</span>
                                                </div>
                                            @endif
                                            @if($defendants)
                                                <div>
                                                    <span class="font-medium text-red-600 dark:text-red-400">Defendants:</span>
                                                    <span class="block text-xs text-gray-900 dark:text-white max-w-xs truncate" title="{{ $defendants }}">{{ $defendants }}</span>
                                                </div>
                                            @endif
                                            @if(!$plaintiffs && !$defendants)
                                                <span class="text-gray-400">No parties listed</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            @if($claimed && is_numeric($claimed))
                                                <span class="font-medium text-slate-600 dark:text-slate-400">ETB {{ number_format($claimed, 2) }}</span>
                                            @elseif($claimed)
                                                <span class="font-medium text-slate-600 dark:text-slate-400">{{ $claimed }}</span>
                                            @else
                                                <span class="text-gray-400">Not specified</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($cf?->status === 'Open') bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300
                                            @elseif($cf?->status === 'Closed') bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300
                                            @elseif($cf?->status === 'Pending') bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-300
                                            @else bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300 @endif">
                                            {{ $cf?->status ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm space-y-2">
                                            <div class="text-gray-900 dark:text-white">
                                                @if($recovered && is_numeric($recovered))
                                                    <span class="font-medium text-slate-600 dark:text-slate-400">ETB {{ number_format($recovered, 2) }}</span>
                                                @elseif($recovered)
                                                    <span class="font-medium text-slate-600 dark:text-slate-400">{{ $recovered }}</span>
                                                @else
                                                    <span class="text-gray-400">Not recovered</span>
                                                @endif
                                            </div>
                                            @if($performance && is_numeric($performance))
                                                <div class="flex items-center space-x-2">
                                                    <div class="w-20 bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                                        <div class="bg-gradient-to-r from-slate-500 to-gray-500 h-2 rounded-full" style="width: {{ min($performance, 100) }}%"></div>
                                                    </div>
                                                    <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ $performance }}%</span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $cf?->branch?->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm space-y-1">
                                            @if($cf?->court?->name)
                                                <div class="text-gray-900 dark:text-white font-medium">{{ $cf->court->name }}</div>
                                            @endif
                                            @if($cf?->court_file_no)
                                                <div class="text-gray-600 dark:text-gray-400 text-xs">File: {{ $cf->court_file_no }}</div>
                                            @endif
                                            @if(!$cf?->court?->name && !$cf?->court_file_no)
                                                <span class="text-gray-400">No court info</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mb-4">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No civil cases found</h3>
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
                        @php
                            $cf = $case->caseFile;
                            $plaintiffs = $cf?->plaintiffs->pluck('name')->join('; ');
                            $defendants = $cf?->defendants->pluck('name')->join('; ');
                            $claimed = $case->claimed_amount ?? '';
                            $recovered = $case->recovered_amount;
                            $performance = ($claimed && is_numeric($claimed) && $claimed > 0) ? round(($recovered ?? 0) * 100 / $claimed, 2) : '';
                        @endphp
                        <div class="border-b border-gray-200 dark:border-gray-700 p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="space-y-4">
                                <!-- Header with File Number and Status -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $cf?->file_number ?? 'N/A' }}</span>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($cf?->status === 'Open') bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300
                                        @elseif($cf?->status === 'Closed') bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300
                                        @elseif($cf?->status === 'Pending') bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-300
                                        @else bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300 @endif">
                                        {{ $cf?->status ?? 'N/A' }}
                                    </span>
                                </div>
                                
                                <!-- Parties Information -->
                                @if($plaintiffs || $defendants)
                                    <div class="space-y-2">
                                        @if($plaintiffs)
                                            <div>
                                                <span class="text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wide">Plaintiffs:</span>
                                                <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $plaintiffs }}</p>
                                            </div>
                                        @endif
                                        @if($defendants)
                                            <div>
                                                <span class="text-xs font-medium text-red-600 dark:text-red-400 uppercase tracking-wide">Defendants:</span>
                                                <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $defendants }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                
                                <!-- Financial Information -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Claimed Amount</span>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white mt-1">
                                            @if($claimed && is_numeric($claimed))
                                                ETB {{ number_format($claimed, 2) }}
                                            @elseif($claimed)
                                                {{ $claimed }}
                                            @else
                                                <span class="text-gray-400">Not specified</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div>
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Recovered</span>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white mt-1">
                                            @if($recovered && is_numeric($recovered))
                                                <span class="text-slate-600 dark:text-slate-400">ETB {{ number_format($recovered, 2) }}</span>
                                            @elseif($recovered)
                                                <span class="text-slate-600 dark:text-slate-400">{{ $recovered }}</span>
                                            @else
                                                <span class="text-gray-400">Not recovered</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Performance Bar -->
                                @if($performance && is_numeric($performance))
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Performance</span>
                                            <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ $performance }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                            <div class="bg-gradient-to-r from-slate-500 to-gray-500 h-2 rounded-full" style="width: {{ min($performance, 100) }}%"></div>
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Branch / Court / Created / Lawyer Info -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-2 border-t border-gray-100 dark:border-gray-600">
                                    <div>
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Branch</span>
                                        <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $cf?->branch?->name ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Court</span>
                                        <div class="text-sm text-gray-900 dark:text-white mt-1">
                                            @if($cf?->court?->name)
                                                <div class="font-medium">{{ $cf->court->name }}</div>
                                                @if($cf?->court_file_no)
                                                    <div class="text-xs text-gray-600 dark:text-gray-400">File: {{ $cf->court_file_no }}</div>
                                                @endif
                                            @else
                                                <span class="text-gray-400">No court info</span>
                                            @endif
                                         </div>
                                     </div>
                                    <!-- Created By -->
                                    <div>
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Created By</span>
                                        <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $cf?->creator?->name ?? 'N/A' }}</p>
                                    </div>
                                    <!-- Lawyer -->
                                    <div>
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Lawyer</span>
                                        <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $cf?->lawyer?->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No civil cases found</h3>
                                <p class="text-gray-500 dark:text-gray-400">Try adjusting your filters to see more results</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>






