<x-app-layout>
    <x-case-show-header :case="$case" />

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Custom Clean Loan Overview -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ $case->title }}
                                <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $case->status === 'open' ? 'bg-green-100 text-green-800' : ($case->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
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

                    <!-- Clean loan specific header grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-sm">
                        <div>
                            <dt class="font-medium text-gray-500">Branch</dt>
                            <dd class="text-gray-900">{{ $case->branch->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Work Unit</dt>
                            <dd class="text-gray-900">{{ $case->workUnit->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Court Name</dt>
                            <dd class="text-gray-900">{{ $caseTypeData->court_name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Court File Number</dt>
                            <dd class="text-gray-900">{{ $caseTypeData->court_file_number ?? $case->court_file_number ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Company File Number</dt>
                            <dd class="text-gray-900">{{ $caseTypeData->company_file_number ?? $case->company_file_number ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Customer Name</dt>
                            <dd class="text-gray-900">{{ $caseTypeData->customer_name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Opened Date</dt>
                            <dd class="text-gray-900">{{ $case->opened_at?->format('M d, Y') ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Status</dt>
                            <dd class="text-gray-900 capitalize">{{ $case->status }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Claimed Amount</dt>
                            <dd class="text-gray-900">ETB {{ number_format($caseTypeData->claimed_amount ?? $case->claimed_amount ?? 0, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Recovered Amount</dt>
                            <dd class="text-gray-900">ETB {{ number_format($caseTypeData->recovered_amount ?? 0, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Outstanding Amount</dt>
                            <dd class="text-gray-900">ETB {{ number_format($caseTypeData->outstanding_amount ?? (($caseTypeData->claimed_amount ?? 0) - ($caseTypeData->recovered_amount ?? 0)), 2) }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Loan Amount</dt>
                            <dd class="text-gray-900">ETB {{ number_format($caseTypeData->loan_amount ?? 0, 2) }}</dd>
                        </div>
                    </div>

                    @if($case->description)
                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Case Description</h4>
                            <p class="text-base text-gray-900 whitespace-pre-line">{{ $case->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Parties Section -->
            @if($case->plaintiffs->count() > 0 || $case->defendants->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Parties Involved</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if($case->plaintiffs->count() > 0)
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500 mb-3">Plaintiffs</h5>
                                    @foreach($case->plaintiffs as $plaintiff)
                                        <div class="mb-3 p-3 bg-gray-50 rounded-lg">
                                            <div class="font-medium text-gray-900">{{ $plaintiff->name }}</div>
                                            @if($plaintiff->contact_number)
                                                <div class="text-sm text-gray-600">Phone: {{ $plaintiff->contact_number }}</div>
                                            @endif
                                            @if($plaintiff->email)
                                                <div class="text-sm text-gray-600">Email: {{ $plaintiff->email }}</div>
                                            @endif
                                            @if($plaintiff->address)
                                                <div class="text-sm text-gray-600">Address: {{ $plaintiff->address }}</div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if($case->defendants->count() > 0)
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500 mb-3">Defendants</h5>
                                    @foreach($case->defendants as $defendant)
                                        <div class="mb-3 p-3 bg-gray-50 rounded-lg">
                                            <div class="font-medium text-gray-900">{{ $defendant->name }}</div>
                                            @if($defendant->contact_number)
                                                <div class="text-sm text-gray-600">Phone: {{ $defendant->contact_number }}</div>
                                            @endif
                                            @if($defendant->email)
                                                <div class="text-sm text-gray-600">Email: {{ $defendant->email }}</div>
                                            @endif
                                            @if($defendant->address)
                                                <div class="text-sm text-gray-600">Address: {{ $defendant->address }}</div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Evidence Section -->
            @if($case->evidences && $case->evidences->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Evidence & Documents</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($case->evidences as $evidence)
                                <div class="border rounded-lg p-4">
                                    <div class="flex items-center">
                                        <svg class="w-8 h-8 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                        </svg>
                                        <div>
                                            <a href="{{ $evidence->file_path }}" target="_blank" class="text-blue-600 hover:underline font-medium">
                                                {{ $evidence->file_name }}
                                            </a>
                                            <div class="text-xs text-gray-500">
                                                {{ $evidence->uploaded_at->format('M d, Y H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Re-use generic sections -->
            @include('lawyer.cases.sections.appeals')
            @include('lawyer.cases.sections.updates_appointments')
            @include('lawyer.cases.sections.edit_history')
        </div>
    </div>
</x-app-layout>






