<x-app-layout>
    <x-slot name="header">
        <!-- Modern Header with Gradient Background -->
        <div class="relative bg-gradient-to-r from-green-600 via-teal-600 to-blue-600 dark:from-green-800 dark:via-teal-800 dark:to-blue-800 overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-black/10 dark:bg-black/20"></div>
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.15) 1px, transparent 0); background-size: 20px 20px;"></div>
            
            <div class="relative px-6 py-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-white/20 dark:bg-white/10 rounded-lg backdrop-blur-sm">
                            <i class="fas fa-cogs text-2xl text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-white">System Settings</h1>
                            <p class="text-green-100 dark:text-green-200 mt-1">Configure essential system parameters</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Court Codes Management -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-gavel text-blue-600 dark:text-blue-400"></i>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Court Codes</h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Manage court identification codes</span>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('admin.settings.court-codes.update') }}" class="p-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4" id="court-codes-container">
                        @forelse($courtCodes ?? [] as $code => $name)
                            <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg court-code-row">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Code</label>
                                    <input type="text" name="codes[{{ $loop->index }}][code]" value="{{ $code }}" 
                                           class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" 
                                           placeholder="e.g., HC001">
                                </div>
                                <div class="flex-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Court Name</label>
                                    <input type="text" name="codes[{{ $loop->index }}][name]" value="{{ $name }}" 
                                           class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" 
                                           placeholder="e.g., High Court of Justice">
                                </div>
                                <button type="button" class="delete-court-code mt-6 p-2 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                <i class="fas fa-gavel text-3xl mb-3"></i>
                                <p>No court codes configured yet.</p>
                            </div>
                        @endforelse
                    </div>
                    
                    <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-200 dark:border-gray-600">
                        <button type="button" id="add-court-code" 
                                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <i class="fas fa-plus mr-2"></i> Add Court Code
                        </button>
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transform hover:-translate-y-0.5 transition-all duration-200">
                            <i class="fas fa-save mr-2"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Case Type Labels -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-green-50 to-teal-50 dark:from-green-900/20 dark:to-teal-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-tags text-green-600 dark:text-green-400"></i>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Case Type Labels</h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Define available case categories</span>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('admin.settings.case-types.update') }}" class="p-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="case-types-container">
                        @forelse($caseTypes ?? [] as $type)
                            <div class="case-type-row">
                                <div class="flex items-center gap-2">
                                    <input type="text" name="case_types[]" value="{{ $type }}" 
                                           class="flex-1 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 transition-colors" 
                                           placeholder="e.g., Criminal Law">
                                    <button type="button" class="delete-case-type p-2 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 text-center py-8 text-gray-500 dark:text-gray-400">
                                <i class="fas fa-tags text-3xl mb-3"></i>
                                <p>No case types configured yet.</p>
                            </div>
                        @endforelse
                    </div>
                    
                    <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-200 dark:border-gray-600">
                        <button type="button" id="add-case-type" 
                                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                            <i class="fas fa-plus mr-2"></i> Add Case Type
                        </button>
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transform hover:-translate-y-0.5 transition-all duration-200">
                            <i class="fas fa-save mr-2"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- System Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-info-circle text-purple-600 dark:text-purple-400"></i>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">System Information</h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Current system status</span>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $totalCases ?? 0 }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total Cases</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $totalUsers ?? 0 }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Active Users</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $systemVersion ?? '1.0.0' }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">System Version</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Simple row deletion for dynamic tables
            document.querySelectorAll('.delete-court-code').forEach(btn => {
                btn.addEventListener('click', () => {
                    btn.closest('.court-code-row').remove();
                });
            });

            document.getElementById('add-court-code')?.addEventListener('click', () => {
                const container = document.getElementById('court-codes-container');
                const index = container.children.length;
                container.insertAdjacentHTML('beforeend', `
                    <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg court-code-row">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Code</label>
                            <input type="text" name="codes[${index}][code]" 
                                   class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" 
                                   placeholder="e.g., HC001">
                        </div>
                        <div class="flex-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Court Name</label>
                            <input type="text" name="codes[${index}][name]" 
                                   class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" 
                                   placeholder="e.g., High Court of Justice">
                        </div>
                        <button type="button" class="delete-court-code mt-6 p-2 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>`);
            });

            document.querySelectorAll('.delete-case-type').forEach(btn => {
                btn.addEventListener('click', () => {
                    btn.closest('.case-type-row').remove();
                });
            });

            document.getElementById('add-case-type')?.addEventListener('click', () => {
                const container = document.getElementById('case-types-container');
                const index = container.children.length;
                container.insertAdjacentHTML('beforeend', `
                    <div class="case-type-row">
                        <div class="flex items-center gap-2">
                            <input type="text" name="case_types[]" 
                                   class="flex-1 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 transition-colors" 
                                   placeholder="e.g., Criminal Law">
                            <button type="button" class="delete-case-type p-2 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>`);
            });
        </script>
    @endpush
</x-app-layout>





