<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8" x-data="{ openId: null, cases: @js($cases->values()) }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Modern Page Header -->
            <div class="relative overflow-hidden bg-gradient-to-r from-emerald-600 to-green-600 dark:from-emerald-800 dark:to-green-800 rounded-3xl shadow-2xl mb-8">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.1"%3E%3Ccircle cx="30" cy="30" r="4"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>
                <div class="relative px-8 py-8">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-white mb-1">Case Approvals</h1>
                            <p class="text-white/90 text-lg">Review and approve pending case requests</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alert Banner -->
            @if($cases->count() > 0)
                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 border border-yellow-200 dark:border-yellow-700 rounded-2xl p-6 mb-8 shadow-lg">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/50 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-yellow-800 dark:text-yellow-200">Pending Approvals</h3>
                            <p class="text-yellow-700 dark:text-yellow-300">{{ $cases->count() }} {{ Str::plural('case', $cases->count()) }} awaiting your review and approval</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Cases Table -->
            <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-3xl overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="bg-gradient-to-r from-gray-50 to-emerald-50 dark:from-gray-900/20 dark:to-emerald-900/20 px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Cases Pending Review</h2>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        File Number
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Case Title</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Lawyer</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Request Type</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Requested Date</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($cases as $case)
                                <tr class="hover:bg-emerald-50 dark:hover:bg-emerald-900/10 transition-colors duration-200">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $case->file_number }}</div>
                                        @if($case->code)
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $case->code }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $case->title }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $case->type ?? 'General' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $case->lawyer->name ?? 'Unassigned' }}</div>
                                        @if($case->branch)
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $case->branch->name }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($case->closure_requested_at)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                Closure Request
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                                Initial Approval
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-900 dark:text-white font-medium">
                                        {{ $case->closure_requested_at ? $case->closure_requested_at->format('M d, Y') : $case->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button @click="openId = {{ $case->id }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                            </svg>
                                            Review
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-4">
                                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">All caught up!</h3>
                                                <p class="text-gray-500 dark:text-gray-400">No cases are currently awaiting approval.</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Review Modal -->
        <div x-show="openId !== null" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50">
            <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95" class="bg-white dark:bg-gray-800 w-full max-w-4xl rounded-3xl shadow-2xl border border-gray-200 dark:border-gray-700 relative overflow-hidden max-h-[90vh] overflow-y-auto" @click.outside="openId = null">
                <template x-if="cases.find(c => c.id === openId)">
                    <div>
                        <!-- Modal Header -->
                        <div class="bg-gradient-to-r from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/20 px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/50 rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Review Case</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400" x-text="'Case #' + cases.find(c => c.id === openId).file_number"></p>
                                    </div>
                                </div>
                                <button @click="openId = null" class="w-8 h-8 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl flex items-center justify-center text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Modal Content -->
                        <div class="p-8">
                            <!-- Case Title and Description -->
                            <div class="mb-6">
                                <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-2" x-text="cases.find(c => c.id === openId).title"></h4>
                                <p class="text-gray-600 dark:text-gray-400" x-text="cases.find(c => c.id === openId).description || 'No description provided.'"></p>
                            </div>

                            <!-- Dynamic Case Details -->
                            <template x-if="cases.find(c => c.id === openId)">
                                <div>
                                    @php
                                        // We'll use Alpine.js to dynamically render case details
                                        // The actual rendering will be handled by JavaScript
                                    @endphp
                                    <div x-data="{
                                        get currentCase() {
                                            return this.cases.find(c => c.id === this.openId);
                                        },
                                        get caseTypeCode() {
                                            return this.currentCase?.case_type?.code || this.currentCase?.caseType?.code || '';
                                        },
                                        get caseTypeSpecificData() {
                                            const caseType = this.caseTypeCode;
                                            switch(caseType) {
                                                case 'SLR': return this.currentCase?.secured_loan_recovery || this.currentCase?.securedLoanRecovery;
                                                case 'LAB': return this.currentCase?.labor_litigation || this.currentCase?.laborLitigation;
                                                case 'LIT': return this.currentCase?.litigation;
                                                case 'OCL': return this.currentCase?.other_civil_litigation || this.currentCase?.otherCivilLitigation;
                                                case 'CRM': return this.currentCase?.criminal_litigation || this.currentCase?.criminalLitigation;
                                                case 'ADV': return this.currentCase?.legal_advisory || this.currentCase?.legalAdvisory;
                                                case 'CLR': return this.currentCase?.clean_loan_recovery || this.currentCase?.cleanLoanRecovery;
                                                default: return null;
                                            }
                                        }
                                    }">
                                        <!-- Basic Case Information -->
                                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-6 mb-6">
                                            <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                Basic Information
                                            </h5>
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">File Number</label>
                                                    <p class="text-gray-900 dark:text-white font-medium" x-text="currentCase?.file_number"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Case Type</label>
                                                    <p class="text-gray-900 dark:text-white font-medium" x-text="currentCase?.case_type?.name || currentCase?.caseType?.name || 'N/A'"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Status</label>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800" x-text="currentCase?.status"></span>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Branch</label>
                                                    <p class="text-gray-900 dark:text-white" x-text="currentCase?.branch?.name || 'N/A'"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Work Unit</label>
                                                    <p class="text-gray-900 dark:text-white" x-text="currentCase?.work_unit?.name || currentCase?.workUnit?.name || 'N/A'"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Assigned Lawyer</label>
                                                    <p class="text-gray-900 dark:text-white" x-text="currentCase?.lawyer?.name || 'Unassigned'"></p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Financial Information -->
                                        <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-6 mb-6">
                                            <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                                </svg>
                                                Financial Details
                                            </h5>
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Claimed Amount</label>
                                                    <p class="text-lg font-bold text-green-700 dark:text-green-400" x-text="'ETB ' + (currentCase?.claimed_amount ? parseFloat(currentCase.claimed_amount).toLocaleString('en-US', {minimumFractionDigits: 2}) : '0.00')"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Recovered Amount</label>
                                                    <p class="text-lg font-bold text-blue-700 dark:text-blue-400" x-text="'ETB ' + (currentCase?.recovered_amount ? parseFloat(currentCase.recovered_amount).toLocaleString('en-US', {minimumFractionDigits: 2}) : '0.00')"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Outstanding Amount</label>
                                                    <p class="text-lg font-bold text-red-700 dark:text-red-400" x-text="'ETB ' + (currentCase?.outstanding_amount ? parseFloat(currentCase.outstanding_amount).toLocaleString('en-US', {minimumFractionDigits: 2}) : '0.00')"></p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Case Type Specific Details -->
                                        <template x-if="caseTypeSpecificData">
                                            <div class="mb-6">
                                                <!-- Secured Loan Recovery Details -->
                                                <template x-if="caseTypeCode === 'SLR'">
                                                    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-xl p-6">
                                                        <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Secured Loan Recovery Details</h5>
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Customer Name</label>
                                                                <p class="text-gray-900 dark:text-white" x-text="caseTypeSpecificData?.customer_name || 'N/A'"></p>
                                                            </div>
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Loan Amount</label>
                                                                <p class="text-gray-900 dark:text-white font-medium" x-text="'ETB ' + (caseTypeSpecificData?.loan_amount ? parseFloat(caseTypeSpecificData.loan_amount).toLocaleString('en-US', {minimumFractionDigits: 2}) : '0.00')"></p>
                                                            </div>
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Collateral Value</label>
                                                                <p class="text-gray-900 dark:text-white font-medium" x-text="'ETB ' + (caseTypeSpecificData?.collateral_value ? parseFloat(caseTypeSpecificData.collateral_value).toLocaleString('en-US', {minimumFractionDigits: 2}) : '0.00')"></p>
                                                            </div>
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Foreclosure Notice Date</label>
                                                                <p class="text-gray-900 dark:text-white" x-text="caseTypeSpecificData?.foreclosure_notice_date || 'N/A'"></p>
                                                            </div>
                                                            <div class="md:col-span-2">
                                                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Collateral Description</label>
                                                                <p class="text-gray-900 dark:text-white" x-text="caseTypeSpecificData?.collateral_description || 'N/A'"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>

                                                <!-- Labor Litigation Details -->
                                                <template x-if="caseTypeCode === 'LAB'">
                                                    <div class="bg-orange-50 dark:bg-orange-900/20 rounded-xl p-6">
                                                        <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Labor Litigation Details</h5>
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Claim Type</label>
                                                                <p class="text-gray-900 dark:text-white" x-text="caseTypeSpecificData?.claim_type || 'N/A'"></p>
                                                            </div>
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Claim Amount</label>
                                                                <p class="text-gray-900 dark:text-white font-medium" x-text="'ETB ' + (caseTypeSpecificData?.claim_amount ? parseFloat(caseTypeSpecificData.claim_amount).toLocaleString('en-US', {minimumFractionDigits: 2}) : '0.00')"></p>
                                                            </div>
                                                            <div class="md:col-span-2">
                                                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Claim Material Description</label>
                                                                <p class="text-gray-900 dark:text-white" x-text="caseTypeSpecificData?.claim_material_desc || 'N/A'"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>

                                                <!-- General Litigation Details -->
                                                <template x-if="caseTypeCode === 'LIT'">
                                                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-6">
                                                        <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Litigation Details</h5>
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Internal File No</label>
                                                                <p class="text-gray-900 dark:text-white" x-text="caseTypeSpecificData?.internal_file_no || 'N/A'"></p>
                                                            </div>
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Execution Opened</label>
                                                                <p class="text-gray-900 dark:text-white" x-text="caseTypeSpecificData?.execution_opened_at || 'N/A'"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <!-- Parties Information -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                                <!-- Plaintiffs -->
                                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-6 border border-blue-200 dark:border-blue-700">
                                    <div class="flex items-center gap-2 mb-4">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <h4 class="font-semibold text-blue-900 dark:text-blue-100">Plaintiffs</h4>
                                    </div>
                                    <template x-if="(cases.find(c => c.id === openId).plaintiffs && cases.find(c => c.id === openId).plaintiffs.length > 0)">
                                        <div class="space-y-3">
                                            <template x-for="pl in cases.find(c => c.id === openId).plaintiffs" :key="pl.id">
                                                <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
                                                    <p class="font-medium text-gray-900 dark:text-white" x-text="pl.name"></p>
                                                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1 space-y-1">
                                                        <p x-show="pl.contact_number" x-text="'Phone: ' + pl.contact_number"></p>
                                                        <p x-show="pl.email" x-text="'Email: ' + pl.email"></p>
                                                        <p x-show="pl.address" x-text="'Address: ' + pl.address"></p>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="!(cases.find(c => c.id === openId).plaintiffs && cases.find(c => c.id === openId).plaintiffs.length > 0)">
                                        <p class="text-blue-700 dark:text-blue-300 text-sm">No plaintiffs listed.</p>
                                    </template>
                                </div>

                                <!-- Defendants -->
                                <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-6 border border-red-200 dark:border-red-700">
                                    <div class="flex items-center gap-2 mb-4">
                                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <h4 class="font-semibold text-red-900 dark:text-red-100">Defendants</h4>
                                    </div>
                                    <template x-if="(cases.find(c => c.id === openId).defendants && cases.find(c => c.id === openId).defendants.length > 0)">
                                        <div class="space-y-3">
                                            <template x-for="df in cases.find(c => c.id === openId).defendants" :key="df.id">
                                                <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
                                                    <p class="font-medium text-gray-900 dark:text-white" x-text="df.name"></p>
                                                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1 space-y-1">
                                                        <p x-show="df.contact_number" x-text="'Phone: ' + df.contact_number"></p>
                                                        <p x-show="df.email" x-text="'Email: ' + df.email"></p>
                                                        <p x-show="df.address" x-text="'Address: ' + df.address"></p>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="!(cases.find(c => c.id === openId).defendants && cases.find(c => c.id === openId).defendants.length > 0)">
                                        <p class="text-red-700 dark:text-red-300 text-sm">No defendants listed.</p>
                                    </template>
                                </div>
                            </div>

                            <!-- Evidence Files -->
                            <div class="mb-8">
                                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-xl p-6 border border-purple-200 dark:border-purple-700">
                                    <div class="flex items-center gap-2 mb-4">
                                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <h4 class="font-semibold text-purple-900 dark:text-purple-100">Evidence Files</h4>
                                    </div>
                                    <template x-if="(cases.find(c => c.id === openId).evidences && cases.find(c => c.id === openId).evidences.length > 0)">
                                        <div class="space-y-2">
                                            <template x-for="ev in cases.find(c => c.id === openId).evidences" :key="ev.id">
                                                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 flex items-center justify-between">
                                                    <div>
                                                        <a :href="ev.file_path" class="text-purple-600 dark:text-purple-400 hover:underline font-medium" target="_blank" x-text="ev.file_name"></a>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400" x-text="'Uploaded: ' + ev.uploaded_at"></p>
                                                    </div>
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                    </svg>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="!(cases.find(c => c.id === openId).evidences && cases.find(c => c.id === openId).evidences.length > 0)">
                                        <p class="text-purple-700 dark:text-purple-300 text-sm">No evidence files attached.</p>
                                    </template>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex justify-end gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <button @click="openId=null" class="px-6 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl transition duration-200">
                                    Cancel
                                </button>
                                <template x-if="cases.find(c => c.id === openId)?.closure_requested_at">
                                    <form x-bind:action="`{{ route('supervisor.cases.approve', ['case' => '__ID__']) }}`.replace('__ID__', openId)" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Approve Closure
                                        </button>
                                    </form>
                                </template>
                                <template x-if="!cases.find(c => c.id === openId)?.closure_requested_at">
                                    <form x-bind:action="`{{ route('supervisor.cases.approve', ['case' => '__ID__']) }}`.replace('__ID__', openId)" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Approve Case
                                        </button>
                                    </form>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</x-app-layout>






