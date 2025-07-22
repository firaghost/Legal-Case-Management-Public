{{-- Dynamic Case Details Based on Case Type --}}
@php
    $caseTypeCode = $case->caseType->code ?? '';
    $caseData = null;
    
    // Get case-specific data based on type
    switch($caseTypeCode) {
        case 'LIT':
            $caseData = $case->litigation;
            break;
        case 'LAB':
            $caseData = $case->laborLitigation;
            break;
        case 'OCL':
            $caseData = $case->otherCivilLitigation;
            break;
        case 'CRM':
            $caseData = $case->criminalLitigation;
            break;
        case 'SLR':
            $caseData = $case->securedLoanRecovery;
            break;
        case 'ADV':
            $caseData = $case->legalAdvisory;
            break;
        case 'CLR':
            $caseData = $case->cleanLoanRecovery;
            break;
    }
@endphp

<div class="space-y-6">
    {{-- Basic Case Information --}}
    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-6">
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Basic Information
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">File Number</label>
                <p class="text-gray-900 dark:text-white font-medium">{{ $case->file_number }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Case Type</label>
                <p class="text-gray-900 dark:text-white font-medium">{{ $case->caseType->name ?? 'N/A' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Status</label>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    {{ $case->status }}
                </span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Branch</label>
                <p class="text-gray-900 dark:text-white">{{ $case->branch->name ?? 'N/A' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Work Unit</label>
                <p class="text-gray-900 dark:text-white">{{ $case->workUnit->name ?? 'N/A' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Assigned Lawyer</label>
                <p class="text-gray-900 dark:text-white">{{ $case->lawyer->name ?? 'Unassigned' }}</p>
            </div>
        </div>
    </div>

    {{-- Financial Information --}}
    <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-6">
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
            </svg>
            Financial Details
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Claimed Amount</label>
                <p class="text-lg font-bold text-green-700 dark:text-green-400">
                    {{ number_format($case->claimed_amount ?? 0, 2) }} ETB
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Recovered Amount</label>
                <p class="text-lg font-bold text-blue-700 dark:text-blue-400">
                    {{ number_format($case->recovered_amount ?? 0, 2) }} ETB
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Outstanding Amount</label>
                <p class="text-lg font-bold text-red-700 dark:text-red-400">
                    {{ number_format($case->outstanding_amount ?? 0, 2) }} ETB
                </p>
            </div>
        </div>
    </div>

    {{-- Case Type Specific Details --}}
    @if($caseData)
        @switch($caseTypeCode)
            @case('SLR')
                {{-- Secured Loan Recovery Details --}}
                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-xl p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Secured Loan Recovery Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Customer Name</label>
                            <p class="text-gray-900 dark:text-white">{{ $caseData->customer_name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Loan Amount</label>
                            <p class="text-gray-900 dark:text-white font-medium">{{ number_format($caseData->loan_amount ?? 0, 2) }} ETB</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Collateral Value</label>
                            <p class="text-gray-900 dark:text-white font-medium">{{ number_format($caseData->collateral_value ?? 0, 2) }} ETB</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Foreclosure Notice Date</label>
                            <p class="text-gray-900 dark:text-white">{{ $caseData->foreclosure_notice_date ? $caseData->foreclosure_notice_date->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Collateral Description</label>
                            <p class="text-gray-900 dark:text-white">{{ $caseData->collateral_description ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Auction Status</label>
                            <div class="flex gap-2">
                                @if($caseData->first_auction_held)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">1st Auction Held</span>
                                @endif
                                @if($caseData->second_auction_held)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">2nd Auction Held</span>
                                @endif
                                @if(!$caseData->first_auction_held && !$caseData->second_auction_held)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">No Auctions</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @break

            @case('LAB')
                {{-- Labor Litigation Details --}}
                <div class="bg-orange-50 dark:bg-orange-900/20 rounded-xl p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Labor Litigation Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Claim Type</label>
                            <p class="text-gray-900 dark:text-white">{{ $caseData->claim_type ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Claim Amount</label>
                            <p class="text-gray-900 dark:text-white font-medium">{{ number_format($caseData->claim_amount ?? 0, 2) }} ETB</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Execution Opened</label>
                            <p class="text-gray-900 dark:text-white">{{ $caseData->execution_opened_at ? $caseData->execution_opened_at->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Early Settlement</label>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $caseData->early_settled ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $caseData->early_settled ? 'Yes' : 'No' }}
                            </span>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Claim Material Description</label>
                            <p class="text-gray-900 dark:text-white">{{ $caseData->claim_material_desc ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                @break

            @case('LIT')
                {{-- General Litigation Details --}}
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Litigation Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Internal File No</label>
                            <p class="text-gray-900 dark:text-white">{{ $caseData->internal_file_no ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Execution Opened</label>
                            <p class="text-gray-900 dark:text-white">{{ $caseData->execution_opened_at ? $caseData->execution_opened_at->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Early Closure</label>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $caseData->early_closed ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $caseData->early_closed ? 'Yes' : 'No' }}
                            </span>
                        </div>
                    </div>
                </div>
                @break

            @default
                {{-- Generic case type details --}}
                <div class="bg-gray-50 dark:bg-gray-900/20 rounded-xl p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Case Type Specific Details</h4>
                    <p class="text-gray-600 dark:text-gray-400">Additional details for {{ $case->caseType->name ?? 'this case type' }} will be displayed here.</p>
                </div>
        @endswitch
    @endif

    {{-- Parties Information --}}
    @if($case->plaintiffs->count() > 0 || $case->defendants->count() > 0)
        <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-xl p-6">
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Parties Involved
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($case->plaintiffs->count() > 0)
                    <div>
                        <h5 class="font-medium text-gray-900 dark:text-white mb-3">Plaintiffs</h5>
                        @foreach($case->plaintiffs as $plaintiff)
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-3 mb-2">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $plaintiff->name }}</p>
                                @if($plaintiff->contact_number)
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $plaintiff->contact_number }}</p>
                                @endif
                                @if($plaintiff->email)
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $plaintiff->email }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
                @if($case->defendants->count() > 0)
                    <div>
                        <h5 class="font-medium text-gray-900 dark:text-white mb-3">Defendants</h5>
                        @foreach($case->defendants as $defendant)
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-3 mb-2">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $defendant->name }}</p>
                                @if($defendant->contact_number)
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $defendant->contact_number }}</p>
                                @endif
                                @if($defendant->email)
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $defendant->email }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Recent Progress Updates --}}
    @if($case->progressUpdates->count() > 0)
        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-xl p-6">
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                Recent Progress Updates ({{ $case->progressUpdates->count() }})
            </h4>
            <div class="space-y-3 max-h-64 overflow-y-auto">
                @foreach($case->progressUpdates->take(5) as $update)
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-3">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $update->created_at->format('M d, Y') }}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $update->created_at->diffForHumans() }}
                            </span>
                        </div>
                        @if($update->status)
                            <p class="text-xs font-medium text-blue-600 dark:text-blue-400 mb-1">Status: {{ $update->status }}</p>
                        @endif
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $update->notes ?? 'No notes provided.' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Upcoming Appointments --}}
    @if($case->appointments->where('appointment_date', '>=', now())->count() > 0)
        <div class="bg-teal-50 dark:bg-teal-900/20 rounded-xl p-6">
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Upcoming Appointments
            </h4>
            <div class="space-y-3">
                @foreach($case->appointments->where('appointment_date', '>=', now())->take(3) as $appointment)
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $appointment->purpose }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $appointment->appointment_date->format('M d, Y') }} 
                                    @if($appointment->appointment_time)
                                        at {{ $appointment->appointment_time }}
                                    @endif
                                </p>
                                @if($appointment->location)
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $appointment->location }}</p>
                                @endif
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-teal-100 text-teal-800">
                                {{ $appointment->status }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>






