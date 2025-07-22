<div class="space-y-6">

    

            
            
            

    <!-- Step 1: Investigation (Police) -->
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Federal Police Branch Name <span class="text-red-500">*</span></label>
            <input x-model="form.police_branch" name="police_branch" class="w-full border-gray-300 rounded" required />
        </div>
        <!-- Charged Persons -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Charged Person(s) <span class="text-red-500">*</span></label>
            <template x-for="(person, idx) in form.charged_persons" :key="idx">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-2 p-4 border rounded-lg bg-gray-50">
                    <div>
                        <input type="text" :name="`charged_persons[${idx}][name]`" x-model="person.name" class="w-full border-gray-300 rounded mb-1" placeholder="Full Name" required>
                    </div>
                    <div>
                        <input type="text" :name="`charged_persons[${idx}][contact]`" x-model="person.contact" class="w-full border-gray-300 rounded mb-1" placeholder="Contact Info" required>
                    </div>
                    <div class="col-span-2 flex justify-end">
                 <button type="button" class="text-red-600 hover:text-red-800 text-xs" @click="form.charged_persons.splice(idx,1)" x-show="form.charged_persons.length > 1">Remove</button>
                    </div>
                </div>
            
     <button type="button" class="mt-2 px-3 py-1 border border-blue-300 rounded text-blue-700 bg-blue-50 hover:bg-blue-100 text-xs" @click="form.charged_persons.push({name:'',contact:''})">+ Add Charged Person</button>
        </div>
    </div>

    <!-- Step 2: Prosecution (Prosecutor) -->
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Federal Public Prosecutor Branch Name <span class="text-red-500">*</span></label>
            <input x-model="form.prosecutor_branch" name="prosecutor_branch" class="w-full border-gray-300 rounded" required />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Court File Number <span class="text-red-500">*</span></label>
            <input x-model="form.court_file_number" name="court_file_number" class="w-full border-gray-300 rounded" required />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Company File Number</label>
            <input x-model="form.company_file_number" name="company_file_number" class="w-full border-gray-300 rounded bg-gray-100" readonly />
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
            <input x-model="form.claimed_thing" name="claimed_thing" class="w-full border-gray-300 rounded" required />
        </div>
    </div>

    <!-- Step 3: Decision (Court) -->
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Case Entraining Court Name <span class="text-red-500">*</span></label>
            <input type="text" name="court_name" id="court_name" x-model="form.court_name" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="{{ old('court_name', $case->court_name ?? '') }}">
        </div>
        <div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Progress Notes</label>
            <textarea x-model="form.progress_notes" name="progress_notes" rows="3" class="w-full border-gray-300 rounded"></textarea>
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
                    
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    <div class="space-y-4">

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
        </div>        <div>
            <label for="opened_at" class="block text-sm font-medium text-gray-700 mb-1">Opened At <span class="text-red-500">*</span></label>
            <input type="date" name="opened_at" id="opened_at" class="w-full border-gray-300 rounded" required value="{{ old('opened_at', isset($case) && $case->opened_at ? $case->opened_at->format('Y-m-d') : '') }}">
            @error('opened_at')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Case Description</label>
            <textarea name="description" id="description" rows="3" class="w-full border-gray-300 rounded" placeholder="Enter or update the main case description...">{{ old('description', $case->description ?? '') }}</textarea>
            @error('description')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <input type="hidden" name="type" value="criminal" />
    </div>
</div>






