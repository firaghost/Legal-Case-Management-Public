<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Branches</h2>
            <a href="#" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                <svg data-lucide="plus-circle" class="w-5 h-5"></svg>
                <span>Add Branch</span>
            </a>
        </div>
    </x-slot>
    <div class="py-6 max-w-4xl mx-auto">
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <svg data-lucide="map-pin" class="w-5 h-5"></svg> Branch List
            </h3>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Active</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    {{-- @foreach($branches as $branch) --}}
                    <tr>
                        <td class="px-4 py-2">Main Branch</td>
                        <td class="px-4 py-2">MAIN</td>
                        <td class="px-4 py-2"><span class="inline-block px-2 py-0.5 rounded bg-green-100 text-green-800 text-xs">Active</span></td>
                        <td class="px-4 py-2">
                            <button class="text-blue-600 hover:underline">Edit</button>
                            <button class="text-red-600 hover:underline ml-2">Delete</button>
                        </td>
                    </tr>
                    {{-- @endforeach --}}
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout> 





