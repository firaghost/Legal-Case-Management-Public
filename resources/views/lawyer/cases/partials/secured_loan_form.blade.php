{{-- 🧩 MODULE 5: Secured Loan Recovery (Code 05) --}}
{{-- Dynamic secured loan recovery form as per July-2025 specification. Uses Alpine.js for reactivity and Tailwind CSS for layout. --}}
@php
    /** @var \Illuminate\Database\Eloquent\Collection $branches */
    use Illuminate\Support\Str;
    $rawNotice = old('foreclosure_notice_date') ?? ($caseTypeData->foreclosure_notice_date ?? $case->foreclosure_notice_date ?? null);
    if($rawNotice instanceof \Illuminate\Support\Carbon) {
        $resolvedNoticeDate = $rawNotice->toDateString();
    } elseif(is_string($rawNotice)) {
        $resolvedNoticeDate = Str::substr($rawNotice,0,10);
    } else {
        $resolvedNoticeDate = '';
    }

    /** @var \Illuminate\Database\Eloquent\Collection $workUnits */
    $caseTypeData = $caseTypeData ?? null;
    $latestAuction = $caseTypeData?->auctions->sortByDesc('round')->first();
    $closureLabel = match($caseTypeData?->closure_type) {
        'fully_repaid'        => 'Fully Paid',
        'collateral_sold'     => 'Auctioned and Covered',
        'collateral_acquired' => 'Acquired by ORGANIZATION',
        'restructured'        => 'Restructured with Approval',
        'settlement'          => 'Settled via Agreement',
        default               => null,
    };
@endphp

