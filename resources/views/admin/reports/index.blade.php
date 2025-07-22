<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Modern Page Header -->
            <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-800 dark:to-indigo-800 rounded-3xl shadow-2xl mb-8">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.1"%3E%3Ccircle cx="30" cy="30" r="4"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>
                <div class="relative px-8 py-8">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-white mb-2">Reports Dashboard</h1>
                            <p class="text-blue-100 text-lg">Generate comprehensive case reports and analytics</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Introduction Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-8 mb-8">
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/50 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Professional Case Reports</h2>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                            Select a case module below to generate detailed analytics and professional PDF reports. Each module provides comprehensive insights with export capabilities for presentations and documentation.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Report Modules Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach([['key'=>'all','label'=>'All Cases','icon'=>'chart-bar','color'=>'blue'],['key'=>'clean-loan-recovery','label'=>'Clean Loan Recovery','icon'=>'credit-card','color'=>'green'],['key'=>'advisory','label'=>'Legal Advisory','icon'=>'scale','color'=>'purple'],['key'=>'criminal','label'=>'Criminal Litigation','icon'=>'shield','color'=>'red'],['key'=>'secured-loan-recovery','label'=>'Secured Loan Recovery','icon'=>'lock','color'=>'yellow'],['key'=>'labor','label'=>'Labor Litigation','icon'=>'users','color'=>'indigo'],['key'=>'other-civil','label'=>'Other Civil Cases','icon'=>'briefcase','color'=>'gray']] as $module)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-2xl transition-all duration-200 transform hover:-translate-y-1">
                        <!-- Module Header -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-{{ $module['color'] }}-100 dark:bg-{{ $module['color'] }}-900/50 rounded-xl flex items-center justify-center">
                                    @if($module['icon'] === 'chart-bar')
                                        <svg class="w-6 h-6 text-{{ $module['color'] }}-600 dark:text-{{ $module['color'] }}-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                    @elseif($module['icon'] === 'credit-card')
                                        <svg class="w-6 h-6 text-{{ $module['color'] }}-600 dark:text-{{ $module['color'] }}-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                        </svg>
                                    @elseif($module['icon'] === 'scale')
                                        <svg class="w-6 h-6 text-{{ $module['color'] }}-600 dark:text-{{ $module['color'] }}-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                                        </svg>
                                    @elseif($module['icon'] === 'shield')
                                        <svg class="w-6 h-6 text-{{ $module['color'] }}-600 dark:text-{{ $module['color'] }}-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                    @elseif($module['icon'] === 'lock')
                                        <svg class="w-6 h-6 text-{{ $module['color'] }}-600 dark:text-{{ $module['color'] }}-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    @elseif($module['icon'] === 'users')
                                        <svg class="w-6 h-6 text-{{ $module['color'] }}-600 dark:text-{{ $module['color'] }}-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                        </svg>
                                    @else
                                        <svg class="w-6 h-6 text-{{ $module['color'] }}-600 dark:text-{{ $module['color'] }}-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0H8m8 0v2a2 2 0 01-2 2H10a2 2 0 01-2-2V6m8 0H8m0 0H4a2 2 0 00-2 2v6a2 2 0 002 2h2m2 0h8a2 2 0 002-2v-6a2 2 0 00-2-2h-2m-2 0V6"/>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $module['label'] }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Report Module</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Module Description -->
                        <div class="mb-6">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Generate detailed analytics and export-ready PDF reports for {{ strtolower($module['label']) }} cases with comprehensive insights and statistics.
                            </p>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('admin.reports.show', $module['key']) }}" class="flex-1 inline-flex items-center justify-center px-4 py-2.5 bg-{{ $module['color'] }}-100 dark:bg-{{ $module['color'] }}-900/50 text-{{ $module['color'] }}-700 dark:text-{{ $module['color'] }}-300 text-sm font-medium rounded-lg hover:bg-{{ $module['color'] }}-200 dark:hover:bg-{{ $module['color'] }}-900/70 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                View Report
                            </a>
                            <a href="{{ route('admin.reports.export', $module['key']) }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" title="Export PDF">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>






