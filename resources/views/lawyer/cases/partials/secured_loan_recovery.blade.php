@php /** @var \App\Models\SecuredLoanRecoveryCase|null $caseTypeData */ @endphp
{{-- Detailed grid removed; values now shown in header --}}
    <!-- Closure -->
    <div>
        <dt class="font-medium text-gray-500">Closure Type</dt>
        <dd class="text-gray-900">
            @if($caseTypeData?->closure_type)
                {{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $caseTypeData->closure_type)) }}
            @else
                —
            @endif
        </dd>
    </div>
    <!-- Warning & Collateral -->
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
    <div>
        <dt class="font-medium text-gray-500">Collateral Estimate</dt>
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
        <dd class="text-gray-900">
                @if($caseTypeData && $caseTypeData->auctions->isNotEmpty())
                    @php $latest = $caseTypeData->auctions->sortByDesc('round')->first(); @endphp
                    Round {{ $latest->round }} @if($latest->auction_date) ({{ $latest->auction_date->toDateString() }}) @endif
                    @if($latest->result)
                        - {{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $latest->result)) }}
                    @endif
                @else
                    —
                @endif
            </dd>
    </div>

    {{-- Auction Details Table --}}
    <div class="md:col-span-2 mt-6">
        <h4 class="text-md font-semibold text-gray-700 mb-2">Auction Details</h4>
        @if($caseTypeData && $caseTypeData->auctions->isNotEmpty())
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-medium text-gray-600">Round</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-600">Date</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-600">Result</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-600">Sold Amount</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-600">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach($caseTypeData->auctions->sortBy('round') as $auction)
                            <tr>
                                <td class="px-4 py-2">Round {{ $auction->round }}</td>
                                <td class="px-4 py-2">{{ $auction->auction_date?->toDateString() ?? '—' }}</td>
                                <td class="px-4 py-2">{{ $auction->result ? \Illuminate\Support\Str::headline(str_replace('_', ' ', $auction->result)) : '—' }}</td>
                                <td class="px-4 py-2">{{ $auction->sold_amount ? number_format($auction->sold_amount, 2) : '—' }}</td>
                                <td class="px-4 py-2">{{ $auction->notes ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500">—</p>
        @endif
    </div>
    {{-- Evidences Section --}}
    <div class="md:col-span-2 mt-6">
        <h4 class="text-md font-semibold text-gray-700 mb-2">Uploaded Evidences</h4>
        @if($case->evidences->isNotEmpty())
            <ul class="list-disc pl-5 space-y-1">
                @foreach($case->evidences as $evidence)
                    <li>
                        <a href="{{ Storage::url($evidence->file_path) }}" class="text-indigo-600 underline" target="_blank">
                            {{ basename($evidence->file_path) }}
                        </a>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-500">—</p>
        @endif
    </div>
<x-recovery-timeline :case="$case" />






