<x-app-layout>
    <x-case-show-header :case="$case" />

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Advisory Case Overview -->
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

                    <!-- Advisory Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-sm">
                        @if($caseTypeData)
                            <div>
                                <dt class="font-medium text-gray-500">Advisory Type</dt>
                                <dd class="text-gray-900">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $caseTypeData->advisory_type === 'written_advice' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                        {{ $caseTypeData->advisory_type === 'written_advice' ? 'Written Advice' : 'Document Review' }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Subject</dt>
                                <dd class="text-gray-900">{{ $caseTypeData->subject ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Request Date</dt>
                                <dd class="text-gray-900">{{ $caseTypeData->request_date ? \Carbon\Carbon::parse($caseTypeData->request_date)->format('M d, Y') : '—' }}</dd>
                            </div>
                            @if($caseTypeData->requesting_department)
                                <div>
                                    <dt class="font-medium text-gray-500">Requesting Department</dt>
                                    <dd class="text-gray-900">{{ $caseTypeData->requesting_department }}</dd>
                                </div>
                            @endif
                            @if($caseTypeData->work_unit_advised)
                                <div>
                                    <dt class="font-medium text-gray-500">Work Unit Advised</dt>
                                    <dd class="text-gray-900">{{ $caseTypeData->work_unit_advised }}</dd>
                                </div>
                            @endif
                            @if($caseTypeData->reference_number)
                                <div>
                                    <dt class="font-medium text-gray-500">Reference Number</dt>
                                    <dd class="text-gray-900">{{ $caseTypeData->reference_number }}</dd>
                                </div>
                            @endif
                        @endif
                        <div>
                            <dt class="font-medium text-gray-500">Opened Date</dt>
                            <dd class="text-gray-900">{{ $case->opened_at?->format('M d, Y') ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Status</dt>
                            <dd class="text-gray-900 capitalize">{{ $case->status }}</dd>
                        </div>
                        @if($caseTypeData && $caseTypeData->is_own_motion)
                            <div>
                                <dt class="font-medium text-gray-500">Own Motion</dt>
                                <dd class="text-gray-900">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Yes
                                    </span>
                                </dd>
                            </div>
                        @endif
                    </div>

                    @if($case->description)
                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Case Description</h4>
                            <p class="text-base text-gray-900 whitespace-pre-line">{{ $case->description }}</p>
                        </div>
                    @endif

                    @if($caseTypeData && $caseTypeData->review_notes)
                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Review Notes</h4>
                            <p class="text-base text-gray-900 whitespace-pre-line">{{ $caseTypeData->review_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Stakeholders Section -->
            @if($caseTypeData && $caseTypeData->stakeholders && $caseTypeData->stakeholders->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Stakeholders</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($caseTypeData->stakeholders as $stakeholder)
                                <div class="mb-3 p-4 bg-gray-50 rounded-lg">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="font-medium text-gray-900">{{ $stakeholder->name }}</div>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $stakeholder->type === 'requester' ? 'bg-blue-100 text-blue-800' : ($stakeholder->type === 'reviewer' ? 'bg-green-100 text-green-800' : ($stakeholder->type === 'approver' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800')) }}">
                                            {{ ucfirst($stakeholder->type) }}
                                        </span>
                                    </div>
                                    @if($stakeholder->organization)
                                        <div class="text-sm text-gray-600 mb-1">{{ $stakeholder->organization }}</div>
                                    @endif
                                    @if($stakeholder->email)
                                        <div class="text-sm text-gray-600">Email: {{ $stakeholder->email }}</div>
                                    @endif
                                    @if($stakeholder->phone)
                                        <div class="text-sm text-gray-600">Phone: {{ $stakeholder->phone }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Document Section (for document review cases) -->
            @if($caseTypeData && $caseTypeData->document_path)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Document Under Review</h4>
                        <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                            <svg class="w-8 h-8 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <a href="{{ Storage::url($caseTypeData->document_path) }}" target="_blank" class="text-blue-600 hover:underline font-medium">
                                    View Document
                                </a>
                                <div class="text-xs text-gray-500">
                                    Uploaded for review
                                </div>
                            </div>
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






