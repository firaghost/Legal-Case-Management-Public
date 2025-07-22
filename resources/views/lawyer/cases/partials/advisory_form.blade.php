<div class="space-y-6">
    <!-- Advisory Type Selection -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Advisory Type <span class="text-red-500">*</span></label>
        <div class="flex gap-4 mb-4">
            <button type="button" @click="tab='advice'" 
                :class="tab==='advice' ? 'px-4 py-2 bg-blue-600 text-white rounded font-semibold' : 'px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300'">
                Written Advice
            </button>
            <button type="button" @click="tab='review'" 
                :class="tab==='review' ? 'px-4 py-2 bg-blue-600 text-white rounded font-semibold' : 'px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300'">
                Document Review
            </button>
        </div>
    </div>

    <!-- Hidden field for advisory_type -->
    <input type="hidden" name="advisory_type" x-model="tab === 'advice' ? 'written_advice' : 'document_review'" />

    <!-- Common Fields -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Subject <span class="text-red-500">*</span></label>
            <input type="text" name="subject" class="w-full border-gray-300 rounded" required 
                   value="{{ old('subject', isset($caseTypeData) ? optional($caseTypeData)->subject : '') }}" 
                   placeholder="Enter the advisory subject">
            @error('subject')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Request Date <span class="text-red-500">*</span></label>
            <input type="date" name="request_date" class="w-full border-gray-300 rounded" required 
                   value="{{ old('request_date', (isset($caseTypeData) && $caseTypeData && $caseTypeData->request_date) ? $caseTypeData->request_date->format('Y-m-d') : date('Y-m-d')) }}">
            @error('request_date')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
        <textarea name="description" rows="4" class="w-full border-gray-300 rounded" 
                  placeholder="Provide details about the advisory request">{{ old('description', isset($caseTypeData) ? optional($caseTypeData)->description : '') }}</textarea>
        @error('description')
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Requesting Department</label>
            <input type="text" name="requesting_department" class="w-full border-gray-300 rounded" 
                   value="{{ old('requesting_department', isset($caseTypeData) ? optional($caseTypeData)->requesting_department : '') }}" 
                   placeholder="Enter requesting department">
            @error('requesting_department')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Work Unit Advised</label>
            <input type="text" name="work_unit_advised" class="w-full border-gray-300 rounded" 
                   value="{{ old('work_unit_advised', isset($caseTypeData) ? optional($caseTypeData)->work_unit_advised : '') }}" 
                   placeholder="Enter work unit being advised">
            @error('work_unit_advised')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Is Own Motion?</label>
            <select name="is_own_motion" class="w-full border-gray-300 rounded">
                <option value="0" {{ old('is_own_motion', (isset($caseTypeData) && $caseTypeData) ? $caseTypeData->is_own_motion ?? '0' : '0') == '0' ? 'selected' : '' }}>No</option>
                <option value="1" {{ old('is_own_motion', (isset($caseTypeData) && $caseTypeData) ? $caseTypeData->is_own_motion ?? '0' : '0') == '1' ? 'selected' : '' }}>Yes</option>
            </select>
            @error('is_own_motion')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Claimed Amount</label>
            <input type="number" step="0.01" name="claimed_amount" class="w-full border-gray-300 rounded" 
                   value="{{ old('claimed_amount', isset($caseTypeData) ? optional($caseTypeData)->claimed_amount : '') }}" 
                   placeholder="Enter claimed amount">
            @error('claimed_amount')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Stakeholders Section -->
    <div class="mb-6">
        <h4 class="text-lg font-medium text-gray-900 mb-4">Stakeholders <span class="text-red-500">*</span></h4>
        @php
    $initialStakeholders = old('stakeholders', (isset($caseTypeData) && $caseTypeData && $caseTypeData->relationLoaded('stakeholders'))
        ? $caseTypeData->stakeholders->map(fn($s) => [
            'name' => $s->name,
            'type' => $s->type,
            'email' => $s->email,
            'phone' => $s->phone,
            'organization' => $s->organization,
        ])->toArray()
        : []);
    if (empty($initialStakeholders)) {
        $initialStakeholders = [[ 'name' => '', 'type' => 'requester', 'email' => '', 'phone' => '', 'organization' => '' ]];
    }