<script>
    // Make the data function available globally BEFORE Alpine initialises
    window.securedLoanForm = function () {
        const setRoundFromFlags = (state) => {
            if (state.first_auction_held && !state.second_auction_held) {
                state.auction_round = '1';
            } else if (state.second_auction_held) {
                state.auction_round = '2';
            } else if (!state.first_auction_held && !state.second_auction_held) {
                state.auction_round = '';
            }
        };
        return {
            init() {
                setRoundFromFlags(this.form);
                this.$watch('form.first_auction_held', () => setRoundFromFlags(this.form));
                this.$watch('form.second_auction_held', () => setRoundFromFlags(this.form));
            },
            previewMode: false,
            form: {
                // existing

                branch_id: @json(old('branch_id', $caseTypeData?->branch_id ?? $case->branch_id ?? '')),
                work_unit_id: @json(old('work_unit_id', $case->work_unit_id ?? '')),
                customer_name: @json(old('customer_name', $caseTypeData?->customer_name ?? '')),
                company_file_number: @json(old('company_file_number', $caseTypeData?->company_file_number ?? '')),
                outstanding_amount: @json(old('outstanding_amount', $caseTypeData?->claimed_amount ?? '')),
                foreclosure_warning: Boolean(@json(old('foreclosure_warning', $caseTypeData?->foreclosure_warning ?? false))),
                auction_round: @json(old('auction_round', $latestAuction?->round ?? '')),
                amount_recovered: @json(old('amount_recovered', $caseTypeData?->recovered_amount ?? '')),
                closure_type: @json(old('closure_type', $closureLabel ?? '')),
                other_notes: @json(old('other_notes', $caseTypeData?->other_notes ?? '')),
                description: @json(old('description', $case->description ?? '')),
                foreclosure_notice_date: @json($resolvedNoticeDate),
                collateral_description: @json(old('collateral_description', $caseTypeData?->collateral_description ?? '')),
                collateral_value: @json(old('collateral_value', $caseTypeData?->collateral_value ?? '')),
                first_auction_held: Boolean(@json(old('first_auction_held', $caseTypeData?->first_auction_held ?? false))),
                second_auction_held: Boolean(@json(old('second_auction_held', $caseTypeData?->second_auction_held ?? false))),
                auction_date: @json(old('auction_date', $latestAuction?->auction_date?->toDateString() ?? '')),
                auction_result: @json(old('auction_result', $latestAuction?->result ?? '')),
                sold_amount: @json(old('sold_amount', $latestAuction?->sold_amount ?? '')),
                auction_notes: @json(old('auction_notes', $latestAuction?->notes ?? '')),
                existingCollateralEstimation: Boolean(@json(!empty($caseTypeData?->collateral_estimation_path))),
                existingAuctionPublication: Boolean(@json(!empty($caseTypeData?->auction_publication_path))),
                existingWarningDoc: Boolean(@json(!empty($caseTypeData?->warning_doc_path))),
            },
            togglePreview() {
                // keep auction_round synced when toggling flags manually
                setRoundFromFlags(this.form);
                this.previewMode = !this.previewMode;
            },
            // Derived properties
            get requireWarningDoc() {
                return this.form.foreclosure_warning;
            },
            get auctionSelected() {
                return this.form.auction_round === '1' || this.form.auction_round === '2';
            },
            get closureTypeRequiresApproval() {
                return this.form.closure_type === 'Restructured with Approval';
            },
            get closureTypeOther() {
                return this.form.closure_type === 'Other';
            },
            get outstandingBalance() {
                return parseFloat(this.form.outstanding_amount) - parseFloat(this.form.amount_recovered);
            },
            get outstandingBalanceFormatted() {
                return this.outstandingBalance.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
        }
    }
</script>

<div x-data="securedLoanForm()" class="space-y-6">
    <!-- Preview Toggle -->
    <div class="flex justify-end">
        <button type="button" @click="togglePreview" class="px-3 py-1 text-sm rounded border border-indigo-600 text-indigo-600 hover:bg-indigo-50">
            <span x-text="previewMode ? 'Edit Mode' : 'Preview Mode'"></span>
        </button>
    </div>

    <!-- ================= FORM MODE ================= -->
    <div x-show="!previewMode" x-cloak>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Base Branch -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Base Branch <span class="text-red-500">*</span></label>
                <select name="branch_id" x-model="form.branch_id" class="w-full border-gray-300 rounded" required>
                    <option value="">Select Branch</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Work Unit -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Work Unit</label>
                <select name="work_unit_id" x-model="form.work_unit_id" class="w-full border-gray-300 rounded">
                    <option value="">Select Work Unit</option>
                    @foreach($workUnits as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Customer Name -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Default Customer Full Name <span class="text-red-500">*</span></label>
                <input type="text" name="customer_name" x-model="form.customer_name" class="w-full border-gray-300 rounded" required />
            </div>
            <!-- Outstanding Loan Amount -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Outstanding Loan Amount Claimed <span class="text-red-500">*</span></label>
                <input type="number" min="0" step="0.01" name="claimed_amount" x-model="form.outstanding_amount" class="w-full border-gray-300 rounded" required />
                <!-- Hidden loan_amount to satisfy DB column -->
                <input type="hidden" name="loan_amount" x-bind:value="form.outstanding_amount" />
            </div>
            <!-- Company File Number (auto-generated, read-only) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Company File #</label>
                <div class="py-2 px-3 border border-gray-300 rounded bg-gray-100 text-sm">
                    @php $autoCompanyFile = 'SLR-' . now()->format('Y-m-d'); @endphp
                    {{ $caseTypeData?->company_file_number ?? $autoCompanyFile }}
                </div>
                <input type="hidden" name="company_file_number" value="{{ $caseTypeData?->company_file_number ?? $autoCompanyFile }}" />
            </div>
            <!-- 30-Day Warning Toggle -->
            <div class="flex items-center mt-7">
                <input type="checkbox" x-model="form.foreclosure_warning" name="foreclosure_warning" class="mr-2" />
                <label class="text-sm font-medium text-gray-700">30-Day Foreclosure Warning</label>
            </div>
            <!-- Foreclosure Notice Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Foreclosure Notice Date</label>
                <input type="date" name="foreclosure_notice_date" x-model="form.foreclosure_notice_date" :value="form.foreclosure_notice_date" class="w-full border-gray-300 rounded" />
            </div>
            <!-- 30-Day Warning Doc -->
            <div class="md:col-span-2" x-show="requireWarningDoc" x-cloak>
                <label class="block text-sm font-medium mb-1 text-red-500">Upload 30-Day Warning Document <span class="text-red-500">*</span></label>
                @if(!empty($caseTypeData?->warning_doc_path))
                    <p class="text-sm mb-1">Existing: <a href="{{ Storage::url($caseTypeData->warning_doc_path) }}" target="_blank" class="text-indigo-600 underline">view</a></p>
                @endif
                <input type="file" name="warning_doc" accept="application/pdf,image/*" :required="requireWarningDoc && !form.existingWarningDoc" class="w-full border-gray-300 rounded bg-white" />
            </div>
            <!-- Collateral Estimation -->
            <template x-if="!auctionSelected">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload Collateral Estimate (if available)</label>
                    @if(!empty($caseTypeData?->collateral_estimation_path))
                        <p class="text-sm mb-1">Existing: <a href="{{ Storage::url($caseTypeData->collateral_estimation_path) }}" target="_blank" class="text-indigo-600 underline">view</a></p>
                    @endif
                    <input type="file" name="collateral_estimation" accept="application/pdf,image/*" class="w-full border-gray-300 rounded bg-white" />
                </div>
            </template>
            <template x-if="auctionSelected">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1 text-red-500">Collateral Estimation Upload <span class="text-red-500">*</span></label>
                    @if(!empty($caseTypeData?->collateral_estimation_path))
                        <p class="text-sm mb-1">Existing: <a href="{{ Storage::url($caseTypeData->collateral_estimation_path) }}" target="_blank" class="text-indigo-600 underline">view</a></p>
                    @endif
                    <input type="file" name="collateral_estimation" accept="application/pdf,image/*" :required="auctionSelected && !form.existingCollateralEstimation" class="w-full border-gray-300 rounded bg-white" />
                </div>
            </template>
            <!-- Case Description -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Case Description</label>
                <textarea name="description" x-model="form.description" rows="3" class="w-full border-gray-300 rounded"></textarea>
            </div>
            <!-- Collateral Description -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Collateral Description</label>
                <textarea name="collateral_description" x-model="form.collateral_description" rows="2" class="w-full border-gray-300 rounded"></textarea>
            </div>
            <!-- Collateral Value -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Collateral Estimated Value (ETB)</label>
                <input type="number" min="0" step="0.01" name="collateral_value" x-model="form.collateral_value" class="w-full border-gray-300 rounded" />
            </div>
            <!-- Auction Held Flags -->
            <div class="flex items-center">
                <input type="checkbox" x-model="form.first_auction_held" name="first_auction_held" class="mr-2" />
                <label class="text-sm font-medium text-gray-700">First Auction Held</label>
            </div>
            <div class="flex items-center">
                <input type="checkbox" x-model="form.second_auction_held" name="second_auction_held" class="mr-2" />
                <label class="text-sm font-medium text-gray-700">Second Auction Held</label>
            </div>
            <!-- Auction Round (legacy) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Auction Publication Round</label>
                <select name="auction_round" x-model="form.auction_round" class="w-full border-gray-300 rounded">
                    <option value="">None</option>
                    <option value="1">Round 1</option>
                    <option value="2">Round 2</option>
                </select>
            </div>
            <!-- Extra Auction Details -->
            <template x-if="auctionSelected">
                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Auction Date <span class="text-red-500">*</span></label>
                        <input type="date" name="auction_date" x-model="form.auction_date" class="w-full border-gray-300 rounded" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Auction Result <span class="text-red-500">*</span></label>
                        <select name="auction_result" x-model="form.auction_result" class="w-full border-gray-300 rounded" required>
                            <option value="">Select</option>
                            <option value="sold">Sold</option>
                            <option value="ORGANIZATION_acquired">ORGANIZATION Acquired</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sold Amount (if sold)</label>
                        <input type="number" min="0" step="0.01" name="sold_amount" x-model="form.sold_amount" class="w-full border-gray-300 rounded">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Auction Notes</label>
                        <textarea name="auction_notes" x-model="form.auction_notes" rows="2" class="w-full border-gray-300 rounded"></textarea>
                    </div>
                </div>
            </template>

            <!-- Auction Publication File -->
            <div class="md:col-span-2" x-show="auctionSelected" x-cloak>
                <label class="block text-sm font-medium mb-1 text-red-500">Auction Publication File Upload <span class="text-red-500">*</span></label>
                @if(!empty($caseTypeData?->auction_publication_path))
                        <p class="text-sm mb-1">Existing: <a href="{{ Storage::url($caseTypeData->auction_publication_path) }}" target="_blank" class="text-indigo-600 underline">view</a></p>
                    @endif
                     <input type="file" name="auction_publication" accept="application/pdf,image/*" :required="auctionSelected && !form.existingAuctionPublication" class="w-full border-gray-300 rounded bg-white" />
            </div>
            <!-- Existing Evidences -->
            @if(isset($case) && $case->evidences->isNotEmpty())
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Existing Evidences</label>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($case->evidences as $evidence)
                            <li>
                                <a href="{{ Storage::url($evidence->file_path) }}" class="text-indigo-600 underline" target="_blank">
                                    {{ basename($evidence->file_path) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <!-- Amount Recovered -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Amount Recovered</label>
                <input type="number" min="0" step="0.01" name="recovered_amount" x-model="form.amount_recovered" class="w-full border-gray-300 rounded" />
                <p class="text-xs text-gray-500 mt-1">Outstanding Balance: <span class="font-semibold" x-text="outstandingBalanceFormatted"></span></p>
            </div>
            <!-- Closure Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Closure Type <span class="text-red-500">*</span></label>
                <select name="closure_type" x-model="form.closure_type" class="w-full border-gray-300 rounded" required>
                    <option value="">Select Closure Type</option>
                    <option value="Fully Paid">Fully Paid</option>
                    <option value="Auctioned and Covered">Auctioned and Covered</option>
                    <option value="Acquired by ORGANIZATION">Acquired by ORGANIZATION</option>
                    <option value="Restructured with Approval">Restructured with Approval</option>
                    <option value="Settled via Agreement">Settled via Agreement</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <!-- Supervisor Approval -->
            <div class="md:col-span-2" x-show="closureTypeRequiresApproval" x-cloak>
                <label class="block text-sm font-medium mb-1 text-red-500">Supervisor Approval File <span class="text-red-500">*</span></label>
                <input type="file" name="supervisor_approval" accept="application/pdf,image/*" :required="closureTypeRequiresApproval" class="w-full border-gray-300 rounded bg-white" />
            </div>
            <!-- Other Notes -->
            <div class="md:col-span-2" x-show="closureTypeOther" x-cloak>
                <label class="block text-sm font-medium text-gray-700 mb-1">Closure Notes</label>
                <textarea name="other_notes" x-model="form.other_notes" rows="3" class="w-full border-gray-300 rounded"></textarea>
            </div>
            <!-- Additional Recovery Docs -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Upload Additional Recovery Documents</label>
                <input type="file" name="recovery_docs[]" accept="application/pdf,image/*" multiple class="w-full border-gray-300 rounded bg-white" />
            </div>
            <!-- Case Information -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Case Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" class="w-full border-gray-300 rounded" required 
                       value="{{ old('title', isset($case) && $case->title ?? '') }}" 
                       placeholder="Enter the case title">
                @error('title')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Case Status <span class="text-red-500">*</span></label>
                <select name="status" id="status" class="w-full border-gray-300 rounded" required>
                    <option value="open" {{ old('status', isset($case) && $case->status ?? 'open') == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="pending" {{ old('status', isset($case) && $case->status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="closed" {{ old('status', isset($case) && $case->status ?? '') == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
                @error('status')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="opened_at" class="block text-sm font-medium text-gray-700 mb-1">Opened At <span class="text-red-500">*</span></label>
                <input type="date" name="opened_at" id="opened_at" class="w-full border-gray-300 rounded" required 
                       value="{{ old('opened_at', isset($case) && $case->opened_at ? $case->opened_at->format('Y-m-d') : date('Y-m-d')) }}">
                @error('opened_at')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <!-- Submit -->
        <div class="flex justify-end pt-4">
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none">Save Case</button>
        </div>
    </div>

    <!-- ================= PREVIEW MODE =============== -->
    <div x-show="previewMode" x-cloak>
        <x-recovery-timeline :case="$case ?? null" />
    </div>

    <input type="hidden" name="type" value="secured_loan" />
</div>





