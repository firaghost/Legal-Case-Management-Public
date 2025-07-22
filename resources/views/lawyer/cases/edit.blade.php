<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Modern Page Header -->
            <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-800 dark:to-indigo-800 rounded-3xl shadow-2xl mb-8">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.1"%3E%3Ccircle cx="30" cy="30" r="4"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>
                <div class="relative px-8 py-8">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-white mb-1">Edit Case</h1>
                            <p class="text-white/90 text-lg">{{ $case->title }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-3xl overflow-hidden border border-gray-200 dark:border-gray-700" x-data="caseForm()" x-init="init()">
                <form action="{{ route('lawyer.cases.update', $case) }}" method="POST" enctype="multipart/form-data" class="p-8">
                    @csrf
                    @method('PUT')
                    
                    <!-- Case Type Section -->
                    <div class="mb-8">
                        <div class="bg-gradient-to-r from-gray-50 to-blue-50 dark:from-gray-900/10 dark:to-blue-900/10 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Case Type</h3>
                            </div>
                            <input type="text" value="{{ $case->caseType->name }}" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium" readonly>
                            <input type="hidden" name="case_type_id" value="{{ $case->case_type_id }}">
                        </div>
                    </div>

                    <!-- Case Details Section -->
                    <div class="mb-8">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/10 dark:to-indigo-900/10 rounded-2xl p-6 border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Case Details</h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Case Title</label>
                                    <input type="text" name="title" value="{{ old('title', $case->title) }}" 
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition duration-200" 
                                           required placeholder="Enter case title">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                                    <select name="status" 
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition duration-200" 
                                            required>
                                        <option value="open" {{ old('status', $case->status) === 'open' ? 'selected' : '' }}>Open</option>
                                        <option value="closed" {{ old('status', $case->status) === 'closed' ? 'selected' : '' }}>Closed</option>
                                        <option value="pending" {{ old('status', $case->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">File Number</label>
                                    <input type="text" name="file_number" value="{{ old('file_number', $case->file_number) }}" 
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition duration-200" 
                                           required placeholder="Enter file number">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Claimed Amount</label>
                                    <input type="number" step="0.01" name="claimed_amount" value="{{ old('claimed_amount', $case->claimed_amount) }}" 
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition duration-200" 
                                           placeholder="Enter claimed amount">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Opened Date</label>
                                    <input type="date" name="opened_at" value="{{ old('opened_at', $case->opened_at ? $case->opened_at->format('Y-m-d') : '') }}" 
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition duration-200" 
                                           required>
                                </div>
                                
                                @if($case->status === 'closed')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Closed Date</label>
                                    <input type="date" name="closed_at" value="{{ old('closed_at', $case->closed_at ? $case->closed_at->format('Y-m-d') : '') }}" 
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition duration-200">
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Plaintiffs Section -->
                    <div class="mb-8">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/10 dark:to-indigo-900/10 rounded-2xl p-6 border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Plaintiffs</h3>
                            </div>
                            
                            <div id="plaintiffs-container" class="space-y-4">
                                @foreach(old('plaintiffs', $case->plaintiffs) as $index => $plaintiff)
                                <div class="plaintiff-group bg-white dark:bg-gray-700/50 rounded-xl p-4 border border-blue-200 dark:border-blue-700 shadow-sm">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name</label>
                                            <input type="text" name="plaintiffs[{{ $index }}][name]" value="{{ data_get($plaintiff, 'name') }}" 
                                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition duration-200" 
                                                   required placeholder="Enter plaintiff name">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact Number</label>
                                            <input type="text" name="plaintiffs[{{ $index }}][contact_number]" value="{{ old('plaintiffs.' . $index . '.contact_number', data_get($plaintiff, 'contact_number')) }}" 
                                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition duration-200" 
                                                   placeholder="Enter contact number">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                                            <input type="email" name="plaintiffs[{{ $index }}][email]" value="{{ old('plaintiffs.' . $index . '.email', data_get($plaintiff, 'email')) }}" 
                                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition duration-200" 
                                                   placeholder="Enter email address">
                                        </div>
                                        <div class="flex items-end gap-2">
                                            <div class="flex-1">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
                                                <input type="text" name="plaintiffs[{{ $index }}][address]" value="{{ old('plaintiffs.' . $index . '.address', data_get($plaintiff, 'address')) }}" 
                                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition duration-200" 
                                                       placeholder="Enter address">
                                            </div>
                                            @if($index > 0)
                                            <button type="button" 
                                                    class="w-10 h-10 bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400 rounded-xl hover:bg-red-200 dark:hover:bg-red-900/70 transition duration-200 flex items-center justify-center" 
                                                    onclick="this.closest('.plaintiff-group').remove(); reindexPlaintiffs();">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            
                            <button type="button" onclick="addPlaintiff()" 
                                    class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 rounded-xl hover:bg-blue-200 dark:hover:bg-blue-900/70 font-medium transition duration-200 transform hover:-translate-y-0.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Add Plaintiff
                            </button>
                        </div>
                    </div>

                    <!-- Defendants Section -->
                    <div class="mb-8">
                        <div class="bg-gradient-to-r from-red-50 to-pink-50 dark:from-red-900/10 dark:to-pink-900/10 rounded-2xl p-6 border border-red-200 dark:border-red-800">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 bg-red-100 dark:bg-red-900/50 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Defendants</h3>
                            </div>
                            
                            <div id="defendants-container" class="space-y-4">
                                @foreach(old('defendants', $case->defendants) as $index => $defendant)
                                <div class="defendant-group bg-white dark:bg-gray-700/50 rounded-xl p-4 border border-red-200 dark:border-red-700 shadow-sm">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name</label>
                                            <input type="text" name="defendants[{{ $index }}][name]" value="{{ data_get($defendant, 'name') }}" 
                                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white transition duration-200" 
                                                   required placeholder="Enter defendant name">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact Number</label>
                                            <input type="text" name="defendants[{{ $index }}][contact_number]" value="{{ old('defendants.' . $index . '.contact_number', data_get($defendant, 'contact_number')) }}" 
                                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white transition duration-200" 
                                                   placeholder="Enter contact number">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                                            <input type="email" name="defendants[{{ $index }}][email]" value="{{ old('defendants.' . $index . '.email', data_get($defendant, 'email')) }}" 
                                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white transition duration-200" 
                                                   placeholder="Enter email address">
                                        </div>
                                        <div class="flex items-end gap-2">
                                            <div class="flex-1">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
                                                <input type="text" name="defendants[{{ $index }}][address]" value="{{ old('defendants.' . $index . '.address', data_get($defendant, 'address')) }}" 
                                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white transition duration-200" 
                                                       placeholder="Enter address">
                                            </div>
                                            @if($index > 0)
                                            <button type="button" 
                                                    class="w-10 h-10 bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400 rounded-xl hover:bg-red-200 dark:hover:bg-red-900/70 transition duration-200 flex items-center justify-center" 
                                                    onclick="this.closest('.defendant-group').remove(); reindexDefendants();">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            
                            <button type="button" onclick="addDefendant()" 
                                    class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300 rounded-xl hover:bg-red-200 dark:hover:bg-red-900/70 font-medium transition duration-200 transform hover:-translate-y-0.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Add Defendant
                            </button>
                        </div>
                    </div>

                    <!-- Case Type Specific Fields -->
                    <div class="mt-6">
                        @php
                            $caseTypeName = strtolower(str_replace(' ', '_', $case->caseType->name));
                            $partialName = 'lawyer.cases.partials.' . $caseTypeName . '_form';
                        @endphp

                        @if(view()->exists($partialName))
                            @include($partialName, [
                                'case' => $case,
                                'caseTypeData' => $caseTypeData,
                                'branches' => $branches,
                                'workUnits' => $workUnits,
                                'lawyers' => $lawyers
                            ])
                        @else
                            <p class="text-sm text-gray-500">No specific fields for this case type.</p>
                        @endif
                    </div>

                    <!-- Description Section -->
                    <div class="mb-8">
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/10 dark:to-emerald-900/10 rounded-2xl p-6 border border-green-200 dark:border-green-800">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/50 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Case Description</h3>
                            </div>
                            <textarea name="description" rows="4" 
                                      class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition duration-200 resize-none" 
                                      placeholder="Enter detailed case description...">{{ old('description', $case->description) }}</textarea>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('lawyer.cases.show', $case) }}" 
                           class="inline-flex items-center gap-2 px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition duration-200 transform hover:-translate-y-0.5 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancel
                        </a>
                        
                        <button type="submit" 
                                class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition duration-200 transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Update Case
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Initialize Alpine.js data
        function caseForm() {
            return {
                type: '{{ strtolower(str_replace(' ', '_', $case->caseType->name)) }}',
                form: {
                    plaintiffs: @json(old('plaintiffs', $case->plaintiffs->map(fn($p) => [
                        'name' => $p->name,
                        'contact_number' => $p->contact_number,
                        'email' => $p->email,
                        'address' => $p->address,
                    ])) ?? []),
                    defendants: @json(old('defendants', $case->defendants->map(fn($d) => [
                        'name' => $d->name,
                        'contact_number' => $d->contact_number,
                        'email' => $d->email,
                        'address' => $d->address,
                    ])) ?? []),
                    claimed_amount: '{{ old('claimed_amount', $case->claimed_amount) }}',
                    recovered_amount: '{{ old('recovered_amount', $case->recovered_amount) }}',
                    company_file_number: '{{ old('company_file_number', $caseTypeData->company_file_number ?? '') }}',
                    claimed_thing: '{{ old('claimed_thing', $caseTypeData->claimed_thing ?? '') }}',
                },
                init() {
                    if (!this.form.plaintiffs || this.form.plaintiffs.length === 0) {
                        this.form.plaintiffs = [{name:'',contact_number:'',email:'',address:''}];
                    }
                    if (!this.form.defendants || this.form.defendants.length === 0) {
                        this.form.defendants = [{name:'',contact_number:'',email:'',address:''}];
                    }
                },
            };
        }

        // Add new plaintiff row
        function addPlaintiff() {
            const container = document.getElementById('plaintiffs-container');
            const index = container.querySelectorAll('.plaintiff-group').length;
            
            const template = `
                <div class="plaintiff-group grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4 p-4 border rounded-lg">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" name="plaintiffs[${index}][name]" class="w-full border-gray-300 rounded" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                        <input type="text" name="plaintiffs[${index}][contact_number]" class="w-full border-gray-300 rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="plaintiffs[${index}][email]" class="w-full border-gray-300 rounded">
                    </div>
                    <div class="flex items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <input type="text" name="plaintiffs[${index}][address]" class="w-full border-gray-300 rounded">
                        </div>
                        <button type="button" class="ml-2 text-red-600 hover:text-red-800" onclick="this.closest('.plaintiff-group').remove(); reindexPlaintiffs();">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', template);
        }

        // Add new defendant row
        function addDefendant() {
            const container = document.getElementById('defendants-container');
            const index = container.querySelectorAll('.defendant-group').length;
            
            const template = `
                <div class="defendant-group grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4 p-4 border rounded-lg">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" name="defendants[${index}][name]" class="w-full border-gray-300 rounded" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                        <input type="text" name="defendants[${index}][contact_number]" class="w-full border-gray-300 rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="defendants[${index}][email]" class="w-full border-gray-300 rounded">
                    </div>
                    <div class="flex items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <input type="text" name="defendants[${index}][address]" class="w-full border-gray-300 rounded">
                        </div>
                        <button type="button" class="ml-2 text-red-600 hover:text-red-800" onclick="this.closest('.defendant-group').remove(); reindexDefendants();">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', template);
        }

        // Reindex plaintiff inputs after removal
        function reindexPlaintiffs() {
            const container = document.getElementById('plaintiffs-container');
            const groups = container.querySelectorAll('.plaintiff-group');
            groups.forEach((group, index) => {
                group.querySelectorAll('input, select').forEach(input => {
                    input.name = input.name.replace(/\[\d+\]/, `[${index}]`);
                });
            });
        }

        // Reindex defendant inputs after removal
        function reindexDefendants() {
            const container = document.getElementById('defendants-container');
            const groups = container.querySelectorAll('.defendant-group');
            groups.forEach((group, index) => {
                group.querySelectorAll('input, select').forEach(input => {
                    input.name = input.name.replace(/\[\d+\]/, `[${index}]`);
                });
            });
        }
    </script>
    @endpush
</x-app-layout>






