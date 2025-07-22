<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Modern Page Header -->
            <div class="relative overflow-hidden bg-gradient-to-r from-purple-600 to-blue-600 dark:from-purple-800 dark:to-blue-800 rounded-3xl shadow-2xl mb-8">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.1"%3E%3Ccircle cx="30" cy="30" r="4"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>
                <div class="relative px-8 py-8">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-white mb-1">Welcome back, {{ Auth::user()->name }}!</h1>
                            <p class="text-white/90 text-lg">Admin Dashboard - Manage users, cases, and system settings</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- System Statistics -->
            <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-3xl border border-gray-200 dark:border-gray-700 overflow-hidden mb-8">
                <div class="bg-gradient-to-r from-purple-50 to-blue-50 dark:from-purple-900/20 dark:to-blue-900/20 px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">System Overview</h2>
                    </div>
                </div>
                <div class="p-8">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6">
                        <!-- Users Stat -->
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-2xl p-6 border border-blue-200 dark:border-blue-700/50">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mb-1">{{ $stats['users'] }}</div>
                            <div class="text-sm text-blue-600/70 dark:text-blue-400/70 font-medium">Total Users</div>
                        </div>

                        <!-- Open Cases Stat -->
                        <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-2xl p-6 border border-green-200 dark:border-green-700/50">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400 mb-1">{{ $stats['open_cases'] }}</div>
                            <div class="text-sm text-green-600/70 dark:text-green-400/70 font-medium">Open Cases</div>
                        </div>

                        <!-- Closed Cases Stat -->
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700/20 dark:to-gray-600/20 rounded-2xl p-6 border border-gray-200 dark:border-gray-600/50">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-gray-500 dark:bg-gray-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/>
                                        <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-2xl font-bold text-gray-600 dark:text-gray-400 mb-1">{{ $stats['closed_cases'] }}</div>
                            <div class="text-sm text-gray-600/70 dark:text-gray-400/70 font-medium">Closed Cases</div>
                        </div>

                        <!-- Lawyers Stat -->
                        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 rounded-2xl p-6 border border-yellow-200 dark:border-yellow-700/50">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-yellow-500 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mb-1">{{ $stats['lawyers'] }}</div>
                            <div class="text-sm text-yellow-600/70 dark:text-yellow-400/70 font-medium">Lawyers</div>
                        </div>

                        <!-- Supervisors Stat -->
                        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20 rounded-2xl p-6 border border-indigo-200 dark:border-indigo-700/50">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-indigo-500 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                        <path d="M16 7a1 1 0 10-2 0v1a1 1 0 102 0V7zM12 7a1 1 0 10-2 0v1a1 1 0 102 0V7z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 mb-1">{{ $stats['supervisors'] }}</div>
                            <div class="text-sm text-indigo-600/70 dark:text-indigo-400/70 font-medium">Supervisors</div>
                        </div>

                        <!-- Pending Cases Stat -->
                        <div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 rounded-2xl p-6 border border-red-200 dark:border-red-700/50">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-2xl font-bold text-red-600 dark:text-red-400 mb-1">{{ $stats['pending'] }}</div>
                            <div class="text-sm text-red-600/70 dark:text-red-400/70 font-medium">Pending Cases</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Case Status Analytics -->
            @php
                $hasChartData = ($stats['open_cases'] ?? 0) > 0 || ($stats['closed_cases'] ?? 0) > 0 || ($stats['pending'] ?? 0) > 0;
                $totalCases = ($stats['open_cases'] ?? 0) + ($stats['closed_cases'] ?? 0) + ($stats['pending'] ?? 0);
            @endphp
            <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-3xl border border-gray-200 dark:border-gray-700 overflow-hidden mb-8">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"/>
                                    <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Case Status Analytics</h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Total Cases: {{ $totalCases }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex bg-gray-100 dark:bg-gray-700 rounded-xl p-1" id="chartToggle">
                                <button class="chart-toggle-btn active px-3 py-1.5 text-sm font-medium rounded-lg transition-all duration-200" data-chart="doughnut">
                                    <svg class="w-4 h-4 mr-1.5 inline" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"/>
                                    </svg>
                                    Pie
                                </button>
                                <button class="chart-toggle-btn px-3 py-1.5 text-sm font-medium rounded-lg transition-all duration-200" data-chart="bar">
                                    <svg class="w-4 h-4 mr-1.5 inline" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                    </svg>
                                    Bar
                                </button>
                            </div>
                            <button class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white text-sm font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Export Report
                            </button>
                        </div>
                    </div>
                </div>
                <div class="p-8">
                    @if($hasChartData)
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <!-- Chart Container -->
                            <div class="lg:col-span-2">
                                <div class="bg-gradient-to-br from-gray-50 to-white dark:from-gray-700/50 dark:to-gray-800/50 rounded-2xl p-6 border border-gray-200 dark:border-gray-600 h-96">
                                    <canvas id="statusChart" class="w-full h-full"></canvas>
                                </div>
                            </div>
                            
                            <!-- Statistics Panel -->
                            <div class="space-y-4">
                                <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-2xl p-6 border border-green-200 dark:border-green-700/50 cursor-pointer hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1" onclick="filterCases('open')">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['open_cases'] ?? 0 }}</div>
                                            <div class="text-sm font-medium text-green-700 dark:text-green-300">Open Cases</div>
                                            <div class="text-xs text-green-600/70 dark:text-green-400/70 mt-1">
                                                {{ $totalCases > 0 ? round((($stats['open_cases'] ?? 0) / $totalCases) * 100, 1) : 0 }}% of total
                                            </div>
                                        </div>
                                        <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700/20 dark:to-gray-600/20 rounded-2xl p-6 border border-gray-200 dark:border-gray-600/50 cursor-pointer hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1" onclick="filterCases('closed')">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $stats['closed_cases'] ?? 0 }}</div>
                                            <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Closed Cases</div>
                                            <div class="text-xs text-gray-600/70 dark:text-gray-400/70 mt-1">
                                                {{ $totalCases > 0 ? round((($stats['closed_cases'] ?? 0) / $totalCases) * 100, 1) : 0 }}% of total
                                            </div>
                                        </div>
                                        <div class="w-12 h-12 bg-gray-500 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 rounded-2xl p-6 border border-red-200 dark:border-red-700/50 cursor-pointer hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1" onclick="filterCases('pending')">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['pending'] ?? 0 }}</div>
                                            <div class="text-sm font-medium text-red-700 dark:text-red-300">Pending Cases</div>
                                            <div class="text-xs text-red-600/70 dark:text-red-400/70 mt-1">
                                                {{ $totalCases > 0 ? round((($stats['pending'] ?? 0) / $totalCases) * 100, 1) : 0 }}% of total
                                            </div>
                                        </div>
                                        <div class="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-gradient-to-br from-gray-50 to-white dark:from-gray-700/50 dark:to-gray-800/50 rounded-2xl p-12 border border-gray-200 dark:border-gray-600 text-center">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Data Available</h3>
                            <p class="text-gray-500 dark:text-gray-400">No case status data to display at this time.</p>
                        </div>
                    @endif
                </div>
            </div>
                        </div>
                    </div>

                    @push('scripts')
        @if($hasChartData)
                        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                        <script>
                // Chart data
                const chartData = {
                    labels: ['Open', 'Closed', 'Pending'],
                    datasets: [{
                        label: 'Cases',
                        data: [
                            {{ $stats['open_cases'] ?? 0 }},
                            {{ $stats['closed_cases'] ?? 0 }},
                            {{ $stats['pending'] ?? 0 }}
                        ],
                        backgroundColor: [
                            'rgba(34, 197, 94, 0.8)',   // green for open
                            'rgba(107, 114, 128, 0.8)', // gray for closed  
                            'rgba(239, 68, 68, 0.8)'    // red for pending
                        ],
                        borderColor: [
                            'rgba(34, 197, 94, 1)',
                            'rgba(107, 114, 128, 1)',
                            'rgba(239, 68, 68, 1)'
                        ],
                        borderWidth: 2,
                        borderRadius: 12,
                        maxBarThickness: 80,
                        hoverBackgroundColor: [
                            'rgba(34, 197, 94, 0.9)',
                            'rgba(107, 114, 128, 0.9)',
                            'rgba(239, 68, 68, 0.9)'
                        ]
                    }]
                };

                // Chart options
                const chartOptions = {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: 'rgba(255, 255, 255, 0.1)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                title: function(context) {
                                    return context[0].label + ' Cases';
                                },
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
                                    return `Count: ${context.raw} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 1500,
                        easing: 'easeOutQuart'
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { 
                                color: '#64748b', 
                                font: { weight: '600', size: 13 },
                                padding: 10
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { 
                                color: 'rgba(148, 163, 184, 0.1)',
                                drawBorder: false
                            },
                            ticks: { 
                                color: '#64748b', 
                                font: { weight: '500', size: 12 }, 
                                stepSize: 1,
                                padding: 10
                            }
                        }
                    },
                    onClick: (e, elements) => {
                        if (elements.length > 0) {
                            const label = e.chart.data.labels[elements[0].index];
                            window.location.href = `/admin/cases?status=${encodeURIComponent(label.toLowerCase())}`;
                        }
                    }
                };

                // Initialize chart
                const statusChartCtx = document.getElementById('statusChart').getContext('2d');
                let currentChart = new Chart(statusChartCtx, {
                    type: 'doughnut',
                    data: {
                        ...chartData,
                        datasets: [{
                            ...chartData.datasets[0],
                            cutout: '60%',
                            borderWidth: 3,
                            hoverBorderWidth: 4
                        }]
                    },
                    options: {
                        ...chartOptions,
                        plugins: {
                            ...chartOptions.plugins,
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    font: { size: 12, weight: '500' },
                                    color: '#64748b'
                                }
                            }
                        }
                    }
                });

                // Chart toggle functionality
                document.querySelectorAll('.chart-toggle-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const chartType = this.dataset.chart;
                        
                        // Update active state
                        document.querySelectorAll('.chart-toggle-btn').forEach(b => {
                            b.classList.remove('active', 'bg-white', 'text-blue-600', 'shadow-sm');
                            b.classList.add('text-gray-600', 'hover:text-gray-800');
                        });
                        this.classList.add('active', 'bg-white', 'text-blue-600', 'shadow-sm');
                        this.classList.remove('text-gray-600', 'hover:text-gray-800');
                        
                        // Destroy current chart
                        currentChart.destroy();
                        
                        // Create new chart based on type
                        if (chartType === 'doughnut') {
                            currentChart = new Chart(statusChartCtx, {
                                type: 'doughnut',
                                data: {
                                    ...chartData,
                                    datasets: [{
                                        ...chartData.datasets[0],
                                        cutout: '60%',
                                        borderWidth: 3,
                                        hoverBorderWidth: 4
                                    }]
                                },
                                options: {
                                    ...chartOptions,
                                    plugins: {
                                        ...chartOptions.plugins,
                                        legend: {
                                            display: true,
                                            position: 'bottom',
                                            labels: {
                                                padding: 20,
                                                usePointStyle: true,
                                                pointStyle: 'circle',
                                                font: { size: 12, weight: '500' },
                                                color: '#64748b'
                                            }
                                        }
                                    }
                                }
                            });
                        } else {
                            currentChart = new Chart(statusChartCtx, {
                                type: 'bar',
                                data: chartData,
                                options: chartOptions
                            });
                        }
                    });
                });

                // Filter cases function
                window.filterCases = function(status) {
                    window.location.href = `/admin/cases?status=${encodeURIComponent(status)}`;
                };

                // Initialize active button styling
                document.querySelector('.chart-toggle-btn.active').classList.add('bg-white', 'text-blue-600', 'shadow-sm');
                        </script>
        @endif
                    @endpush
</x-app-layout>






