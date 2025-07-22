<x-app-layout>
    <x-case-show-header :case="$case" />

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Custom Secured-Loan Overview -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ $case->title }}
                                <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $case->status === 'open' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($case->status) }}
                                </span>
                            </h3>
                            <p class="text-sm text-gray-500">File #{{ $case->file_number }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('lawyer.cases.progress.create', $case) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">Add Progress</a>
                            <a href="{{ route('lawyer.cases.edit', $case) }}" class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Edit</a>
                        </div>
                    </div>

                    <!-- Secured-loan specific header grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-sm">
                        <div>
                            <dt class="font-medium text-gray-500">Base Branch</dt>
                            <dd class="text-gray-900">{{ $case->branch->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Customer Name</dt>
                            <dd class="text-gray-900">{{ $caseTypeData->customer_name ?? $case->customer_name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Status</dt>
                            <dd class="text-gray-900 capitalize">{{ $case->status }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Opened Date</dt>
                            <dd class="text-gray-900">{{ $case->opened_at?->format('M d, Y') ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Company File #</dt>
                            <dd class="text-gray-900">{{ $caseTypeData->company_file_number ?? $case->company_file_number ?? '—' }}</dd>
                        </div>
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
                    </div>

                    @if($case->description)
                        <div class="mt-4">
                            <h4 class="text-sm font-medium text-gray-500">Case Description</h4>
                            <p class="text-base text-gray-900 whitespace-pre-line">{{ $case->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Re-use generic sections (appeals, parties, updates, appointments) by including partials from generic show -->
            @include('lawyer.cases.sections.appeals')
            
            @include('lawyer.cases.sections.updates_appointments')

            <!-- Secured-loan Detail block -->
            @include('lawyer.cases.partials.secured_loan_recovery')

            <!-- Edit history section -->
            @include('lawyer.cases.sections.edit_history')
        </div>
    </div>
</x-app-layout>






