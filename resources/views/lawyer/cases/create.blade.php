<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8" x-data="caseForm()" x-init="init()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <!-- Modern Page Header -->
            <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-800 dark:to-indigo-800 rounded-3xl shadow-2xl mb-8">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.1"%3E%3Ccircle cx="30" cy="30" r="4"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>
                <div class="relative px-8 py-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-white mb-1">Create New Case</h1>
                                <p class="text-white/90 text-lg">Select a case type to begin filling out the required information.</p>
                            </div>
                        </div>
                        <a href="{{ route('lawyer.cases.index') }}" 
                           class="inline-flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm border border-white/30 rounded-xl font-semibold text-sm text-white hover:bg-white/30 focus:outline-none focus:ring-2 focus:ring-white/50 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                            </svg>
                            Back to Cases
                        </a>
                    </div>
                </div>
            </div>

            <form action="{{ route('lawyer.cases.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf


                @if ($errors->any())
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-6 py-4 rounded-2xl shadow-lg">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <h4 class="font-semibold">Please correct the following errors:</h4>
                        </div>
                        <ul class="list-disc pl-7 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <!-- Card 1: Case Type Selection -->
                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-3xl overflow-hidden border border-gray-200 dark:border-gray-700 transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Case Type Selection</h3>
                        </div>
                    </div>
                    <div class="p-8">
                        <div class="w-full">
                            <label for="case_type" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                Select Case Type <span class="text-red-500">*</span>
                            </label>
                            <select id="case_type" x-model="type" name="type" 
                                    class="block w-full px-4 py-3 text-base border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                    required>
                                <option value="">Select a case type</option>
                                <option value="clean_loan" data-abbreviation="CLN">Clean Loan Recovery</option>
                                <option value="secured_loan" data-abbreviation="SCN">Secured Loan Recovery</option>
                                <option value="labor" data-abbreviation="LBR">Labor Litigation</option>
                                <option value="civil" data-abbreviation="CIV">Other Civil Litigation</option>
                                <option value="criminal" data-abbreviation="CRM">Criminal Litigation</option>
                                <option value="advisory" data-abbreviation="ADV">Legal Advisory & Document Review</option>
                            </select>
                            @error('type')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Card 2: Dynamic Case Information -->
                <div x-show="type" x-cloak 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform -translate-y-4"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     class="bg-white dark:bg-gray-800 shadow-xl rounded-3xl overflow-hidden border border-gray-200 dark:border-gray-700 transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
                    
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 001 1h6a1 1 0 001-1V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white" x-text="caseTypeTitle">Case Information</h3>
                            </div>
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-200" x-show="type">
                                <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                                <span x-text="type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())">Selected Type</span>
                            </span>
                        </div>
                    </div>
                    <div class="p-8">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
                            <div class="sm:col-span-3">
                                <label for="company_file_number" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Company File Number</label>
                                <input type="text" name="company_file_number" x-model="form.company_file_number" readonly
                                       class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 rounded-xl cursor-not-allowed">
                            </div>
                        </div>
                        
                        <!-- Dynamic Partials -->
                        <template x-if="type==='clean_loan'">
                            <div>
                            @include('lawyer.cases.partials.clean_loan_form', ['branches' => $branches, 'workUnits' => $workUnits, 'lawyers' => $lawyers])
                        </div>
                            </template>
                        <template x-if="type==='secured_loan'">
                            <div>
                            @include('lawyer.cases.partials.secured_loan_form', ['branches' => $branches, 'workUnits' => $workUnits, 'lawyers' => $lawyers])
                        </div>
                            </template>
                        <template x-if="type==='labor'">
                            <div>
                            @include('lawyer.cases.partials.labor_form', ['branches' => $branches, 'workUnits' => $workUnits, 'lawyers' => $lawyers])
                        </div>
                            </template>
                        <template x-if="type==='civil'">
                            <div>
                            @include('lawyer.cases.partials.civil_form', ['branches' => $branches, 'workUnits' => $workUnits, 'lawyers' => $lawyers])
                        </div>
                            </template>
                        <template x-if="type==='criminal'">
                            <div>
                            @include('lawyer.cases.partials.criminal_form', ['branches' => $branches, 'workUnits' => $workUnits, 'lawyers' => $lawyers])
                        </div>
                            </template>
                        <template x-if="type==='advisory'">
                            <div x-data="{tab:'advice', reviewStep:1, adviceStep:1}">
                            @include('lawyer.cases.partials.advisory_form', ['branches' => $branches, 'workUnits' => $workUnits, 'lawyers' => $lawyers])
                        </div>
                            </template>

                    <div class="bg-gray-50 dark:bg-gray-800/50 px-8 py-6 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                        <button type="submit"
                                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Save Case
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    @push('styles')
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('caseForm', () => ({
                type: '',
                tab: 'advice',
                reviewStep: 1,
                form: {
                    company_file_number: ''
                },
                caseTitle: '',
                isSupervisor: @json(auth()->user()->isSupervisor()),
                showCloseModal: false,
                caseTypeTitle: 'Case Information',
                
                init() {
                    // Any initialization code
                    this.updateCaseNumber();
                    // Watch for type changes to update title
                    this.$watch('type', (newType) => {
                        this.updateCaseTitle(newType);
                    });
                },
                
                updateCaseNumber() {
                    // Your existing case number generation logic
                },
                
                updateCaseTitle(caseType) {
                    const titles = {
                        'clean_loan': 'Clean Loan Recovery Case',
                        'secured_loan': 'Secured Loan Recovery Case',
                        'labor': 'Labor Litigation Case',
                        'civil': 'Other Civil Litigation Case',
                        'criminal': 'Criminal Litigation Case',
                        'advisory': 'Legal Advisory & Document Review Case'
                    };
                    this.caseTitle = titles[caseType] || '';
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>






