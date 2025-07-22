<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Modern Page Header -->
            <div class="relative overflow-hidden bg-gradient-to-r from-emerald-600 to-blue-600 dark:from-emerald-800 dark:to-blue-800 rounded-3xl shadow-2xl mb-8">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.1"%3E%3Ccircle cx="30" cy="30" r="4"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>
                <div class="relative px-8 py-8">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-white mb-1">Welcome back, {{ Auth::user()->name }}!</h1>
                            <p class="text-white/90 text-lg">Supervisor Dashboard - Manage your cases and team efficiently</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <a href="#pending-approvals" class="block group">
                    <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-3xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-3xl transition-all duration-300 transform group-hover:-translate-y-1">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-red-100 to-red-200 dark:from-red-900/50 dark:to-red-800/50 rounded-2xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Awaiting Closure</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['pending_closure'] }}</p>
                            </div>
                        </div>
                    </div>
                </a>
                
                <a href="#pending-approvals" class="block group">
                    <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-3xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-3xl transition-all duration-300 transform group-hover:-translate-y-1">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-orange-100 to-orange-200 dark:from-orange-900/50 dark:to-orange-800/50 rounded-2xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending Approval</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['pending_approval'] }}</p>
                            </div>
                        </div>
                    </div>
                </a>
                
                <a href="#execution-files" class="block group">
                    <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-3xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-3xl transition-all duration-300 transform group-hover:-translate-y-1">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/50 dark:to-blue-800/50 rounded-2xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Execution Files</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['execution_opened'] }}</p>
                            </div>
                        </div>
                    </div>
                </a>
                
                <a href="#appeals" class="block group">
                    <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-3xl p-6 border border-gray-200 dark:border-gray-700 hover:shadow-3xl transition-all duration-300 transform group-hover:-translate-y-1">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-yellow-100 to-yellow-200 dark:from-yellow-900/50 dark:to-yellow-800/50 rounded-2xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Appeals</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['appeals'] }}</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Case Type Cards -->
            <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-3xl overflow-hidden border border-gray-200 dark:border-gray-700 mb-8">
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Case Types Overview</h2>
                    </div>
                </div>
                <div class="p-8">
                    <div x-data="{ filterType: '{{ $selectedType ?? '' }}' }">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($caseTypes as $type)
                                <form method="GET" action="{{ route('supervisor.cases') }}" class="contents">
                                    <input type="hidden" name="type" value="{{ $type->id }}">
                                    <div @click="filterType = '{{ $type->id }}'; $el.closest('form').submit()"
                                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 cursor-pointer border-2 border-gray-200 dark:border-gray-600 hover:shadow-2xl hover:border-indigo-400 dark:hover:border-indigo-500 transition-all duration-300 transform hover:-translate-y-1 {{ $selectedType == $type->id ? 'ring-2 ring-indigo-500 border-indigo-500' : '' }}">
                                        <div class="text-center">
                                            <div class="w-12 h-12 mx-auto mb-4 bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900/50 dark:to-purple-900/50 rounded-2xl flex items-center justify-center">
                                                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $type->name }}</h3>
                                            <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mb-2">{{ $stats['by_type'][$type->id] ?? 0 }}</div>
                                            <div class="flex items-center justify-center gap-2 text-sm">
                                                <span class="text-gray-600 dark:text-gray-400">Total Cases</span>
                                            </div>
                                            @if(($stats['by_type_pending'][$type->id] ?? 0) > 0)
                                                <div class="mt-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-900/50 text-orange-800 dark:text-orange-200">
                                                    {{ $stats['by_type_pending'][$type->id] }} pending
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </form>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Updates from Lawyers -->
            <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-3xl overflow-hidden border border-gray-200 dark:border-gray-700 mb-8">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.5a2.121 2.121 0 113 3L7 19.5 3 21l1.5-4L16.5 3.5z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Recent Updates from Lawyers</h2>
                    </div>
                </div>
                <div class="p-8">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse ($recentUpdates as $u)
                            <div class="bg-gradient-to-br from-gray-50 to-white dark:from-gray-700 dark:to-gray-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-gray-600 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                                <div class="flex items-center gap-2 mb-4">
                                    @if($u->caseFile)
                                        <a href="{{ route('supervisor.cases', ['search' => $u->caseFile->file_number]) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 text-sm rounded-xl font-semibold hover:bg-blue-200 dark:hover:bg-blue-900/70 transition duration-200">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6z" clip-rule="evenodd"/>
                                            </svg>
                                            Case #{{ $u->caseFile->file_number }}
                                        </a>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm rounded-xl font-semibold">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6z" clip-rule="evenodd"/>
                                            </svg>
                                            Case #N/A
                                        </span>
                                    @endif
                                    <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-lg">{{ $u->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-sm text-gray-800 dark:text-gray-200 mb-3 leading-relaxed">{{ $u->notes }}</div>
                                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                    <div class="w-6 h-6 bg-gradient-to-br from-green-100 to-emerald-100 dark:from-green-900/50 dark:to-emerald-900/50 rounded-full flex items-center justify-center">
                                        <svg class="w-3 h-3 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <span class="font-medium">{{ $u->user->name ?? 'Unknown' }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-12">
                                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-800 rounded-2xl flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.5a2.121 2.121 0 113 3L7 19.5 3 21l1.5-4L16.5 3.5z" />
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">No recent updates</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Updates from lawyers will appear here</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Pending Approvals Section -->
            <div id="pending-approvals" class="bg-white dark:bg-gray-800 shadow-2xl rounded-3xl overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19a2 2 0 100-4 2 2 0 000 4z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Cases Needing Authorization</h2>
                    </div>
                </div>
                <div class="p-8">
                    <!-- Alert Banner -->
                    @if($needAuth->count())
                    <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 bg-gradient-to-r from-yellow-100 to-orange-100 dark:from-yellow-900/30 dark:to-orange-900/30 border border-yellow-200 dark:border-yellow-800 rounded-2xl">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-yellow-200 dark:bg-yellow-800 rounded-xl flex items-center justify-center">
                                    <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-yellow-800 dark:text-yellow-200">
                                        You have 
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-yellow-200 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-200 mx-1">
                                            {{ $needAuth->count() }}
                                        </span>
                                        case(s) awaiting approval{{ $selectedType ? ' for ' . optional($caseTypes->firstWhere('id', $selectedType))->name : '' }}
                                    </p>
                                </div>
                            </div>
                            <button @click="show = false" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-200 transition duration-200">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Filter Section -->
                    <div class="flex items-center gap-4 mb-6">
                        <form method="GET" class="flex items-center gap-3">
                            <label for="type" class="text-sm font-medium text-gray-700 dark:text-gray-300">Filter by Type:</label>
                            <select name="type" id="type" 
                                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white transition duration-200" 
                                    onchange="this.form.submit()">
                                <option value="">All Types</option>
                                @foreach($caseTypes as $type)
                                    <option value="{{ $type->id }}" @if($selectedType == $type->id) selected @endif>{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </form>
                        @if($selectedType)
                            <a href="?#pending-approvals" class="text-sm text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-200 font-medium transition duration-200">Clear Filter</a>
                        @endif
                    </div>
                    
                    <!-- Cases Table -->
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-2xl overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-gradient-to-r from-yellow-100 to-orange-100 dark:from-yellow-900/50 dark:to-orange-900/50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">File Number</th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Case Title</th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Request Date</th>
                                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900 dark:text-white">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                    @forelse ($needAuth as $c)
                                        <tr class="hover:bg-white dark:hover:bg-gray-700 transition duration-200 group">
                                            <td class="px-6 py-4">
                                                <span class="font-semibold text-yellow-700 dark:text-yellow-400">{{ $c->file_number }}</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="text-gray-900 dark:text-white font-medium">{{ $c->title }}</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="text-gray-600 dark:text-gray-400">{{ optional($c->closure_requested_at)->format('d M Y') }}</span>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <a href="{{ route('supervisor.approvals') }}" 
                                                   class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform group-hover:-translate-y-0.5">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Review
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-12 text-center">
                                                <div class="flex flex-col items-center gap-3">
                                                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-2xl flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19a2 2 0 100-4 2 2 0 000 4z" />
                                                        </svg>
                                                    </div>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">No pending approvals</p>
                                                    <p class="text-xs text-gray-400 dark:text-gray-500">Cases requiring approval will appear here</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>






