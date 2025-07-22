<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <!-- Modern Page Header -->
            <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-800 dark:to-indigo-800 rounded-3xl shadow-2xl mb-8">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.1\"%3E%3Ccircle cx=\"30\" cy=\"30\" r=\"4\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>
                <div class="relative px-8 py-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-white mb-1">Edit Other Civil Litigation Case</h1>
                                <p class="text-white/90 text-lg truncate max-w-xs sm:max-w-none">{{ $case->title }}</p>
                            </div>
                        </div>
                        <a href="{{ route('lawyer.cases.show', $case) }}" 
                           class="inline-flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm border border-white/30 rounded-xl font-semibold text-sm text-white hover:bg-white/30 focus:outline-none focus:ring-2 focus:ring-white/50 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                            </svg>
                            Back to Case
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card Container -->
            <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-3xl overflow-hidden border border-gray-200 dark:border-gray-700 p-8">
    <form action="{{ route('lawyer.cases.update', $case) }}" method="POST" enctype="multipart/form-data" x-data="otherCivilLitigationForm()" x-init="init()">
        @csrf
        @method('PUT')
        @include('lawyer.cases.partials.civil_form', [
            'case' => $case,
            'caseTypeData' => $caseTypeData,
            'branches' => $branches,
            'workUnits' => $workUnits,
            'lawyers' => $lawyers
        ])
    </form>
    @push('scripts')
    <script>
    function otherCivilLitigationForm() {
        return {
            form: {
                plaintiffs: @json(old('plaintiffs', $plaintiffs)),
                defendants: @json(old('defendants', $defendants)),
                claimed_amount: '{{ old('claimed_amount', $case->claimed_amount) }}',
                recovered_amount: '{{ old('recovered_amount', $caseTypeData->recovered_amount ?? '') }}',
                company_file_number: '{{ old('company_file_number', $case->file_number) }}',
                claimed_thing: '{{ old('claimed_thing', $caseTypeData->claimed_thing ?? '') }}',
                branch_id: '{{ old('branch_id', $case->branch_id) }}',
                work_unit_id: '{{ old('work_unit_id', $case->work_unit_id) }}',
                court_name: '{{ old('court_name', $case->court_name) }}',
                court_file_number: '{{ old('court_file_number', $case->file_number) }}',
            },
            isSupervisor: @json(auth()->user()->isSupervisor()),
            showCloseModal: false,
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
    </script>
    @endpush
</x-app-layout> 





