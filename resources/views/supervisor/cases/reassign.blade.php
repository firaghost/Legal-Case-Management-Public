{{-- Reassignment Modal --}}
<div id="reassignModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Reassign Case</h3>
                <button onclick="closeReassignModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form action="{{ route('supervisor.cases.reassign', $case->id) }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Current Lawyer
                    </label>
                    <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <span class="text-gray-900 dark:text-white font-semibold">
                            {{ $case->lawyer->name ?? 'Unassigned' }}
                        </span>
                    </div>
                </div>

                <div>
                    <label for="new_lawyer_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        New Lawyer <span class="text-red-500">*</span>
                    </label>
                    <select name="new_lawyer_id" id="new_lawyer_id" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Select a lawyer...</option>
                        {{-- This will be populated via JavaScript --}}
                    </select>
                    <div id="lawyer-loading" class="hidden mt-2 text-sm text-gray-500">Loading lawyers...</div>
                </div>

                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Reason for Reassignment (Optional)
                    </label>
                    <textarea name="reason" id="reason" rows="3" maxlength="500" placeholder="Enter reason for reassignment..."
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"></textarea>
                    <div class="mt-1 text-xs text-gray-500">Maximum 500 characters</div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeReassignModal()"
                        class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 transition-colors">
                        Reassign Case
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openReassignModal() {
    document.getElementById('reassignModal').classList.remove('hidden');
    loadLawyers();
}

function closeReassignModal() {
    document.getElementById('reassignModal').classList.add('hidden');
}

async function loadLawyers() {
    const select = document.getElementById('new_lawyer_id');
    const loading = document.getElementById('lawyer-loading');
    
    loading.classList.remove('hidden');
    
    try {
        const response = await fetch('/lawyers');
        const lawyers = await response.json();
        
        // Clear existing options except the first one
        select.innerHTML = '<option value="">Select a lawyer...</option>';
        
        lawyers.forEach(lawyer => {
            // Don't show the currently assigned lawyer
            @if($case->lawyer)
            if (lawyer.id !== {{ $case->lawyer->id }}) {
            @endif
                const option = document.createElement('option');
                option.value = lawyer.id;
                option.textContent = lawyer.name + (lawyer.email ? ` (${lawyer.email})` : '');
                select.appendChild(option);
            @if($case->lawyer)
            }
            @endif
        });
    } catch (error) {
        console.error('Error loading lawyers:', error);
        select.innerHTML = '<option value="">Error loading lawyers</option>';
    } finally {
        loading.classList.add('hidden');
    }
}

// Close modal when clicking outside
document.getElementById('reassignModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeReassignModal();
    }
});
</script>






