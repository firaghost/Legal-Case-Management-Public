<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8" x-data="{
        showModal: false,
        selected: null,
        loading: false,
        selectedRows: [],
        selectAll: false,
        toggleAll(rows) {
            if (this.selectAll) {
                this.selectedRows = rows.map(r => r.id);
            } else {
                this.selectedRows = [];
            }
        },
        toggleRow(id) {
            if (this.selectedRows.includes(id)) {
                this.selectedRows = this.selectedRows.filter(i => i !== id);
            } else {
                this.selectedRows.push(id);
            }
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Modern Page Header -->
            <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-800 dark:to-indigo-800 rounded-3xl shadow-2xl mb-8">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.1"%3E%3Ccircle cx="30" cy="30" r="4"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>
                <div class="relative px-8 py-8">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-white mb-1">All Cases</h1>
                            <p class="text-white/90 text-lg">Manage and oversee all legal cases in the system</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Filters Section -->
            <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-3xl overflow-hidden border border-gray-200 dark:border-gray-700 mb-8">
                <div class="bg-gradient-to-r from-gray-50 to-blue-50 dark:from-gray-900/20 dark:to-blue-900/20 px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Filter Cases</h2>
                    </div>
                </div>
                <div class="p-8">
                    <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Case Type</label>
                            <select name="type" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition duration-200">
                                <option value="">All Types</option>
                                <option>Loan</option>
                                <option>Labor</option>
                                <option>Civil</option>
                                <option>Criminal</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                            <select name="status" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition duration-200">
                                <option value="">All Status</option>
                                <option>Open</option>
                                <option>Closed</option>
                                <option>Pending</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Lawyer</label>
                            <select name="lawyer" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition duration-200">
                                <option value="">All Lawyers</option>
                                @foreach ($cases->pluck('lawyer.name','lawyer.id')->unique() as $name)
                                    <option>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Branch</label>
                            <select name="branch" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition duration-200">
                                <option value="">All Branches</option>
                                @foreach ($cases->pluck('branch.name')->unique() as $branch)
                                    <option>{{ $branch }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">From Date</label>
                            <input type="date" name="from" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition duration-200" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">To Date</label>
                            <input type="date" name="to" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition duration-200" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                            <input type="text" name="search" placeholder="Search cases..." class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition duration-200" />
                        </div>
                        <div class="flex items-end gap-3">
                            <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="11" cy="11" r="8" />
                                    <path d="M21 21l-4.35-4.35" />
                                </svg>
                                Apply
                            </button>
                            <button type="reset" class="px-4 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition duration-200">
                                Reset
                            </button>
                            <button type="button" onclick="window.location='{{ url()->current() }}?export=csv'" class="px-4 py-3 bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300 rounded-xl hover:bg-green-200 dark:hover:bg-green-900/70 transition duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Bulk Action Bar -->
            <div x-show="selectedRows.length > 0" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" class="mb-8">
                <div class="bg-gradient-to-r from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/20 border border-emerald-200 dark:border-emerald-700 rounded-2xl p-6 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/50 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-emerald-800 dark:text-emerald-200">Bulk Actions</h3>
                                <p class="text-sm text-emerald-600 dark:text-emerald-400" x-text="selectedRows.length + ' cases selected'"></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <form method="POST" action="{{ route('supervisor.cases.bulk-approve') }}" onsubmit="return confirm('Approve selected cases?')" class="inline">
                                @csrf
                                <input type="hidden" name="ids" :value="selectedRows.join(',')">
                                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Approve
                                </button>
                            </form>
                            <button type="button" @click="window.location='{{ url()->current() }}?export=bulk&ids=' + selectedRows.join(',')" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Export
                            </button>
                            <button type="button" @click="selectedRows=[]; selectAll=false" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Clear
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cases Table -->
            <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-3xl overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="bg-gradient-to-r from-gray-50 to-indigo-50 dark:from-gray-900/20 dark:to-indigo-900/20 px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Cases Overview</h2>
                    </div>
                </div>
                <div class="relative overflow-x-auto">
                    <template x-if="loading">
                        <div class="absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm z-10">
                            <div class="flex items-center gap-3">
                                <svg class="animate-spin h-8 w-8 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                </svg>
                                <span class="text-gray-600 dark:text-gray-400 font-medium">Loading cases...</span>
                            </div>
                        </div>
                    </template>
                    <table class="min-w-full">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-4 text-left">
                                    <input type="checkbox" x-model="selectAll" @change="toggleAll(@js($cases->items()))" class="w-4 h-4 text-blue-600 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 focus:ring-2">
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Case #</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Lawyer</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Branch</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($cases as $case)
                                <tr class="hover:bg-blue-50 transition">
                                    <td class="px-6 py-4 text-center">
                                        <input type="checkbox" :value="{{ $case->id }}" x-model="selectedRows" @change="selectAll = selectedRows.length === @js($cases->count())" class="w-4 h-4 text-blue-600 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 focus:ring-2">
                                    </td>
                                    <td class="px-6 py-4 font-medium">{{ $case->file_number }}</td>
                                    <td class="px-6 py-4">{{ $case->type }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $case->status === 'Open' ? 'bg-green-100 text-green-800' :
                                               ($case->status === 'Closed' ? 'bg-gray-100 text-gray-800' : 
                                               'bg-yellow-100 text-yellow-800') }}">
                                            {{ $case->status }}
                                        </span>
                                        @if($case->approved_at)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 ml-1">Approved</span>
                                        @elseif($case->closure_requested_at)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 ml-1">Approval Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">{{ $case->lawyer->name ?? '-' }}</td>
                                    <td class="px-6 py-4">{{ $case->branch->name ?? '-' }}</td>
                                    <td class="px-6 py-4">{{ $case->created_at }}</td>
                                    <td class="px-6 py-4 text-center flex gap-2 justify-center">
                                        <a href="{{ route('supervisor.cases.show', $case) }}" class="px-2 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700 flex items-center gap-1" title="View Details">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /><path d="M12 16a4 4 0 100-8 4 4 0 000 8z" /></svg>
                                            View
                                        </a>
                                        <button onclick="openReassignModal({{ $case->id }}, '{{ $case->file_number }}', '{{ $case->lawyer->name ?? 'Unassigned' }}')" class="px-2 py-1 bg-purple-600 text-white rounded text-xs hover:bg-purple-700 flex items-center gap-1" title="Reassign Lawyer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                            </svg>
                                            Reassign
                                        </button>
                                        <a href="{{ url()->current() }}?export=row&id={{ $case->id }}" class="px-2 py-1 bg-green-600 text-white rounded text-xs hover:bg-green-700 flex items-center gap-1" title="Export Row">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                                            Export
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $cases->links() }}
            </div>

            <!-- Case Details Modal -->
            <div x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50">
                <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95" class="bg-white dark:bg-gray-800 w-full max-w-2xl rounded-3xl shadow-2xl border border-gray-200 dark:border-gray-700 relative overflow-hidden max-h-[90vh] overflow-y-auto">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Case Details</h3>
                            </div>
                            <button @click="showModal = false" class="w-8 h-8 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl flex items-center justify-center text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="p-8">
                        <template x-if="selected">
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Case Number</label>
                                        <p class="text-gray-900 dark:text-white font-semibold" x-text="selected.file_number"></p>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                                        <p class="text-gray-900 dark:text-white font-semibold" x-text="selected.type"></p>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                        <p class="text-gray-900 dark:text-white font-semibold" x-text="selected.status"></p>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lawyer</label>
                                        <p class="text-gray-900 dark:text-white font-semibold" x-text="selected.lawyer?.name ?? '-'"></p>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Branch</label>
                                        <p class="text-gray-900 dark:text-white font-semibold" x-text="selected.branch?.name ?? '-'"></p>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Created</label>
                                        <p class="text-gray-900 dark:text-white font-semibold" x-text="selected.created_at"></p>
                                    </div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                                    <p class="text-gray-900 dark:text-white" x-text="selected.description ?? 'No description available'"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            
            <!-- Reassignment Modal -->
            <div id="reassignModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
                    <div class="mt-3">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Reassign Case</h3>
                            <button onclick="closeReassignModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <form id="reassignForm" method="POST" class="space-y-4">
                            @csrf
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Case Information
                                </label>
                                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Case #: <span id="modal-case-number" class="font-semibold text-gray-900 dark:text-white"></span></p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Current Lawyer: <span id="modal-current-lawyer" class="font-semibold text-gray-900 dark:text-white"></span></p>
                                </div>
                            </div>

                            <div>
                                <label for="new_lawyer_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    New Lawyer <span class="text-red-500">*</span>
                                </label>
                                <select name="new_lawyer_id" id="new_lawyer_id" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select a lawyer...</option>
                                </select>
                                <div id="lawyer-loading" class="hidden mt-2 text-sm text-gray-500">Loading lawyers...</div>
                            </div>

                            <div>
                                <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Reason for Reassignment (Optional)
                                </label>
                                <textarea name="reason" id="reason" rows="3" maxlength="500" placeholder="Enter reason for reassignment..."
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
                                <div class="mt-1 text-xs text-gray-500">Maximum 500 characters</div>
                            </div>

                            <div class="flex justify-end gap-3 pt-4">
                                <button type="button" onclick="closeReassignModal()"
                                    class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 transition-colors">
                                    Reassign Case
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        const lawyersUrl = "{{ url('/lawyers') }}";
        const casesBaseUrl = "{{ url('/cases') }}";
        
        let currentCaseId = null;
        
        function openReassignModal(caseId, caseNumber, currentLawyer) {
            currentCaseId = caseId;
            document.getElementById('modal-case-number').textContent = caseNumber;
            document.getElementById('modal-current-lawyer').textContent = currentLawyer;
            document.getElementById('reassignForm').action = `${casesBaseUrl}/${caseId}/reassign`;
            document.getElementById('reassignModal').classList.remove('hidden');
            loadLawyers();
        }

    function closeReassignModal() {
        document.getElementById('reassignModal').classList.add('hidden');
        currentCaseId = null;
        document.getElementById('reassignForm').reset();
    }

    async function loadLawyers() {
        const select = document.getElementById('new_lawyer_id');
        const loading = document.getElementById('lawyer-loading');
        
        loading.classList.remove('hidden');
        
        try {
            console.log('Fetching lawyers from:', lawyersUrl);
            const response = await fetch(lawyersUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                credentials: 'same-origin'
            });
            
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const lawyers = await response.json();
            console.log('Lawyers loaded:', lawyers);
            
            // Clear existing options except the first one
            select.innerHTML = '<option value="">Select a lawyer...</option>';
            
            if (Array.isArray(lawyers) && lawyers.length > 0) {
                lawyers.forEach(lawyer => {
                    const option = document.createElement('option');
                    option.value = lawyer.id;
                    option.textContent = lawyer.name + (lawyer.email ? ` (${lawyer.email})` : '');
                    select.appendChild(option);
                });
            } else {
                select.innerHTML += '<option value="" disabled>No lawyers available</option>';
            }
        } catch (error) {
            console.error('Error loading lawyers:', error);
            select.innerHTML = '<option value="">Error loading lawyers</option>';
            
            // Show user-friendly error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'mt-2 text-sm text-red-600';
            errorDiv.textContent = 'Failed to load lawyers. Please try again.';
            select.parentNode.appendChild(errorDiv);
        } finally {
            loading.classList.add('hidden');
        }
    }

    // Close modal when clicking outside
    document.getElementById('reassignModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeReassignModal();
        }
    });
    </script>
</x-app-layout>






