<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Modern Page Header -->
            <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-800 dark:to-indigo-800 rounded-3xl shadow-2xl mb-8">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.1"%3E%3Ccircle cx="30" cy="30" r="4"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>
                <div class="relative px-8 py-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/>
                                        <path d="M14 2v6h6"/>
                                        <path d="M16 13H8"/>
                                        <path d="M16 17H8"/>
                                        <path d="M10 9H8"/>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-white mb-1">{{ $case->title }}</h1>
                                <div class="flex items-center gap-3">
                                    <p class="text-white/90 text-lg">File #{{ $case->file_number }}</p>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $case->status === 'open' ? 'bg-green-500/20 text-green-200 border border-green-400/30' : 'bg-gray-500/20 text-gray-200 border border-gray-400/30' }}">
                                        <span class="w-2 h-2 {{ $case->status === 'open' ? 'bg-green-400' : 'bg-gray-400' }} rounded-full mr-2"></span>
                                        {{ ucfirst($case->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @php $isLawyer = auth()->user()->hasRole('lawyer'); @endphp
                        @if($isLawyer)
                        <div class="flex flex-col sm:flex-row gap-3">
                            <a href="{{ route('lawyer.cases.progress.create', $case) }}" 
                               class="inline-flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm border border-white/30 rounded-xl font-semibold text-sm text-white hover:bg-white/30 focus:outline-none focus:ring-2 focus:ring-white/50 transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                                </svg>
                                Add Progress
                            </a>
                            <a href="{{ route('lawyer.cases.edit', $case) }}" 
                               class="inline-flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm border border-white/30 rounded-xl font-semibold text-sm text-white hover:bg-white/30 focus:outline-none focus:ring-2 focus:ring-white/50 transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                </svg>
                                Edit Case
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Case Overview Card -->
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-3xl overflow-hidden border border-gray-200 dark:border-gray-700 mb-8">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Case Overview</h3>
                    </div>
                </div>
                <div class="p-8">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ $case->title }}
                                <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $case->status === 'open' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($case->status) }}
                                </span>
                            </h3>
                            <p class="text-sm text-gray-500">File #{{ $case->file_number }}</p>
                        </div>
                        @php $isLawyer = auth()->user()->hasRole('lawyer'); @endphp
                        @if($isLawyer)
                        <div class="flex space-x-2">
                            <a href="{{ route('lawyer.cases.progress.create', $case) }}" 
                               class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Add Progress
                            </a>
                            <a href="{{ route('lawyer.cases.edit', $case) }}" 
                               class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Edit
                            </a>
                        </div>
                        @endif
                     </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Case Details -->
                        <div class="space-y-2">
                            <h4 class="text-sm font-medium text-gray-500">Case Type</h4>
                            <p class="text-sm text-gray-900">{{ $case->caseType->name ?? 'N/A' }}</p>
                        </div>
                        <div class="space-y-2">
                            <h4 class="text-sm font-medium text-gray-500">Code</h4>
                            <p class="text-sm text-gray-900">{{ $case->code ?? '—' }}</p>
                        </div>
                        <div class="space-y-2">
                            <h4 class="text-sm font-medium text-gray-500">Status</h4>
                            <p class="text-sm text-gray-900 capitalize">{{ $case->status }}</p>
                        </div>
                        <div class="space-y-2">
                            <h4 class="text-sm font-medium text-gray-500">Opened Date</h4>
                            <p class="text-sm text-gray-900">{{ $case->opened_at?->format('M d, Y') ?? 'N/A' }}</p>
                        </div>
                        @if($case->closed_at)
                        <div class="space-y-2">
                            <h4 class="text-sm font-medium text-gray-500">Closed Date</h4>
                            <p class="text-sm text-gray-900">{{ $case->closed_at->format('M d, Y') }}</p>
                        </div>
                        @endif
                        <!-- Type-specific fields -->
                        @if($caseTypeData)
                            <div class="space-y-2">
                                <h4 class="text-sm font-medium text-gray-500">Court Name</h4>
                                <p class="text-sm text-gray-900">{{ $caseTypeData->court_name ?? $case->court_name ?? '—' }}</p>
                            </div>
                            <div class="space-y-2">
                                <h4 class="text-sm font-medium text-gray-500">Court File Number</h4>
                                <p class="text-sm text-gray-900">{{ $caseTypeData->court_file_number ?? '—' }}</p>
                            </div>
                            <div class="space-y-2">
                                <h4 class="text-sm font-medium text-gray-500">Company File Number</h4>
                                <p class="text-sm text-gray-900">{{ $case->file_number ?? '—' }}</p>
                            </div>
                            <div class="space-y-2">
                                <h4 class="text-sm font-medium text-gray-500">Claimed Amount</h4>
                                <p class="text-sm text-gray-900">ETB {{ number_format(($caseTypeData->claimed_amount ?? $case->claimed_amount ?? 0), 2) }}</p>
                            </div>
                            <div class="space-y-2">
                                <h4 class="text-sm font-medium text-gray-500">Recovered Amount</h4>
                                <p class="text-sm text-gray-900">ETB {{ number_format(($caseTypeData->recovered_amount ?? $case->recovered_amount ?? 0), 2) }}</p>
                            </div>
                            <div class="space-y-2">
                                <h4 class="text-sm font-medium text-gray-500">Outstanding Amount</h4>
                                <p class="text-sm text-gray-900">ETB {{ number_format(($caseTypeData->outstanding_amount ?? $case->outstanding_amount ?? 0), 2) }}</p>
                            </div>
                            @if(isset($caseTypeData->police_station) || isset($case->police_station))
                            <div class="space-y-2">
                                <h4 class="text-sm font-medium text-gray-500">Police Station</h4>
                                <p class="text-sm text-gray-900">{{ $caseTypeData->police_station ?? $case->police_station ?? '—' }}</p>
                            </div>
                            @endif
                            @if(isset($caseTypeData->police_file_number) || isset($case->police_file_number))
                            <div class="space-y-2">
                                <h4 class="text-sm font-medium text-gray-500">Police File Number</h4>
                                <p class="text-sm text-gray-900">{{ $caseTypeData->police_file_number ?? $case->police_file_number ?? '—' }}</p>
                            </div>
                            @endif
                            @if(isset($caseTypeData->requesting_unit) || isset($case->requesting_unit))
                            <div class="space-y-2">
                                <h4 class="text-sm font-medium text-gray-500">Requesting Unit</h4>
                                <p class="text-sm text-gray-900">{{ $caseTypeData->requesting_unit ?? $case->requesting_unit ?? '—' }}</p>
                            </div>
                            @endif
                            @if(isset($caseTypeData->advisory_type) || isset($case->advisory_type))
                            <div class="space-y-2">
                                <h4 class="text-sm font-medium text-gray-500">Advisory Type</h4>
                                <p class="text-sm text-gray-900">{{ $caseTypeData->advisory_type ?? $case->advisory_type ?? '—' }}</p>
                            </div>
                            @endif
                        @endif
                    </div>
                    <div class="mt-4">
                        <h4 class="text-sm font-medium text-gray-500">Case Description</h4>
                        <p class="text-base text-gray-900 whitespace-pre-line">{{ $case->description ?: '—' }}</p>
                    </div>
                </div>
            </div>

            <!-- Appeals Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6" x-data="{ open: true }">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center cursor-pointer" @click="open = !open">
                    <h3 class="text-lg font-medium text-gray-900">Appeal / Cassation Stages</h3>
                    <svg x-show="!open" class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    <svg x-show="open" class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                </div>
                <div class="p-6" x-show="open" x-cloak>
                    @forelse($case->appeals as $appeal)
                        <div class="mb-6 pb-4 border-b last:border-b-0 last:mb-0 last:pb-0">
                            <div class="flex items-center justify-between">
                                <h4 class="text-md font-semibold text-gray-800">{{ ucwords($appeal->level) }} Stage</h4>
                                <span class="text-xs text-gray-500">Filed {{ $appeal->created_at->format('d M Y') }}</span>
                            </div>
                            <dl class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <dt class="font-medium text-gray-500">File Number</dt>
                                    <dd class="text-gray-900">{{ $appeal->file_number }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-500">Decision Date</dt>
                                    <dd class="text-gray-900">{{ $appeal->decided_at?->format('d M Y') ?? '—' }}</dd>
                                </div>
                                @if($appeal->notes)
                                <div class="md:col-span-2">
                                    <dt class="font-medium text-gray-500">Notes</dt>
                                    <dd class="text-gray-900">{{ $appeal->notes }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No appeal stages recorded.</p>
                    @endforelse
                    <div class="mt-4">
                        <a href="{{ route('lawyer.cases.appeals.create', $case) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700">
                            + Add Stage
                        </a>
                    </div>
                </div>
            </div>

            <!-- Parties Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Plaintiffs -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Plaintiffs</h3>
                    </div>
                    <div class="p-6">
                        @forelse($case->plaintiffs as $plaintiff)
                            <div class="mb-4 pb-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                                <h4 class="font-medium text-gray-900">{{ $plaintiff->name }}</h4>
                                @if($plaintiff->contact_number)
                                    <p class="text-sm text-gray-500">{{ $plaintiff->contact_number }}</p>
                                @endif
                                @if($plaintiff->email)
                                    <p class="text-sm text-gray-500">{{ $plaintiff->email }}</p>
                                @endif
                                @if($plaintiff->address)
                                    <p class="text-sm text-gray-500">{{ $plaintiff->address }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No plaintiffs found.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Defendants -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Defendants</h3>
                    </div>
                    <div class="p-6">
                        @forelse($case->defendants as $defendant)
                            <div class="mb-4 pb-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                                <h4 class="font-medium text-gray-900">{{ $defendant->name }}</h4>
                                @if($defendant->contact_number)
                                    <p class="text-sm text-gray-500">{{ $defendant->contact_number }}</p>
                                @endif
                                @if($defendant->email)
                                    <p class="text-sm text-gray-500">{{ $defendant->email }}</p>
                                @endif
                                @if($defendant->address)
                                    <p class="text-sm text-gray-500">{{ $defendant->address }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No defendants found.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Progress Updates & Upcoming Appointments -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Progress Updates -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Recent Updates</h3>
                        <a href="{{ route('lawyer.cases.progress.create', $case) }}" 
                           class="text-sm text-blue-600 hover:text-blue-800">
                            Add Update
                        </a>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @forelse($case->progressUpdates as $update)
                            <div class="p-6">
                                <div class="flex justify-between">
                                    <div class="flex space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $update->user->name ?? 'System' }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                {{ $update->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ ucwords(str_replace('_', ' ', $update->status)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-2 text-sm text-gray-700">
                                    <p>{{ $update->notes }}</p>
                                </div>
                                @if($update->attachment_path)
                                <div class="mt-2">
                                    <a href="{{ Storage::url($update->attachment_path) }}" 
                                       target="_blank"
                                       class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                                        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        View Attachment
                                    </a>
                                </div>
                                @endif
                            </div>
                        @empty
                            <div class="p-6 text-center text-sm text-gray-500">
                                No progress updates yet.
                            </div>
                        @endforelse
                    </div>
                    @if($case->progressUpdates->count() > 5)
                    <div class="px-6 py-4 border-t border-gray-200 text-center">
                        <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                            View all updates
                        </a>
                    </div>
                    @endif
                </div>

                <!-- Upcoming Appointments -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Upcoming Appointments</h3>
                        <a href="{{ route('lawyer.cases.appointments.create', $case) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Appointment
                        </a>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @forelse($case->appointments as $appointment)
                            <div class="p-6">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 pt-0.5">
                                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-100 text-blue-800">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $appointment->title }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $appointment->appointment_date->format('M d, Y') }} at {{ $appointment->appointment_time }}
                                        </p>
                                        @if($appointment->location)
                                        <p class="text-sm text-gray-500 mt-1">
                                            <svg class="h-4 w-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ $appointment->location }}
                                        </p>
                                        @endif
                                        @if($appointment->notes)
                                        <p class="mt-2 text-sm text-gray-600">
                                            {{ $appointment->notes }}
                                        </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-sm text-gray-500">
                                No upcoming appointments.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Case Type Specific Information -->
            @if($caseTypeData)
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ ucwords(str_replace('_', ' ', $case->caseType->name)) }} Details
                    </h3>
                </div>
                <div class="p-6">
                    @includeIf('lawyer.cases.partials.' . str_replace(' ', '_', strtolower($case->caseType->name)))
                </div>
            </div>
            @endif
        </div>
        <!-- Action Log / Edit History -->
        <div class="mt-10 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Edit History</h3>
                <a href="{{ route('lawyer.cases.edit-history', $case) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    View Edit History
                </a>
            </div>
        </div>
    </div>
</x-app-layout>






