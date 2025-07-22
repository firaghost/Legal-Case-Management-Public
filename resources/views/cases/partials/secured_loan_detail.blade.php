<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Secured Loan Recovery Details</h3>
    </div>
    <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-sm">
        <!-- Branch & Work Unit -->
        <div>
            <dt class="font-medium text-gray-500">Base Branch</dt>
            <dd class="text-gray-900">{{ $case->branch->name ?? '—' }}</dd>
        </div>
        <div>
            <dt class="font-medium text-gray-500">Work Unit</dt>
            <dd class="text-gray-900">{{ $caseTypeData->work_unit_id ? optional($caseTypeData->workUnit)->name : '—' }}</dd>
        </div>
        <!-- Customer & File Numbers -->
        <div>
            <dt class="font-medium text-gray-500">Customer Name</dt>
            <dd class="text-gray-900">{{ $caseTypeData->customer_name ?? $case->customer_name ?? '—' }}</dd>
        </div>
        <div>
            <dt class="font-medium text-gray-500">Company File #</dt>
            <dd class="text-gray-900">{{ $caseTypeData->company_file_number ?? $case->company_file_number ?? '—' }}</dd>
        </div>
        <div>
            <dt class="font-medium text-gray-500">Court File #</dt>
            <dd class="text-gray-900">{{ $caseTypeData->court_file_number ?? '—' }}</dd>
        </div>
        <!-- Financials -->
        <div>
            <dt class="font-medium text-gray-500">Claimed Amount</dt>
            <dd class="text-gray-900">ETB {{ number_format($caseTypeData->claimed_amount ?? 0, 2) }}</dd>
        </div>
        <div>
            <dt class="font-medium text-gray-500">Recovered Amount</dt>
            <dd class="text-gray-900">ETB {{ number_format($caseTypeData->recovered_amount ?? 0, 2) }}</dd>
        </div>
        <div>
            <dt class="font-medium text-gray-500">Outstanding Balance</dt>
            <dd class="text-gray-900">ETB {{ number_format(($caseTypeData->claimed_amount - $caseTypeData->recovered_amount), 2) }}</dd>
        </div>
        <!-- Closure & Status -->
        <div>
            <dt class="font-medium text-gray-500">Closure Type</dt>
            <dd class="text-gray-900">{{ $caseTypeData->closure_type ?? '—' }}</dd>
        </div>
        <!-- Warnings -->
        <div>
            <dt class="font-medium text-gray-500">30-Day Foreclosure Warning</dt>
            <dd class="text-gray-900">
                @if($caseTypeData->warning_doc_path)
                <a class="text-indigo-600 underline" href="{{ Storage::url($caseTypeData->warning_doc_path) }}" target="_blank">view</a>
                @else
                    —
                @endif
            </dd>
        </div>
        <!-- Collateral & Auction -->
        <div>
            <dt class="font-medium text-gray-500">Collateral Estimation</dt>
            <dd class="text-gray-900">
                @if($caseTypeData->collateral_estimation_path)
                    <a class="text-indigo-600 underline" href="{{ Storage::url($caseTypeData->collateral_estimation_path) }}" target="_blank">view</a>
                @else
                    —
                @endif
            </dd>
        </div>
        <div>
            <dt class="font-medium text-gray-500">Auction Publication</dt>
            <dd class="text-gray-900">
                @if($caseTypeData->auction_publication_path)
                    <a class="text-indigo-600 underline" href="{{ Storage::url($caseTypeData->auction_publication_path) }}" target="_blank">view</a>
                @else
                    —
                @endif
            </dd>
        </div>
        <div>
            <dt class="font-medium text-gray-500">Auction Round</dt>
            <dd class="text-gray-900">{{ $caseTypeData->auction_round ?? 'None' }}</dd>
        </div>
    </div>
    <div class="px-6 pb-6">
        <x-recovery-timeline :case="$case" />
    </div>
</div>






