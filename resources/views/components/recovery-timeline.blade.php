{{-- RecoveryTimeline Component --}}
{{-- Displays step-based timeline for a secured loan recovery case. --}}
@props(['case' => null])

@php
    // Helper to render date & user
    function stepMeta($date, $userId) {
        return $date ? (e($date->format('Y-m-d')) . ' | ID: ' . e($userId)) : '—';
    }
@endphp

<div class="bg-white shadow rounded-lg p-4">
    <h3 class="text-lg font-semibold mb-4">Recovery Timeline</h3>

    @if(!$case)
        <p class="text-sm text-gray-500">No case data provided. Timeline preview unavailable.</p>
    @else
        <ul class="space-y-3">
            <li class="flex items-start">
                <span class="w-6 h-6 flex items-center justify-center rounded-full bg-emerald-600 text-white text-xs mr-3">1</span>
                <div>
                    <p class="font-medium">Case Registered</p>
                    <p class="text-xs text-gray-500">{{ stepMeta($case->created_at, $case->lawyer_id) }}</p>
                </div>
            </li>
            @if($case->foreclosure_warning)
                <li class="flex items-start">
                    <span class="w-6 h-6 flex items-center justify-center rounded-full bg-emerald-600 text-white text-xs mr-3">2</span>
                    <div>
                        <p class="font-medium">30-Day Warning</p>
                        <p class="text-xs text-gray-500">{{ stepMeta($case->warning_doc_uploaded_at ?? null, $case->warning_doc_uploaded_by ?? $case->lawyer_id) }}</p>
                    </div>
                </li>
            @endif
            @if($case->collateral_estimation_path)
                <li class="flex items-start">
                    <span class="w-6 h-6 flex items-center justify-center rounded-full bg-emerald-600 text-white text-xs mr-3">3</span>
                    <div>
                        <p class="font-medium">Collateral Estimated</p>
                        <p class="text-xs text-gray-500">{{ stepMeta($case->collateral_estimation_uploaded_at ?? null, $case->lawyer_id) }}</p>
                    </div>
                </li>
            @endif
            @if($case->auction_round == 1)
                <li class="flex items-start">
                    <span class="w-6 h-6 flex items-center justify-center rounded-full bg-emerald-600 text-white text-xs mr-3">4</span>
                    <div>
                        <p class="font-medium">Auction Round 1</p>
                        <p class="text-xs text-gray-500">{{ stepMeta($case->auction_round1_at ?? null, $case->lawyer_id) }}</p>
                    </div>
                </li>
            @endif
            @if($case->auction_round == 2)
                <li class="flex items-start">
                    <span class="w-6 h-6 flex items-center justify-center rounded-full bg-emerald-600 text-white text-xs mr-3">5</span>
                    <div>
                        <p class="font-medium">Auction Round 2</p>
                        <p class="text-xs text-gray-500">{{ stepMeta($case->auction_round2_at ?? null, $case->lawyer_id) }}</p>
                    </div>
                </li>
            @endif
            <li class="flex items-start">
                <span class="w-6 h-6 flex items-center justify-center rounded-full bg-emerald-600 text-white text-xs mr-3">6</span>
                <div>
                    <p class="font-medium">Amount Recovered</p>
                    <p class="text-xs text-gray-500">{{ $case->amount_recovered ? number_format($case->amount_recovered,2) : '—' }}</p>
                </div>
            </li>
            <li class="flex items-start">
                <span class="w-6 h-6 flex items-center justify-center rounded-full bg-emerald-600 text-white text-xs mr-3">7</span>
                <div>
                    <p class="font-medium">Case Closure</p>
                    <p class="text-xs text-gray-500">{{ $case->closure_type ?? '—' }}</p>
                </div>
            </li>
        </ul>
    @endif
</div>






