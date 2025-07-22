<x-app-layout>
    <x-slot name="header">
        <!-- Modern Header with Gradient Background -->
        <div class="relative bg-gradient-to-r from-indigo-600 via-purple-600 to-blue-600 dark:from-indigo-800 dark:via-purple-800 dark:to-blue-800 overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-black/10 dark:bg-black/20"></div>
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.15) 1px, transparent 0); background-size: 20px 20px;"></div>
            
            <div class="relative px-6 py-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-white/20 dark:bg-white/10 rounded-lg backdrop-blur-sm">
                            <i class="fas fa-clipboard-list text-2xl text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-white">Audit Logs</h1>
                            <p class="text-indigo-100 dark:text-indigo-200 mt-1">Monitor system activities and user actions</p>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('admin.logs.export', array_merge(request()->all(), ['format' => 'excel'])) }}" 
                           class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                            <i class="fas fa-file-excel mr-2"></i> Export Excel
                        </a>
                        <a href="{{ route('admin.logs.export', array_merge(request()->all(), ['format' => 'pdf'])) }}" 
                           class="inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                            <i class="fas fa-file-pdf mr-2"></i> Export PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Modern Filters Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-filter text-indigo-600 dark:text-indigo-400"></i>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Filter Audit Logs</h3>
                    </div>
                </div>
                
                <form method="GET" class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Date Range -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                <i class="fas fa-calendar-alt mr-1 text-indigo-500"></i>Date Range
                            </label>
                            <div class="grid grid-cols-1 gap-2">
                                <input type="date" name="start_date" value="{{ request('start_date') }}" 
                                       class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                                <input type="date" name="end_date" value="{{ request('end_date') }}" 
                                       class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                            </div>
                        </div>
                        
                        <!-- User Filter -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                <i class="fas fa-user mr-1 text-green-500"></i>User
                            </label>
                            <select name="user_id" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                                <option value="">All Users</option>
                                @foreach(($users ?? []) as $id => $name)
                                    <option value="{{ $id }}" @selected(request('user_id') == $id)>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Action Type Filter -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                <i class="fas fa-bolt mr-1 text-yellow-500"></i>Action Type
                            </label>
                            <select name="action" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                                <option value="">All Actions</option>
                                <option value="LOGIN" @selected(request('action') == 'LOGIN')>Login</option>
                                <option value="LOGOUT" @selected(request('action') == 'LOGOUT')>Logout</option>
                                <option value="CREATE_" @selected(str_starts_with(request('action'), 'CREATE_'))>Create</option>
                                <option value="UPDATE_" @selected(str_starts_with(request('action'), 'UPDATE_'))>Update</option>
                                <option value="DELETE_" @selected(str_starts_with(request('action'), 'DELETE_'))>Delete</option>
                            </select>
                        </div>
                        
                        <!-- Model Type Filter -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                <i class="fas fa-database mr-1 text-purple-500"></i>Model Type
                            </label>
                            <select name="auditable_type" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                                <option value="">All Models</option>
                                @foreach($modelTypes ?? [] as $type => $name)
                                    <option value="{{ $type }}" @selected(request('auditable_type') == $type)>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- Search Box -->
                    <div class="space-y-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            <i class="fas fa-search mr-1 text-blue-500"></i>Search in Changes
                        </label>
                        <div class="relative">
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                   class="block w-full pl-10 pr-4 py-3 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors" 
                                   placeholder="Search in changes...">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                        <a href="{{ route('admin.logs') }}" 
                           class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            <i class="fas fa-sync-alt mr-2"></i> Reset Filters
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center justify-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform hover:-translate-y-0.5 transition-all duration-200">
                            <i class="fas fa-filter mr-2"></i> Apply Filters
                        </button>
                    </div>
                </form>
            </div>

           

            <!-- Modern Data Display -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-list text-indigo-600 dark:text-indigo-400"></i>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Audit Log Entries</h3>
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $logs->total() ?? 0 }} total entries
                        </div>
                    </div>
                </div>

                <!-- Desktop Table View -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-clock mr-1"></i>Date/Time
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-user mr-1"></i>User
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-bolt mr-1"></i>Action
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-database mr-1"></i>Model
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-hashtag mr-1"></i>ID
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-edit mr-1"></i>Changes
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                            @forelse($logs as $log)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        <div class="flex flex-col">
                                            <span class="font-medium">{{ $log->created_at->format('M d, Y') }}</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $log->created_at->format('H:i:s') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center text-white text-xs font-medium">
                                                {{ substr(optional($log->user)->name ?? 'S', 0, 1) }}
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ optional($log->user)->name ?? 'System' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $actionClass = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ';
                                            if (str_contains(strtolower($log->action), 'delete')) {
                                                $actionClass .= 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
                                                $icon = 'fas fa-trash';
                                            } elseif (str_contains(strtolower($log->action), 'create')) {
                                                $actionClass .= 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                                                $icon = 'fas fa-plus';
                                            } elseif (str_contains(strtolower($log->action), 'update')) {
                                                $actionClass .= 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
                                                $icon = 'fas fa-edit';
                                            } elseif (str_contains(strtolower($log->action), 'login')) {
                                                $actionClass .= 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200';
                                                $icon = 'fas fa-sign-in-alt';
                                            } else {
                                                $actionClass .= 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
                                                $icon = 'fas fa-cog';
                                            }
                                        @endphp
                                        <span class="{{ $actionClass }}">
                                            <i class="{{ $icon }} mr-1"></i>{{ $log->action }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        @php
                                            $type = $log->auditable_type;
                                            if (class_exists($type)) {
                                                $type = class_basename($type);
                                            }
                                            $type = str_replace('\\', ' ', $type);
                                            $type = str_replace('Models ', '', $type);
                                            $type = preg_replace('/([a-z])([A-Z])/s','$1 $2', $type);
                                            echo $type ?: 'System';
                                        @endphp
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $log->auditable_id }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="max-w-xs">
                                            @if(is_array($log->changes))
                                                <div class="space-y-1">
                                                    @foreach(array_slice($log->changes, 0, 2) as $key => $value)
                                                        <div class="text-xs">
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                                {{ $key }}
                                                            </span>
                                                            <span class="ml-1 text-gray-600 dark:text-gray-400">{{ is_array($value) ? json_encode($value) : Str::limit($value, 30) }}</span>
                                                        </div>
                                                    @endforeach
                                                    @if(count($log->changes) > 2)
                                                        <div class="text-xs text-gray-500 dark:text-gray-400">+{{ count($log->changes) - 2 }} more changes</div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ Str::limit($log->changes, 50) }}</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-clipboard-list text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No audit logs found</h3>
                                            <p class="text-gray-500 dark:text-gray-400">Try adjusting your filters to see more results.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="lg:hidden">
                    <div class="divide-y divide-gray-200 dark:divide-gray-600">
                        @forelse($logs as $log)
                            <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center text-white font-medium">
                                            {{ substr(optional($log->user)->name ?? 'S', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ optional($log->user)->name ?? 'System' }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $log->created_at->format('M d, Y H:i:s') }}
                                            </div>
                                        </div>
                                    </div>
                                    @php
                                        $actionClass = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ';
                                        if (str_contains(strtolower($log->action), 'delete')) {
                                            $actionClass .= 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
                                            $icon = 'fas fa-trash';
                                        } elseif (str_contains(strtolower($log->action), 'create')) {
                                            $actionClass .= 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                                            $icon = 'fas fa-plus';
                                        } elseif (str_contains(strtolower($log->action), 'update')) {
                                            $actionClass .= 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
                                            $icon = 'fas fa-edit';
                                        } elseif (str_contains(strtolower($log->action), 'login')) {
                                            $actionClass .= 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200';
                                            $icon = 'fas fa-sign-in-alt';
                                        } else {
                                            $actionClass .= 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
                                            $icon = 'fas fa-cog';
                                        }
                                    @endphp
                                    <span class="{{ $actionClass }}">
                                        <i class="{{ $icon }} mr-1"></i>{{ $log->action }}
                                    </span>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Model:</span>
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            @php
                                                $type = $log->auditable_type;
                                                if (class_exists($type)) {
                                                    $type = class_basename($type);
                                                }
                                                $type = str_replace('\\', ' ', $type);
                                                $type = str_replace('Models ', '', $type);
                                                $type = preg_replace('/([a-z])([A-Z])/s','$1 $2', $type);
                                                echo $type ?: 'System';
                                            @endphp
                                        </div>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">ID:</span>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $log->auditable_id }}</div>
                                    </div>
                                </div>
                                
                                @if($log->changes)
                                    <div class="mt-3">
                                        <span class="text-gray-500 dark:text-gray-400 text-sm">Changes:</span>
                                        <div class="mt-1">
                                            @if(is_array($log->changes))
                                                <div class="space-y-1">
                                                    @foreach(array_slice($log->changes, 0, 3) as $key => $value)
                                                        <div class="text-xs">
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                                {{ $key }}
                                                            </span>
                                                            <span class="ml-1 text-gray-600 dark:text-gray-400">{{ is_array($value) ? json_encode($value) : Str::limit($value, 40) }}</span>
                                                        </div>
                                                    @endforeach
                                                    @if(count($log->changes) > 3)
                                                        <div class="text-xs text-gray-500 dark:text-gray-400">+{{ count($log->changes) - 3 }} more changes</div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ Str::limit($log->changes, 100) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="p-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-clipboard-list text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No audit logs found</h3>
                                    <p class="text-gray-500 dark:text-gray-400">Try adjusting your filters to see more results.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Pagination -->
            @if(isset($logs) && method_exists($logs, 'links'))
                <div class="bg-white dark:bg-gray-800 px-6 py-4 border-t border-gray-200 dark:border-gray-600">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>

</x-app-layout>