@endphp
<div x-data='{ stakeholders: @json($initialStakeholders) }'>
            <template x-for="(stakeholder, index) in stakeholders" :key="index">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4 p-4 border rounded-lg bg-gray-50">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                        <input type="text" :name="`stakeholders[${index}][name]`" x-model="stakeholder.name" 
                               class="w-full border-gray-300 rounded" required placeholder="Stakeholder name">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
                        <select :name="`stakeholders[${index}][type]`" x-model="stakeholder.type" 
                                class="w-full border-gray-300 rounded" required>
                            <option value="requester">Requester</option>
                            <option value="reviewer">Reviewer</option>
                            <option value="approver">Approver</option>
                            <option value="recipient">Recipient</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" :name="`stakeholders[${index}][email]`" x-model="stakeholder.email" 
                               class="w-full border-gray-300 rounded" placeholder="Email address">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" :name="`stakeholders[${index}][phone]`" x-model="stakeholder.phone" 
                               class="w-full border-gray-300 rounded" placeholder="Phone number">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Organization</label>
                        <input type="text" :name="`stakeholders[${index}][organization]`" x-model="stakeholder.organization" 
                               class="w-full border-gray-300 rounded" placeholder="Organization name">
                    </div>
                    <div class="flex items-end">
                        <button type="button" @click="stakeholders.splice(index, 1)" 
                                x-show="stakeholders.length > 1"
                                class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                            Remove
                        </button>
                    </div>
                </div>
            </template>
            <button type="button" @click="stakeholders.push({ name: '', type: 'requester', email: '', phone: '', organization: '' })" 
                    class="mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                Add Stakeholder
            </button>
        </div>
        @error('stakeholders')
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Type-specific fields -->
    <div x-show="tab === 'advice'" class="space-y-4">
        <h4 class="text-lg font-medium text-gray-900 mb-4">Written Advice Details</h4>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Reference Number</label>
            <input type="text" name="reference_number" class="w-full border-gray-300 rounded" 
                   value="{{ old('reference_number', isset($caseTypeData) ? $caseTypeData->reference_number : '') }}" 
                   placeholder="Enter reference number">
            @error('reference_number')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div x-show="tab === 'review'" class="space-y-4">
        <h4 class="text-lg font-medium text-gray-900 mb-4">Document Review Details</h4>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Document Upload</label>
            @if(isset($caseTypeData) && optional($caseTypeData)->document_path)
                <p class="text-sm text-gray-600 mb-2">Existing document: <a href="{{ Storage::url($caseTypeData->document_path) }}" target="_blank" class="text-blue-600 underline">View current file</a></p>
            @endif
            <input type="file" name="document" accept=".pdf,.doc,.docx" class="w-full border-gray-300 rounded bg-white">
            <p class="text-xs text-gray-500 mt-1">Upload document for review (PDF, DOC, DOCX - max 10MB)</p>
            @error('document')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Review Notes</label>
            <textarea name="review_notes" rows="4" class="w-full border-gray-300 rounded" 
                      placeholder="Provide notes about the document review">{{ old('review_notes',  isset($caseTypeData) ? (isset($caseTypeData->review_notes) ? $caseTypeData->review_notes : '') : '') }}</textarea>
            @error('review_notes')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Common Case Fields -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Case Title <span class="text-red-500">*</span></label>
            <input type="text" name="title" id="title" class="w-full border-gray-300 rounded" required 
                   value="{{ old('title', isset($case) ? $case->title : '') }}" 
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
    </div>

    <div>
        <label for="opened_at" class="block text-sm font-medium text-gray-700 mb-1">Opened At <span class="text-red-500">*</span></label>
        <input type="date" name="opened_at" id="opened_at" class="w-full border-gray-300 rounded" required 
               value="{{ old('opened_at', isset($case) && $case->opened_at ? $case->opened_at->format('Y-m-d') : date('Y-m-d')) }}">
        @error('opened_at')
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <input type="hidden" name="type" value="advisory" />
</div>






