<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create New Case</h2>
    </x-slot>
    <div class="py-8 max-w-3xl mx-auto">
        <form method="POST" action="{{ route('admin.cases.store') }}" class="bg-white shadow rounded-lg p-8 space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" class="w-full border-gray-300 rounded" required />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full border-gray-300 rounded"></textarea>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Case Type <span class="text-red-500">*</span></label>
                    <select name="case_type_id" class="w-full border-gray-300 rounded" required>
                        <option value="">Select Type</option>
                        @foreach($caseTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Branch <span class="text-red-500">*</span></label>
                    <select name="branch_id" class="w-full border-gray-300 rounded" required>
                        <option value="">Select Branch</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Work Unit</label>
                    <select name="work_unit_id" class="w-full border-gray-300 rounded">
                        <option value="">Select Work Unit</option>
                        @foreach($workUnits as $workUnit)
                            <option value="{{ $workUnit->id }}">{{ $workUnit->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Assigned Lawyer</label>
                    <select name="lawyer_id" class="w-full border-gray-300 rounded">
                        <option value="">Select Lawyer</option>
                        @foreach($lawyers as $lawyer)
                            <option value="{{ $lawyer->id }}">{{ $lawyer->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
                            <option value="{{ $lawyer->id }}">{{ $lawyer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" class="w-full border-gray-300 rounded" required>
                        <option value="open">Open</option>
                        <option value="pending">Pending</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Claimed Amount</label>
                    <input type="number" step="0.01" name="claimed_amount" class="w-full border-gray-300 rounded" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Recovered Amount</label>
                    <input type="number" step="0.01" name="recovered_amount" class="w-full border-gray-300 rounded" />
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-4">
                <a href="{{ route('admin.cases.index') }}" class="px-4 py-2 border rounded-md">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center gap-2">
                    <svg data-lucide="save" class="w-5 h-5"></svg> Create Case
                </button>
            </div>
        </form>
    </div>
</x-app-layout> 





