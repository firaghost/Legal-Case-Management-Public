<div class="space-y-6" x-data="{ isSupervisor: (typeof isSupervisor !== 'undefined' ? isSupervisor : false), showCloseModal: (typeof showCloseModal !== 'undefined' ? showCloseModal : false) }">
    <!-- Case Details -->
    <h2 class="text-lg font-semibold text-gray-800 mb-2 mt-4">Case Details</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div>
            <label for="branch_id_labor" class="block text-sm font-medium text-gray-700">Branch</label>
            <select name="branch_id" id="branch_id_labor" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option value="">Select Branch</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="work_unit_id_labor" class="block text-sm font-medium text-gray-700">Work Unit</label>
            <select name="work_unit_id" id="work_unit_id_labor" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option value="">Select Work Unit</option>
                @foreach($workUnits as $workUnit)
                    <option value="{{ $workUnit->id }}">{{ $workUnit->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="court_name_labor" class="block text-sm font-medium text-gray-700">Court Name</label>
            <input type="text" name="court_name" id="court_name_labor" x-model="form.court_name" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="{{ old('court_name', $case->court_name ?? '') }}">
        </div>
        <div>
            <label for="court_file_number_labor" class="block text-sm font-medium text-gray-700">Court File Number</label>
            <input type="text" name="court_file_number" id="court_file_number_labor" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Company File Number</label>
            <input x-model="form.company_file_number" name="company_file_number" class="w-full border-gray-300 rounded bg-gray-100" readonly />
        </div>
        <div>
            <label for="opened_at" class="block text-sm font-medium text-gray-700 mb-1">Opened At <span class="text-red-500">*</span></label>
            <input type="date" name="opened_at" id="opened_at" class="w-full border-gray-300 rounded" required value="{{ old('opened_at', isset($case) && $case->opened_at ? $case->opened_at->format('Y-m-d') : '') }}">
            @error('opened_at')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Parties -->
    <h2 class="text-lg font-semibold text-gray-800 mb-2 mt-8">Parties</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Plaintiff(s) <span class="text-red-500">*</span></label>
            <p class="text-xs text-gray-500 mb-2">Add all plaintiffs involved in the case.</p>
            @error('plaintiffs')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
            @error('plaintiffs.*.name')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
            <template x-for="(plaintiff, idx) in form.plaintiffs" :key="idx">
                <div class="grid grid-cols-1 gap-2 mb-2 p-3 border rounded-lg bg-gray-50">
                    <input type="text" :name="`plaintiffs[${idx}][name]`" x-model="plaintiff.name" class="w-full border-gray-300 rounded mb-1" placeholder="Full Name" required>
                    <input type="text" :name="`plaintiffs[${idx}][contact_number]`" x-model="plaintiff.contact_number" class="w-full border-gray-300 rounded mb-1" placeholder="Contact Number">
                    <input type="email" :name="`plaintiffs[${idx}][email]`" x-model="plaintiff.email" class="w-full border-gray-300 rounded mb-1" placeholder="Email">
                    <input type="text" :name="`plaintiffs[${idx}][address]`" x-model="plaintiff.address" class="w-full border-gray-300 rounded mb-1" placeholder="Address">
                    <div class="flex justify-end">
                        <button type="button" class="text-red-600 hover:text-red-800 text-xs" @click="form.plaintiffs.splice(idx,1)" x-show="form.plaintiffs.length > 1">Remove</button>
                    </div>
                </div>
            </template>
            <button type="button" class="mt-2 px-3 py-1 border border-blue-300 rounded text-blue-700 bg-blue-50 hover:bg-blue-100 text-xs" @click="form.plaintiffs.push({name:'',contact_number:'',email:'',address:''})">+ Add Plaintiff</button>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Defendant(s) <span class="text-red-500">*</span></label>
            <p class="text-xs text-gray-500 mb-2">Add all defendants involved in the case.</p>
            @error('defendants')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
            @error('defendants.*.name')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
            <template x-for="(defendant, idx) in form.defendants" :key="idx">
                <div class="grid grid-cols-1 gap-2 mb-2 p-3 border rounded-lg bg-gray-50">
                    <input type="text" :name="`defendants[${idx}][name]`" x-model="defendant.name" class="w-full border-gray-300 rounded mb-1" placeholder="Full Name" required>
                    <input type="text" :name="`defendants[${idx}][contact_number]`" x-model="defendant.contact_number" class="w-full border-gray-300 rounded mb-1" placeholder="Contact Number">
                    <input type="email" :name="`defendants[${idx}][email]`" x-model="defendant.email" class="w-full border-gray-300 rounded mb-1" placeholder="Email">
                    <input type="text" :name="`defendants[${idx}][address]`" x-model="defendant.address" class="w-full border-gray-300 rounded mb-1" placeholder="Address">
                    <div class="flex justify-end">
                        <button type="button" class="text-red-600 hover:text-red-800 text-xs" @click="form.defendants.splice(idx,1)" x-show="form.defendants.length > 1">Remove</button>
                    </div>
                </div>
            </template>
            <button type="button" class="mt-2 px-3 py-1 border border-blue-300 rounded text-blue-700 bg-blue-50 hover:bg-blue-100 text-xs" @click="form.defendants.push({name:'',contact_number:'',email:'',address:''})">+ Add Defendant</button>
        </div>
    </div>

    <!-- Claim Information -->
    <h2 class="text-lg font-semibold text-gray-800 mb-2 mt-8">Claim Information</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Claim Type <span class="text-red-500">*</span></label>
            <select x-model="form.claim_type" name="claim_type" class="w-full border-gray-300 rounded" required>
                <option value="">Select Claim Type</option>
                <option value="Money">Money</option>
                <option value="Material">Material</option>
                <option value="Both">Both</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Claim Amount (ETB)</label>
            <input type="number" name="claim_amount" x-model="form.claim_amount" class="w-full border-gray-300 rounded" min="0" step="0.01" placeholder="Enter claim amount">
            <input type="hidden" name="claimed_amount" :value="form.claim_amount">
        </div>
        <div x-show="form.claim_type === 'Material' || form.claim_type === 'Both'">
            <label class="block text-sm font-medium text-gray-700 mb-1">Material Description</label>
            <textarea name="claim_material_desc" x-model="form.claim_material_desc" class="w-full border-gray-300 rounded" placeholder="Describe the claimed material..."></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Recovered Amount</label>
            <input type="number" name="recovered_amount" x-model="form.recovered_amount" class="w-full border-gray-300 rounded" min="0" step="0.01" placeholder="Enter recovered amount">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Outstanding Amount</label>
            <input type="number" name="outstanding_amount" readonly :value="(parseFloat(form.claim_amount||0) - parseFloat(form.recovered_amount||0)).toFixed(2)" class="w-full border-gray-300 rounded bg-gray-100" placeholder="Auto-calculated">
        </div>
    </div>

    <!-- Attachments -->
    <h2 class="text-lg font-semibold text-gray-800 mb-2 mt-8">Attachments</h2>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Evidence or Documents (PDF/Image) <span class="text-red-500">*</span></label>
        <input type="file" name="evidence[]" accept="application/pdf,image/*" multiple class="w-full border-gray-300 rounded bg-white" />
        <p class="text-xs text-gray-500 mt-1">You can upload multiple files. Max size: 8MB each.</p>
        @if(isset($case) && $case->evidences && $case->evidences->count())
            <div class="mt-2">
                <p class="text-sm font-medium text-gray-700 mb-1">Already Uploaded:</p>
                <ul class="list-disc pl-5">
                    @foreach($case->evidences as $evidence)
                        <li>
                            <a href="{{ $evidence->file_path }}" target="_blank" class="text-blue-600 hover:underline">{{ $evidence->file_name }}</a>
                            <span class="text-xs text-gray-500 ml-2">({{ $evidence->uploaded_at->format('Y-m-d H:i') }})</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Case Information -->
    <h2 class="text-lg font-semibold text-gray-800 mb-2 mt-8">Case Information</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
    </div>

    <!-- Description -->
    <h2 class="text-lg font-semibold text-gray-800 mb-2 mt-8">Description</h2>
    <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Case Description</label>
        <textarea name="description" id="description" rows="3" class="w-full border-gray-300 rounded" placeholder="Enter or update the main case description...">{{ old('description', $case->description ?? '') }}</textarea>
        @error('description')
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label for="edit_description" class="block text-sm font-medium text-gray-700 mb-1">Edit Description <span class="text-red-500">*</span></label>
        <textarea name="edit_description" id="edit_description" rows="3" class="w-full border-gray-300 rounded" required placeholder="Describe what you changed or updated in this edit..."></textarea>
        @error('edit_description')
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Actions -->
    <div class="flex justify-between items-center pt-6 mt-4">
        <button type="button" x-show="isSupervisor" @click="showCloseModal = true" class="inline-flex items-center px-4 py-2 border border-red-600 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            Early Close Case
        </button>
        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Save Case
        </button>
    </div>

    <!-- Early Case Closure Confirmation Modal -->
    <div x-show="showCloseModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
            <h2 class="text-lg font-semibold mb-4">Confirm Early Case Closure</h2>
            <p class="mb-4">Are you sure you want to request early closure for this case? This action requires supervisor approval.</p>
            <div class="flex justify-end space-x-2">
                <button type="button" @click="showCloseModal = false" class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">Cancel</button>
                <button type="button" @click="requestEarlyClosure()" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">Confirm</button>
            </div>
        </div>
    </div>
    <input type="hidden" name="type" value="labor" />
</div>






