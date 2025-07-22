<div class="space-y-6">
    <!-- Progress Indicator -->
    <div class="w-full bg-gray-200 rounded h-2">
        <div class="h-2 bg-emerald-600 rounded" :style="`width: ${progress}%`"></div>
    </div>

    <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Case Title <span class="text-red-500">*</span></label>
        <input type="text" name="title" id="title" class="w-full border-gray-300 rounded" required value="{{ old('title', $case->title ?? '') }}" placeholder="Enter the case title">
        @error('title')
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Case Status <span class="text-red-500">*</span></label>
        <select name="status" id="status" class="w-full border-gray-300 rounded" required>
            <option value="open" {{ old('status', $case->status ?? '') == 'open' ? 'selected' : '' }}>Open</option>
            <option value="pending" {{ old('status', $case->status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="closed" {{ old('status', $case->status ?? '') == 'closed' ? 'selected' : '' }}>Closed</option>
        </select>
        @error('status')
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="opened_at" class="block text-sm font-medium text-gray-700 mb-1">Opened At <span class="text-red-500">*</span></label>
        <input type="date" name="opened_at" id="opened_at" class="w-full border-gray-300 rounded" required value="{{ old('opened_at', isset($case) && $case->opened_at ? $case->opened_at->format('Y-m-d') : '') }}">
        @error('opened_at')
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Base Branch <span class="text-red-500">*</span></label>
            <select name="branch_id" class="w-full border-gray-300 rounded" required>
                <option value="">Select Branch</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Work Unit <span class="text-red-500">*</span></label>
            <select name="work_unit_id" class="w-full border-gray-300 rounded" required>
                <option value="">Select Work Unit</option>
                @foreach($workUnits as $workUnit)
                    <option value="{{ $workUnit->id }}">{{ $workUnit->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Plaintiffs -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Plaintiff(s) <span class="text-red-500">*</span></label>
        @error('plaintiffs')
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror
        @error('plaintiffs.*.name')
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror
        <template x-for="(plaintiff, idx) in form.plaintiffs" :key="idx">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-2 p-4 border rounded-lg bg-gray-50">
                <div>
                    <input type="text" :name="`plaintiffs[${idx}][name]`" x-model="plaintiff.name" class="w-full border-gray-300 rounded mb-1" placeholder="Full Name" required>
                </div>
                <div>
                    <input type="text" :name="`plaintiffs[${idx}][contact_number]`" x-model="plaintiff.contact_number" class="w-full border-gray-300 rounded mb-1" placeholder="Contact Number">
                </div>
                <div>
                    <input type="email" :name="`plaintiffs[${idx}][email]`" x-model="plaintiff.email" class="w-full border-gray-300 rounded mb-1" placeholder="Email">
                </div>
                <div>
                    <input type="text" :name="`plaintiffs[${idx}][address]`" x-model="plaintiff.address" class="w-full border-gray-300 rounded mb-1" placeholder="Address">
                </div>
                <div class="col-span-2 flex justify-end">
                    <button type="button" class="text-red-600 hover:text-red-800 text-xs" @click="form.plaintiffs.splice(idx,1)" x-show="form.plaintiffs.length > 1">Remove</button>
                </div>
            </div>
        </template>
        <button type="button" class="mt-2 px-3 py-1 border border-blue-300 rounded text-blue-700 bg-blue-50 hover:bg-blue-100 text-xs" @click="form.plaintiffs.push({name:'',contact_number:'',email:'',address:''})">+ Add Plaintiff</button>
    </div>

    <!-- Defendants -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Defendant(s) <span class="text-red-500">*</span></label>
        @error('defendants')
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror
        @error('defendants.*.name')
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror
        <template x-for="(defendant, idx) in form.defendants" :key="idx">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-2 p-4 border rounded-lg bg-gray-50">
                <div>
                    <input type="text" :name="`defendants[${idx}][name]`" x-model="defendant.name" class="w-full border-gray-300 rounded mb-1" placeholder="Full Name" required>
                </div>
                <div>
                    <input type="text" :name="`defendants[${idx}][contact_number]`" x-model="defendant.contact_number" class="w-full border-gray-300 rounded mb-1" placeholder="Contact Number">
                </div>
                <div>
                    <input type="email" :name="`defendants[${idx}][email]`" x-model="defendant.email" class="w-full border-gray-300 rounded mb-1" placeholder="Email">
                </div>
                <div>
                    <input type="text" :name="`defendants[${idx}][address]`" x-model="defendant.address" class="w-full border-gray-300 rounded mb-1" placeholder="Address">
                </div>
                <div class="col-span-2 flex justify-end">
                    <button type="button" class="text-red-600 hover:text-red-800 text-xs" @click="form.defendants.splice(idx,1)" x-show="form.defendants.length > 1">Remove</button>
                </div>
            </div>
        </template>
        <button type="button" class="mt-2 px-3 py-1 border border-blue-300 rounded text-blue-700 bg-blue-50 hover:bg-blue-100 text-xs" @click="form.defendants.push({name:'',contact_number:'',email:'',address:''})">+ Add Defendant</button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Court File Number</label>
            <input type="text" name="court_file_number" value="{{ old('court_file_number', $caseTypeData->court_file_number ?? '') }}" class="w-full border-gray-300 rounded">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Company File Number</label>
            <input x-model="form.company_file_number" name="company_file_number" class="w-full border-gray-300 rounded bg-gray-100" readonly />
        </div>
    </div>
    <!-- Monetary Details -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Claimed Amount (ETB)</label>
            <input type="number" name="claimed_amount" x-model="form.claimed_amount" class="w-full border-gray-300 rounded" min="0" step="0.01" placeholder="Enter claimed amount">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Recovered Amount</label>
            <input type="number" name="recovered_amount" id="recovered_amount" x-model="form.recovered_amount" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" min="0" step="0.01" placeholder="Enter recovered amount" value="{{ old('recovered_amount', $caseTypeData->recovered_amount ?? ($case->recovered_amount ?? '')) }}">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Outstanding Amount</label>
            <input type="number" name="outstanding_amount" readonly :value="(parseFloat(form.claimed_amount||0) - parseFloat(form.recovered_amount||0)).toFixed(2)" class="w-full border-gray-300 rounded bg-gray-100" placeholder="Auto-calculated">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Claimed Thing <span class="text-red-500">*</span></label>
        <select x-model="form.claimed_thing" name="claimed_thing" class="w-full border-gray-300 rounded" required>
            <option value="">Select Claimed Thing</option>
            <option value="money">Money</option>
            <option value="property">Property</option>
            <option value="both">Both</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Case Entraining Court Name</label>
        <input type="text" name="court_name" id="court_name" x-model="form.court_name" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="{{ old('court_name', $case->court_name ?? '') }}">
    </div>
    <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Case Description</label>
        <textarea name="description" id="description" rows="3" class="w-full border-gray-300 rounded" placeholder="Enter or update the main case description...">{{ old('description', $case->description ?? '') }}</textarea>
        @error('description')
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
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
    <div>
        <label for="edit_description" class="block text-sm font-medium text-gray-700 mb-1">Edit Description <span class="text-red-500">*</span></label>
        <textarea name="edit_description" id="edit_description" rows="3" class="w-full border-gray-300 rounded" required placeholder="Describe what you changed or updated in this edit..."></textarea>
        @error('edit_description')
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <input type="hidden" name="type" value="civil" />
    <div class="flex justify-end pt-4">
        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Save Case
        </button>
    </div>
</div>







