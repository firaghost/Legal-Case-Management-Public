<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Modern Welcome Header -->
            <div class="relative overflow-hidden bg-gradient-to-r from-[#3ca44c] to-[#2563eb] dark:from-[#1e3a2e] dark:to-[#2563eb] rounded-3xl shadow-2xl mb-8">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.1"%3E%3Ccircle cx="30" cy="30" r="4"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>
                <div class="relative px-8 py-12">
                    <div class="flex items-center gap-6">
                        <div class="flex-shrink-0">
                            <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                                <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2L2 7v10c0 5.55 3.84 10 9 11 1.09-.21 2.11-.56 3-1.03V7l-2-1.5V17c0 .55-.45 1-1 1s-1-.45-1-1V6l6-4.5L22 7v10c0 5.55-3.84 10-9 11-5.16-1-9-5.45-9-11V7l10-5z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h1 class="text-4xl font-bold text-white mb-2">
                                Welcome back, <span class="text-yellow-200">{{ Auth::user()->name }}</span>!
                            </h1>
                            <p class="text-xl text-white/90 font-medium">Here's your legal workspace overview and recent activity.</p>
                            <div class="flex items-center gap-2 mt-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/20 text-white">
                                    <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                                    {{ ucfirst(Auth::user()->role ?? 'Lawyer') }}
                                </span>
                                <span class="text-white/80 text-sm">{{ now()->format('l, F j, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modern Statistics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <a href="{{ route('lawyer.cases.index', ['status' => 'Open']) }}" class="group">
                    <div class="relative overflow-hidden bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 dark:border-gray-700">
                        <div class="absolute inset-0 bg-gradient-to-br from-green-500 to-emerald-600 opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Active Cases</p>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['active'] }}</p>
                                    <p class="text-xs text-green-600 dark:text-green-400 mt-1">Currently open</p>
                                </div>
                                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v0H8v0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('lawyer.cases.index', ['status' => 'Closed']) }}" class="group">
                    <div class="relative overflow-hidden bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 dark:border-gray-700">
                        <div class="absolute inset-0 bg-gradient-to-br from-[#1e3a2e] to-gray-700 opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Closed This Month</p>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['closedMonth'] }}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Completed cases</p>
                                </div>
                                <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('lawyer.cases.index', ['has_upcoming_hearings' => true]) }}" class="group">
                    <div class="relative overflow-hidden bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 dark:border-gray-700">
                        <div class="absolute inset-0 bg-gradient-to-br from-[#2563eb] to-blue-700 opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Upcoming Hearings</p>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['hearings'] }}</p>
                                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">This week</p>
                                </div>
                                <div class="w-12 h-12 bg-[#3ca44c]/20 dark:bg-[#3ca44c]/30 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('lawyer.progress') }}" class="group">
                    <div class="relative overflow-hidden bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 dark:border-gray-700">
                        <div class="absolute inset-0 bg-gradient-to-br from-[#3ca44c] to-[#1e3a2e] opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Recent Activity</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">View Updates</p>
                                    <p class="text-xs text-orange-600 dark:text-orange-400 mt-1">Progress tracking</p>
                                </div>
                                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/50 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Modern Quick Actions Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Quick Links Section -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Quick Actions
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4">
                            <a href="{{ route('lawyer.cases.index') }}" class="group flex flex-col items-center p-4 rounded-xl bg-gradient-to-br from-[#3ca44c] to-[#1e3a2e] dark:from-[#3ca44c]/20 dark:to-[#1e3a2e]/20 hover:from-[#3ca44c]/20 hover:to-[#1e3a2e]/20 dark:hover:from-[#3ca44c]/30 dark:hover:to-[#1e3a2e]/30 transition-all duration-300 transform hover:scale-105">
                                <div class="w-12 h-12 bg-[#3ca44c]/20 dark:bg-[#3ca44c]/30 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-6 h-6 text-[#3ca44c] dark:text-[#3ca44c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v0H8v0z"></path>
                                    </svg>
                                </div>
                                <span class="font-semibold text-gray-900 dark:text-gray-100 text-center">My Cases</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">View all cases</span>
                            </a>
                            <a href="{{ route('lawyer.advisory') }}" class="group flex flex-col items-center p-4 rounded-xl bg-gradient-to-br from-[#3ca44c]/10 to-[#2563eb]/10 dark:from-[#3ca44c]/20 dark:to-[#2563eb]/20 hover:from-[#3ca44c]/20 hover:to-[#2563eb]/20 dark:hover:from-[#3ca44c]/30 dark:hover:to-[#2563eb]/30 transition-all duration-300 transform hover:scale-105">
                                <div class="w-12 h-12 bg-[#3ca44c]/20 dark:bg-[#3ca44c]/30 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                </div>
                                <span class="font-semibold text-gray-900 dark:text-gray-100 text-center">Advisory</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">Legal advice</span>
                            </a>
                            <a href="{{ route('lawyer.cases.create') }}" class="group flex flex-col items-center p-4 rounded-xl bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 hover:from-purple-100 hover:to-pink-100 dark:hover:from-purple-900/30 dark:hover:to-pink-900/30 transition-all duration-300 transform hover:scale-105">
                                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/50 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </div>
                                <span class="font-semibold text-gray-900 dark:text-gray-100 text-center">New Case</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">Create case</span>
                            </a>
                            <a href="{{ route('lawyer.progress') }}" class="group flex flex-col items-center p-4 rounded-xl bg-gradient-to-br from-[#3ca44c] to-[#1e3a2e] dark:from-orange-900/20 dark:to-red-900/20 hover:from-orange-100 hover:to-red-100 dark:hover:from-orange-900/30 dark:hover:to-red-900/30 transition-all duration-300 transform hover:scale-105">
                                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/50 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Case Updates</h3>
                                <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">Track updates</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Case Types Section -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Case Types
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-3 max-h-80 overflow-y-auto">
                            @foreach($caseTypes as $caseType)
                                <a href="{{ route('lawyer.cases.index', ['type' => $caseType->id]) }}" class="group block p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50 hover:bg-gradient-to-br hover:from-blue-50 hover:to-indigo-50 dark:hover:from-blue-900/20 dark:hover:to-indigo-900/20 transition-all duration-300 transform hover:scale-105 border border-gray-100 dark:border-gray-600">
                                    <div class="text-center">
                                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-sm mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $caseType->name }}</h3>
                                        <div class="flex items-center justify-center gap-1">
                                            <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $caseType->case_files_count }}</span>
                                        </div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Cases</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>






