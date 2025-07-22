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

            <!-- Type-Specific Details (Dynamic) -->
            <div class="bg-white shadow rounded-2xl p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <svg data-lucide="layers" class="w-5 h-5"></svg>
                    Type-Specific Details
                </h3>
                @php
                    $type = strtolower($case->caseType->code ?? $case->caseType->name ?? '');
                    $typeMap = [
                        'litigation' => 'litigation',
                        'labor' => 'laborLitigation',
                        'othercivil' => 'otherCivilLitigation',
                        'criminal' => 'criminalLitigation',
                        'securedloan' => 'securedLoanRecovery',
                        'advisory' => 'legalAdvisory',
                        'cleanloan' => 'cleanLoanRecovery',
                    ];
                    $relation = $typeMap[$type] ?? null;
                    $typeData = $relation ? $case->{$relation} : null;
                @endphp
                @if($typeData)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($typeData->getAttributes() as $key => $value)
                            @continue(in_array($key, ['id','case_file_id','created_at','updated_at','deleted_at']))
                            <div><span class="font-medium">{{ ucwords(str_replace(['_', '-'], ' ', $key)) }}:</span> {{ is_bool($value) ? ($value ? 'Yes' : 'No') : ($value ?: '—') }}</div>
                        @endforeach
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






