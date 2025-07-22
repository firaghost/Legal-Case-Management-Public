<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Modern Page Header -->
            <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-800 dark:to-indigo-800 rounded-3xl shadow-2xl mb-8 border border-gray-100 dark:border-gray-700">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.1"%3E%3Ccircle cx="30" cy="30" r="4"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>
                <div class="relative px-8 py-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v0H8v0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h1 class="text-3xl font-bold text-white">My Cases</h1>
                                    <p class="text-blue-100 font-medium">Manage and track all your assigned legal cases</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 mt-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/20 text-white">
                                    <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                                    {{ $cases->total() ?? 0 }} Total Cases
                                </span>
                                <span class="text-blue-200 text-sm">{{ now()->format('l, F j, Y') }}</span>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="{{ route('lawyer.cases.create') }}" class="inline-flex items-center px-6 py-3 bg-white text-blue-600 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Create New Case
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modern Filters Card -->
            <div x-data="{ open: false }" class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden mb-8">
                <div class="p-6 cursor-pointer bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border-b border-gray-200 dark:border-gray-700" @click="open = !open">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Advanced Filters</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Filter cases by type, status, and date</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
                <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" class="p-6">
                    <form method="GET" action="{{ route('lawyer.cases.index') }}" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Case Type Filter -->
                            <div class="space-y-2">
                                <label for="type" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Case Type</label>
                                <select id="type" name="type" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-gray-100 transition-all duration-200">
                                    <option value="">All Types</option>
                                    @foreach(App\Models\CaseType::all() as $caseType)
                                        <option value="{{ $caseType->id }}" {{ request('type') == $caseType->id ? 'selected' : '' }}>
                                            {{ $caseType->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Status Filter -->
                            <div class="space-y-2">
                                <label for="status" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Status</label>
                                <select id="status" name="status" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-gray-100 transition-all duration-200">
                                    <option value="">All Statuses</option>
                                    <option value="Open" {{ request('status') === 'Open' ? 'selected' : '' }}>Open</option>
                                    <option value="Closed" {{ request('status') === 'Closed' ? 'selected' : '' }}>Closed</option>
                                    <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                                </select>
                            </div>
                            <!-- Date Filter -->
                            <div class="space-y-2">
                                <label for="date" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Date Opened</label>
                                <input type="date" id="date" name="date" value="{{ request('date') }}" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-gray-100 transition-all duration-200">
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:justify-end items-center gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <a href="{{ route('lawyer.cases.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Clear Filters
                            </a>
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Apply Filters
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modern Cases Grid -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Cases Overview</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $cases->total() ?? 0 }} cases found</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($cases->count() > 0)
                    <!-- Desktop Table View -->
                    <div class="hidden lg:block">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Case Details</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type & Code</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Next Appointment</th>
                                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($cases as $case)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mr-3">
                                                        <span class="text-sm font-bold text-blue-600 dark:text-blue-400">#</span>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $case->file_number }}</div>
                                                        <div class="text-xs text-gray-500 dark:text-gray-400">File Number</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $case->caseType->name ?? 'N/A' }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">Code: {{ $case->code }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex flex-col gap-1">
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                                        {{ $case->status === 'Open' ? 'bg-green-500 text-white dark:bg-green-900/30 dark:text-green-400' :
                                                           ($case->status === 'Closed' ? 'bg-gray-500 text-white dark:bg-gray-700 dark:text-gray-300' : 
                                                           'bg-yellow-500 text-white dark:bg-yellow-900/30 dark:text-yellow-400') }}">
                                                        {{ $case->status }}
                                                    </span>
                                                    @if($case->approved_at)
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">Approved</span>
                                                    @elseif($case->closure_requested_at)
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400">Pending Approval</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                                    {{ optional($case->next_appointment)->format('M d, Y') ?? 'N/A' }}
                                                </div>
                                                @if($case->next_appointment)
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $case->next_appointment->diffForHumans() }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    <a href="{{ route('lawyer.cases.show', $case) }}" class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-900/30 dark:text-blue-400 rounded-lg text-xs font-semibold transition-colors duration-200">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                        View
                                                    </a>
                                                    <a href="{{ route('lawyer.cases.edit', $case) }}" class="inline-flex items-center px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white dark:bg-emerald-900/30 dark:text-emerald-400 rounded-lg text-xs font-semibold transition-colors duration-200">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                        Edit
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Mobile Card View -->
                    <div class="lg:hidden p-4 space-y-4">
                        @foreach ($cases as $case)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4 border border-gray-200 dark:border-gray-600">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                            <span class="text-sm font-bold text-blue-600 dark:text-blue-400">#</span>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $case->file_number }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $case->caseType->name ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ route('lawyer.cases.show', $case) }}" class="p-2 bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-900/30 dark:text-blue-400 rounded-lg">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('lawyer.cases.edit', $case) }}" class="p-2 bg-emerald-600 hover:bg-emerald-700 text-white dark:bg-emerald-900/30 dark:text-emerald-400 rounded-lg">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Code:</span>
                                        <span class="text-gray-900 dark:text-gray-100 ml-1">{{ $case->code }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Status:</span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ml-1
                                            {{ $case->status === 'Open' ? 'bg-green-500 text-white dark:bg-green-900/30 dark:text-green-400' :
                                               ($case->status === 'Closed' ? 'bg-gray-500 text-white dark:bg-gray-600 dark:text-gray-300' : 
                                               'bg-yellow-500 text-white dark:bg-yellow-900/30 dark:text-yellow-400') }}">
                                            {{ $case->status }}
                                        </span>
                                    </div>
                                </div>
                                @if($case->next_appointment)
                                    <div class="mt-3 text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">Next Appointment:</span>
                                        <span class="text-gray-900 dark:text-gray-100 ml-1">{{ $case->next_appointment->format('M d, Y') }}</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">No Cases Found</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-6">No cases match your current filter criteria. Try adjusting your filters or create a new case.</p>
                        <a href="{{ route('lawyer.cases.create') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Create New Case
                        </a>
                    </div>
                @endif
                
                @if ($cases->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                        {{ $cases->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>






