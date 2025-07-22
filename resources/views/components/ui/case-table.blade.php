@props([
    // LengthAwarePaginator or collection of cases with already-applied filter/pagination
    'cases',
    // array of ['title' => 'File No.', 'accessor' => 'file_no']
    'columns' => [],
    // array of action definitions; e.g. ['label' => 'View', 'route' => fn($case)=> route('cases.show', $case), 'icon' => 'eye']
    'actions' => [],
])

<div class="space-y-4">
    <!-- Filter & search bar -->
    <form method="GET" class="flex flex-col sm:flex-row sm:items-center gap-2">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by File No. or Name" class="w-full border-gray-300 rounded-md" />
        </div>
        <select name="status" class="border-gray-300 rounded-md">
            <option value="">All Status</option>
            <option value="open" @selected(request('status')==='open')>Open</option>
            <option value="closed" @selected(request('status')==='closed')>Closed</option>
            <option value="pending" @selected(request('status')==='pending')>Pending</option>
        </select>
        <select name="type" class="border-gray-300 rounded-md">
            <option value="">All Types</option>
            <option value="civil" @selected(request('type')==='civil')>Civil</option>
            <option value="criminal" @selected(request('type')==='criminal')>Criminal</option>
            <option value="labor" @selected(request('type')==='labor')>Labor</option>
            <option value="loan" @selected(request('type')==='loan')>Loan</option>
        </select>
        <button class="px-4 py-2 bg-emerald-600 text-white rounded-md">Filter</button>
    </form>

    <div class="overflow-x-auto rounded-lg shadow ring-1 ring-black ring-opacity-5">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 sticky top-0 z-10">
                <tr>
                    @foreach($columns as $col)
                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">{{ $col['title'] }}</th>
                    @endforeach
                    @if(count($actions))
                        <th class="px-3 py-2 text-center font-medium text-gray-500 uppercase tracking-wide">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($cases as $case)
                    <tr class="hover:bg-gray-50">
                        @foreach($columns as $col)
                            <td class="px-3 py-2 whitespace-nowrap">{{ data_get($case, $col['accessor']) }}</td>
                        @endforeach
                        @if(count($actions))
                            <td class="px-3 py-2 whitespace-nowrap text-center space-x-1">
                                @foreach($actions as $action)
                                    @php $route = is_callable($action['route']) ? $action['route']($case) : $action['route']; @endphp
                                    <a href="{{ $route }}" class="inline-flex items-center px-2 py-1 text-xs rounded bg-gray-100 hover:bg-gray-200 border border-gray-300">
                                        @if(isset($action['icon']))<svg data-lucide="{{ $action['icon'] }}" class="w-3 h-3 mr-1"></svg>@endif{{ $action['label'] }}
                                    </a>
                                @endforeach
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns)+1 }}" class="px-3 py-6 text-center text-gray-500">No cases found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(method_exists($cases, 'links'))
        <div>
            {{ $cases->links() }}
        </div>
    @endif
</div>






