<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <svg data-lucide="folder-open" class="w-8 h-8 text-blue-600"></svg>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                Case Detail - {{ $case->file_number }}
            </h2>
            <span class="ml-4 inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                {{ $case->caseType->name ?? '—' }}
            </span>
        </div>
    </x-slot>

    <div class="mt-4">
        <h4 class="text-sm font-medium text-gray-500">Case Description</h4>
        <p class="text-base text-gray-900 whitespace-pre-line">{{ $case->description ?: '—' }}</p>
    </div>

    <div class="mb-4">
        <span class="font-medium text-gray-700">Created By:</span>
        <span class="text-gray-900">{{ $case->creator->name ?? 'Unknown' }}</span>
    </div>

    <div class="py-8">
        <div class="max-w-5xl mx-auto space-y-10">
            <!-- Case Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <x-ui.stat-card title="Status" :value="' '" icon="info" color="blue">
                    <span class="text-base font-semibold text-blue-700">{{ $case->status }}</span>
                </x-ui.stat-card>
                <x-ui.stat-card title="Claimed Amount" :value="$case->claimed_amount ?? 0" icon="dollar-sign" color="green" />
                <x-ui.stat-card title="Recovered" :value="$case->recovered_amount ?? 0" icon="check-circle" color="gray" />
                <x-ui.stat-card title="Assigned Lawyer" :value="' '" icon="gavel" color="yellow">
                    <span class="text-base font-semibold text-yellow-700">{{ $case->lawyer->name ?? '—' }}</span>
                </x-ui.stat-card>
            </div>

            <!-- General Info -->
            <div class="bg-white shadow rounded-2xl p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <svg data-lucide="info" class="w-5 h-5"></svg>
                    General Information
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4 text-sm">
                    <div><span class="font-medium">Co. File #:</span> {{ $case->file_number }}</div>
                    <div><span class="font-medium">Court File #:</span> {{ $case->court_file_no ?? '—' }}</div>
                    <div><span class="font-medium">Case Type:</span> {{ $case->caseType->name ?? '—' }}</div>
                    <div><span class="font-medium">Plaintiff(s):</span> {{ $case->plaintiffs->pluck('name')->implode(', ') ?: '—' }}</div>
                    <div><span class="font-medium">Defendant(s):</span> {{ $case->defendants->pluck('name')->implode(', ') ?: '—' }}</div>
                    <div><span class="font-medium">Branch:</span> {{ $case->branch->name ?? '—' }}</div>
                    <div><span class="font-medium">Court:</span> {{ $case->court->name ?? '—' }}</div>
                    <div><span class="font-medium">Opened At:</span> {{ $case->opened_at?->format('d M Y') ?? '—' }}</div>
                    <div><span class="font-medium">Closed At:</span> {{ $case->closed_at?->format('d M Y') ?? '—' }}</div>
                </div>
            </div>

            <!-- Type-Specific Details -->
            <div class="bg-white shadow rounded-2xl p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <svg data-lucide="layers" class="w-5 h-5"></svg>
                    Type-Specific Details
                </h3>
                @php $code = $case->caseType->code ?? null; @endphp
                @if($code === '00' && $case->litigation)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><span class="font-medium">Outstanding Amount:</span> {{ $case->litigation->outstanding_amount ?? '—' }}</div>
                        <div><span class="font-medium">Recovered Amount:</span> {{ $case->litigation->recovered_amount ?? '—' }}</div>
                        <div><span class="font-medium">Execution Opened At:</span> {{ $case->litigation->execution_opened_at ?? '—' }}</div>
                        <div><span class="font-medium">Early Closed:</span> {{ $case->litigation->early_closed ? 'Yes' : 'No' }}</div>
                    </div>
                @elseif($code === '02' && $case->laborLitigation)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><span class="font-medium">Claim Type:</span> {{ $case->laborLitigation->claim_type ?? '—' }}</div>
                        <div><span class="font-medium">Claim Amount:</span> {{ $case->laborLitigation->claim_amount ?? '—' }}</div>
                        <div><span class="font-medium">Recovered Amount:</span> {{ $case->laborLitigation->recovered_amount ?? '—' }}</div>
                        <div><span class="font-medium">Early Settled:</span> {{ $case->laborLitigation->early_settled ? 'Yes' : 'No' }}</div>
                    </div>
                @elseif($code === '03' && $case->otherCivilLitigation)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><span class="font-medium">Claim Type:</span> {{ $case->otherCivilLitigation->claim_type ?? '—' }}</div>
                        <div><span class="font-medium">Claim Amount:</span> {{ $case->otherCivilLitigation->claim_amount ?? '—' }}</div>
                        <div><span class="font-medium">Recovered Amount:</span> {{ $case->otherCivilLitigation->recovered_amount ?? '—' }}</div>
                        <div><span class="font-medium">Early Settled:</span> {{ $case->otherCivilLitigation->early_settled ? 'Yes' : 'No' }}</div>
                    </div>
                @elseif($code === '04' && $case->criminalLitigation)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><span class="font-medium">Police Ref #:</span> {{ $case->criminalLitigation->police_ref_no ?? '—' }}</div>
                        <div><span class="font-medium">Prosecutor Ref #:</span> {{ $case->criminalLitigation->prosecutor_ref_no ?? '—' }}</div>
                        <div><span class="font-medium">Evidence Summary:</span> {{ $case->criminalLitigation->evidence_summary ?? '—' }}</div>
                        <div><span class="font-medium">Recovered Amount:</span> {{ $case->criminalLitigation->recovered_amount ?? '—' }}</div>
                    </div>
                @elseif($code === '05' && $case->securedLoanRecovery)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><span class="font-medium">Loan Amount:</span> {{ $case->securedLoanRecovery->loan_amount ?? '—' }}</div>
                        <div><span class="font-medium">Outstanding Amount:</span> {{ $case->securedLoanRecovery->outstanding_amount ?? '—' }}</div>
                        <div><span class="font-medium">Collateral Value:</span> {{ $case->securedLoanRecovery->collateral_value ?? '—' }}</div>
                        <div><span class="font-medium">Recovered Amount:</span> {{ $case->securedLoanRecovery->recovered_amount ?? '—' }}</div>
                        <div><span class="font-medium">Closure Type:</span> {{ $case->securedLoanRecovery->closure_type ?? '—' }}</div>
                    </div>
                @elseif($code === '06' && $case->legalAdvisory)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><span class="font-medium">Advisory Type:</span> {{ $case->legalAdvisory->advisory_type ?? '—' }}</div>
                        <div><span class="font-medium">Subject:</span> {{ $case->legalAdvisory->subject ?? '—' }}</div>
                        <div><span class="font-medium">Status:</span> {{ $case->legalAdvisory->status ?? '—' }}</div>
                        <div><span class="font-medium">Request Date:</span> {{ $case->legalAdvisory->request_date ?? '—' }}</div>
                        <div><span class="font-medium">Submission Date:</span> {{ $case->legalAdvisory->submission_date ?? '—' }}</div>
                    </div>
                @elseif($code === '01' && $case->cleanLoanRecovery)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><span class="font-medium">Outstanding Amount:</span> {{ $case->cleanLoanRecovery->outstanding_amount ?? '—' }}</div>
                        <div><span class="font-medium">Recovered Amount:</span> {{ $case->cleanLoanRecovery->recovered_amount ?? '—' }}</div>
                    </div>
                @else
                    <div class="text-gray-500">No additional details for this case type.</div>
                @endif
            </div>

            <!-- Progress Timeline -->
            <div class="bg-white shadow rounded-2xl p-6">
                <h3 class="text-lg font-semibold flex items-center gap-2 mb-4">
                    <svg data-lucide="list" class="w-5 h-5"></svg>
                    Progress Timeline
                </h3>
                <x-progress.timeline :updates="$updates" />
            </div>
        </div>
    </div>

    <div class="mt-10 bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">Edit History</h3>
            <a href="{{ route('admin.cases.edit-history', $case) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                View Edit History
            </a>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.onComponentInitialized(() => {
                    lucide.createIcons();
                })
            });
        </script>
    @endpush
</x-app-layout> 





