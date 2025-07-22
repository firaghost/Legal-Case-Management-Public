<nav x-data="{
        open: false,
        collapsed: localStorage.getItem('sidebarCollapsed') === 'true',
        init() {
            this.$watch('collapsed', (value) => {
                localStorage.setItem('sidebarCollapsed', value);
            });
        }
    }" class="min-h-screen bg-white dark:bg-gray-800/90 flex flex-col shadow-2xl border-r border-gray-300 dark:border-gray-700/50">
    <!-- Modern Sidebar Header -->
    <div class="flex items-center justify-between p-4 border-b border-gray-200/50 dark:border-gray-700/50">
        <!-- Logo/Brand Section -->
        <div class="flex items-center gap-3" x-show="!collapsed" x-transition>
            <div class="w-8 h-8 bg-gradient-to-br from-green-600 to-green-700 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #3ca44c 0%, #1e3a2e 100%);">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2m-6 4h4" />
                </svg>
            </div>
            <span class="font-bold text-gray-900 dark:text-white text-lg">LCMS</span>
        </div>
        
        <!-- Collapse Button -->
        <button @click="collapsed = !collapsed" class="group relative p-2.5 rounded-xl backdrop-blur-sm bg-white/10 dark:bg-gray-800/10 hover:bg-white/20 dark:hover:bg-gray-700/20 focus:outline-none transition-all duration-300 border border-white/20 dark:border-gray-600/20 shadow-lg hover:shadow-xl hover:scale-105">
            <svg x-show="!collapsed" class="h-5 w-5 text-gray-700 dark:text-gray-300 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
            <svg x-show="collapsed" class="h-5 w-5 text-gray-700 dark:text-gray-300 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
            </svg>
            <!-- Tooltip -->
            <div class="absolute right-full mr-2 top-1/2 transform -translate-y-1/2 px-2 py-1 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-50">
                <span x-show="!collapsed">Collapse</span>
                <span x-show="collapsed">Expand</span>
            </div>
        </button>
        
        <!-- Mobile hamburger -->
        <button @click="open = !open" class="sm:hidden p-2 rounded-xl hover:bg-white/80 dark:hover:bg-gray-700/50 focus:outline-none transition-all border border-gray-200 dark:border-gray-600">
            <svg class="h-6 w-6 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
    <!-- Enhanced User Profile Section with Glassmorphism -->
    <div class="mx-4 my-6" :class="{'mx-2': collapsed}">
        <div class="backdrop-blur-xl bg-white/20 dark:bg-gray-800/20 rounded-2xl shadow-2xl border border-white/30 dark:border-gray-600/30 overflow-hidden relative before:absolute before:inset-0 before:bg-gradient-to-br before:from-white/10 before:to-transparent before:rounded-2xl">
            <div class="p-4" :class="{'p-2': collapsed}">
                <div class="flex items-center gap-3" :class="{'justify-center': collapsed}">
                    @if(Auth::user()->profile_picture)
                        <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile Picture" class="w-12 h-12 rounded-full object-cover border-3 shadow-lg" style="border-color: #3ca44c;">
                    @else
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white text-lg font-bold shadow-lg" style="background: linear-gradient(135deg, #3ca44c 0%, #1e3a2e 100%);">
                            {{ strtoupper(substr(Auth::user()->name,0,1)) }}
                        </div>
                    @endif
                    <div x-show="!collapsed" x-transition class="flex flex-col min-w-0 flex-1">
                        <span class="font-semibold text-gray-900 dark:text-gray-100 truncate">{{ Auth::user()->name }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->email }}</span>
                        <div class="flex items-center gap-1 mt-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium text-white" style="background: linear-gradient(135deg, #3ca44c 0%, #1e3a2e 100%);">
                                <span class="w-1.5 h-1.5 rounded-full mr-1" style="background-color: #ffffff;"></span>
                                {{ ucfirst(Auth::user()->role ?? 'User') }}
                            </span>
                        </div>
                    </div>
                </div>
                <div x-show="!collapsed" x-transition class="mt-3 pt-3 border-t border-white/20 dark:border-gray-600/20">
                    <a href="{{ route('profile.edit') }}" class="group flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-white dark:hover:text-white rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg backdrop-blur-sm border border-transparent hover:border-white/20" style="--tw-gradient-from: #3ca44c; --tw-gradient-to: #1e3a2e;" onmouseover="this.style.background='linear-gradient(135deg, #3ca44c 0%, #1e3a2e 100%)'" onmouseout="this.style.background=''">
                        <div class="p-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 group-hover:bg-white/20 transition-all duration-300">
                            <svg class="w-4 h-4 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <span class="group-hover:translate-x-1 transition-transform duration-300">Edit Profile</span>
                        <svg class="w-4 h-4 ml-auto opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @php
        $role = strtolower(Auth::user()->role ?? '');
        if(!$role){
            if(Auth::user()->isAdmin()) $role = 'admin';
            elseif(Auth::user()->isSupervisor()) $role = 'supervisor';
            elseif(Auth::user()->isLawyer()) $role = 'lawyer';
            elseif(Auth::user()->roles->first()?->name === 'system administrator') $role = 'admin';
        }
        $links = [
            'lawyer' => [
                ['label' => 'Dashboard', 'route' => route('lawyer.dashboard'), 'icon' => 'home'],
                ['label' => 'New Case Entry', 'route' => route('lawyer.cases.create'), 'icon' => 'plus'],
                ['label' => 'My Cases', 'route' => route('lawyer.cases.index'), 'icon' => 'folder'],
                ['label' => 'Add Progress', 'route' => route('lawyer.progress'), 'icon' => 'clipboard-list'],
                ['label' => 'Messages', 'route' => route('chat'), 'icon' => 'message-square', 'notification' => true],
                ['label' => 'Advisory Requests', 'route' => route('lawyer.advisory'), 'icon' => 'message-circle'],
                ['label' => 'Logout', 'route' => route('logout'), 'icon' => 'logout', 'method' => 'post'],
            ],
            'supervisor' => [
                ['label' => 'Dashboard', 'route' => route('supervisor.dashboard'), 'icon' => 'home'],
                ['label' => 'All Cases', 'route' => route('supervisor.cases'), 'icon' => 'folder-open'],
                ['label' => 'Messages', 'route' => route('chat'), 'icon' => 'message-square', 'notification' => true],
                ['label' => 'Case Approvals', 'route' => route('supervisor.approvals'), 'icon' => 'check-circle'],
                ['label' => 'Assignment History', 'route' => route('supervisor.cases.assignment-history'), 'icon' => 'arrow-right-left'],
                ['label' => 'Closed Cases', 'route' => route('supervisor.closed'), 'icon' => 'archive'],
                ['label' => 'Reports', 'route' => route('supervisor.reports'), 'icon' => 'bar-chart-3'],
                ['label' => 'Advisory Reviews', 'route' => route('supervisor.advisory'), 'icon' => 'file-search'],
                ['label' => 'Logout', 'route' => route('logout'), 'icon' => 'logout', 'method' => 'post'],
            ],
            'admin' => [
                ['label' => 'Dashboard', 'route' => route('admin.dashboard'), 'icon' => 'home'],
                ['label' => 'Messages', 'route' => route('chat'), 'icon' => 'message-square', 'notification' => true],
                ['label' => 'User Management', 'route' => route('admin.users'), 'icon' => 'users'],
                ['label' => 'Permissions', 'route' => route('admin.permissions.index'), 'icon' => 'shield'],
                ['label' => 'Roles', 'route' => route('admin.roles.index'), 'icon' => 'users'],
                ['label' => 'Work Units', 'route' => route('admin.work-units.index'), 'icon' => 'briefcase'],
                ['label' => 'Branches', 'route' => route('admin.branches.index'), 'icon' => 'map-pin'],
                ['label' => 'All Cases', 'route' => route('admin.cases.index'), 'icon' => 'folder-open'],
                ['label' => 'Reports', 'route' => route('admin.reports'), 'icon' => 'bar-chart-3'],
                ['label' => 'All Cases Report', 'route' => route('admin.reports.show', 'all'), 'icon' => 'file-text'],
                ['label' => 'System Settings', 'route' => route('admin.settings'), 'icon' => 'settings'],
                ['label' => 'Audit Logs', 'route' => route('admin.logs'), 'icon' => 'clipboard-list'],
                ['label' => 'Logout', 'route' => route('logout'), 'icon' => 'logout', 'method' => 'post'],
            ],
        ];
    @endphp
    <!-- Enhanced Navigation Menu -->
    <div :class="{'w-64': !collapsed, 'w-20': collapsed}" class="flex flex-col flex-1 transition-all duration-300 ease-in-out bg-white dark:bg-gray-800/50 overflow-hidden" style="min-width: 5rem; max-width: 16rem;">
        <div class="flex-1 px-4 py-2">
            <nav class="space-y-1">
                @foreach ($links[$role] ?? [] as $index => $link)
                    @if (($link['label'] ?? '') === 'Logout')
                        <div class="mt-8 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <form method="POST" action="{{ $link['route'] }}">
                                @csrf
                                <button type="submit" class="group relative flex items-center w-full text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200 transform hover:scale-[1.02]" 
                                        :class="{'px-3 py-3 rounded-xl': !collapsed, 'px-2 py-4 mx-2 rounded-2xl justify-center': collapsed}">
                                    <div class="flex items-center justify-center bg-red-100 dark:bg-red-900/30 group-hover:bg-red-200 dark:group-hover:bg-red-900/50 transition-colors duration-200" 
                                         :class="{'w-10 h-10 rounded-lg': !collapsed, 'w-12 h-12 rounded-xl': collapsed}">
                                        <svg data-lucide="log-out" class="transition-all duration-200" :class="{'w-5 h-5': !collapsed, 'w-6 h-6': collapsed}"></svg>
                                    </div>
                                    <span x-show="!collapsed" x-transition class="ml-3 font-medium">{{ $link['label'] }}</span>
                                    <!-- Tooltip for collapsed state -->
                                    <div x-show="collapsed" class="absolute left-full ml-2 top-1/2 transform -translate-y-1/2 px-3 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-50 shadow-lg">
                                        {{ $link['label'] }}
                                        <div class="absolute right-full top-1/2 transform -translate-y-1/2 w-0 h-0 border-t-4 border-b-4 border-r-4 border-transparent border-r-gray-900 dark:border-r-gray-700"></div>
                                    </div>
                                </button>
                            </form>
                        </div>
                    @else
                        @php
                            $isActive = url()->current() === $link['route'] || (isset($link['active_routes']) && in_array(request()->route()->getName(), $link['active_routes']));
                        @endphp
                        <a href="{{ $link['route'] }}" 
                           class="group relative flex items-center transition-all duration-200 transform hover:scale-[1.02] {{ $isActive ? 'bg-gradient-to-r from-green-600 to-green-700 text-white shadow-lg shadow-green-500/25' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}" 
                           :class="{'px-3 py-3 rounded-xl': !collapsed, 'px-2 py-4 mx-2 rounded-2xl justify-center': collapsed}">
                            <div class="flex items-center justify-center transition-colors duration-200 {{ $isActive ? 'bg-white/20 text-white' : 'bg-gray-100 dark:bg-gray-800 group-hover:bg-gray-200 dark:group-hover:bg-gray-700' }}" 
                                 :class="{'w-10 h-10 rounded-lg': !collapsed, 'w-12 h-12 rounded-xl': collapsed}">
                                <svg data-lucide="{{ $link['icon'] ?? 'circle' }}" class="transition-all duration-200" :class="{'w-5 h-5': !collapsed, 'w-6 h-6': collapsed}"></svg>
                            </div>
                            <span x-show="!collapsed" x-transition class="ml-3 font-medium">{{ $link['label'] }}</span>
                            <!-- Tooltip for collapsed state -->
                            <div x-show="collapsed" class="absolute left-full ml-2 top-1/2 transform -translate-y-1/2 px-3 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-50 shadow-lg">
                                {{ $link['label'] }}
                                <div class="absolute right-full top-1/2 transform -translate-y-1/2 w-0 h-0 border-t-4 border-b-4 border-r-4 border-transparent border-r-gray-900 dark:border-r-gray-700"></div>
                            </div>
                            @if(($link['notification'] ?? false) && ($count = auth()->user()->unreadChatCount()) > 0)
                                <span x-show="!collapsed" class="ml-auto inline-flex items-center justify-center w-6 h-6 text-xs font-bold rounded-full bg-red-500 text-white shadow-lg animate-pulse">
                                    {{ $count > 99 ? '99+' : $count }}
                                </span>
                            @endif
                            @if($isActive)
                                <div class="absolute right-0 w-1 h-8 bg-white rounded-l-full"></div>
                            @endif
                        </a>
                    @endif
                @endforeach
            </nav>
        </div>
    </div>
    <!-- Responsive Navigation Menu (Mobile) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-transparent">
        <div class="pt-2 pb-3 space-y-1">
            @foreach ($links[$role] ?? [] as $link)
                @if (($link['label'] ?? '') === 'Logout')
                    <form method="POST" action="{{ $link['route'] }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-3 py-2 rounded flex items-center gap-2">
                            <svg data-lucide="log-out" class="w-5 h-5"></svg>
                            {{ $link['label'] }}
                        </button>
                    </form>
                @else
                    <a href="{{ $link['route'] }}" class="block px-3 py-2 rounded flex items-center gap-2 {{ url()->current() === $link['route'] ? '' : '' }}">
                        <svg data-lucide="{{ $link['icon'] ?? 'circle' }}" class="w-5 h-5"></svg>
                        {{ $link['label'] }}
                    </a>
                @endif
            @endforeach
        </div>
    </div>
</nav>






