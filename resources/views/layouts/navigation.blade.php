<div class="flex min-h-screen w-full">
    <nav x-data="{ open: false, collapsed: false }" class="min-h-screen bg-gray-900 text-white flex flex-col shadow-lg w-64 relative z-10">
        <!-- Sidebar Toggle (Desktop) -->
        <div class="flex items-center justify-between h-16 px-4 border-b border-gray-800">
            <div class="flex items-center gap-2">
                <button @click="collapsed = !collapsed" class="p-2 rounded-md hover:bg-gray-800 focus:outline-none transition">
                    <svg x-show="!collapsed" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    <svg x-show="collapsed" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <x-application-logo class="block h-8 w-auto fill-current text-white" />
                    <span x-show="!collapsed" class="font-bold text-lg tracking-wide ml-2">LCMS</span>
                </a>
            </div>
            @auth
            @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
            @if(auth()->user()->isSupervisor() || auth()->user()->hasAnyRole(['lawyer','Lawyer']) || (property_exists(auth()->user(),'role') && auth()->user()->role === 'lawyer'))
                <div x-data="{ notifOpen: false }" class="relative mr-4">
                    <button @click="notifOpen = !notifOpen" class="relative focus:outline-none">
                        <svg class="w-6 h-6 text-blue-100 hover:text-white transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if($unread > 0)
                            <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full px-1">{{ $unread }}</span>
                        @endif
                    </button>
                    <div x-show="notifOpen" @click.away="notifOpen = false" x-cloak class="absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded shadow-lg z-50">
                        <div class="p-3 border-b font-semibold text-blue-700">Notifications</div>
                        <ul class="max-h-80 overflow-y-auto">
                            @forelse(auth()->user()->unreadNotifications as $notification)
                                <li class="px-4 py-3 border-b hover:bg-blue-50 transition flex flex-col">
                                    <div class="flex items-center justify-between">
                                        <span class="font-semibold text-blue-800">Case #{{ $notification->data['file_number'] ?? '' }}</span>
                                        <span class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="text-sm text-gray-700">{{ $notification->data['title'] ?? '' }}</div>
                                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="mt-2">
                                        @csrf
                                        <button type="submit" class="text-xs text-blue-600 hover:underline">Mark as read</button>
                                    </form>
                                </li>
                            @empty
                                <li class="px-4 py-3 text-gray-500">No new notifications.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            @endif
            @endauth

            <!-- Hamburger for mobile -->
            <button @click="open = !open" class="sm:hidden p-2 rounded-md hover:bg-gray-800 focus:outline-none transition">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /><path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <!-- Sidebar Menu (Desktop) -->
        <div :class="{'w-64': !collapsed, 'w-20': collapsed}" class="flex flex-col flex-1 transition-all duration-300 ease-in-out bg-gray-900 overflow-hidden" style="min-width: 5rem; max-width: 16rem;">
            <ul class="flex-1 py-6 space-y-2">
                <li>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('dashboard') ? 'bg-gray-800' : '' }}">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6" /></svg>
                        <span x-show="!collapsed">Dashboard</span>
                    </a>
                </li>
                <!-- Add more menu items as needed, with icons -->
                <li>
                    <a href="{{ route('supervisor.cases') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('supervisor.cases') ? 'bg-gray-800' : '' }}">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="13" rx="2" /><path d="M16 3v4M8 3v4" /></svg>
                        <span x-show="!collapsed">All Cases</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('supervisor.approvals') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('supervisor.approvals') ? 'bg-gray-800' : '' }}">
                        <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6" /><circle cx="12" cy="19" r="2" /></svg>
                        <span x-show="!collapsed">Approvals</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('supervisor.cases', ['type' => 6]) }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->fullUrlIs('*type=6*') ? 'bg-gray-800' : '' }}">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 17l4 4 4-4m0-5V3" /></svg>
                        <span x-show="!collapsed">Advisory</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('supervisor.closed') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('supervisor.closed') ? 'bg-gray-800' : '' }}">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        <span x-show="!collapsed">Closed Cases</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('supervisor.cases.assignment-history') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('supervisor.cases.assignment-history') ? 'bg-gray-800' : '' }}">
                        <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                        <span x-show="!collapsed">Assignment History</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('supervisor.reports') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('supervisor.reports') ? 'bg-gray-800' : '' }}">
                        <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6" /><circle cx="12" cy="19" r="2" /></svg>
                        <span x-show="!collapsed">Reports</span>
                    </a>
                </li>
            </ul>
            <!-- User/Profile/Logout -->
            <div class="px-4 py-4 border-t border-gray-800 flex flex-col gap-2">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center text-lg font-bold">{{ strtoupper(substr(Auth::user()->name,0,1)) }}</div>
                    <div x-show="!collapsed" class="flex flex-col">
                        <span class="font-semibold">{{ Auth::user()->name }}</span>
                        <span class="text-xs text-gray-400">{{ Auth::user()->email }}</span>
                    </div>
                </div>
                <div x-show="!collapsed" class="mt-2">
                    <a href="{{ route('profile.edit') }}" class="block px-2 py-1 rounded hover:bg-gray-800 text-sm">Profile</a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-1">
                        @csrf
                        <button type="submit" class="block w-full text-left px-2 py-1 rounded hover:bg-gray-800 text-sm">Log Out</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Responsive Navigation Menu (Mobile) -->
        <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-gray-900">
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('supervisor.cases')" :active="request()->routeIs('supervisor.cases')">
                    {{ __('All Cases') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('supervisor.approvals')" :active="request()->routeIs('supervisor.approvals')">
                    {{ __('Approvals') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('supervisor.closed')" :active="request()->routeIs('supervisor.closed')">
                    {{ __('Closed Cases') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('supervisor.cases.assignment-history')" :active="request()->routeIs('supervisor.cases.assignment-history')">
                    {{ __('Assignment History') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('supervisor.reports')" :active="request()->routeIs('supervisor.reports')">
                    {{ __('Reports') }}
                </x-responsive-nav-link>
            </div>
            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-1 border-t border-gray-800">
                <div class="px-4">
                    <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-400">{{ Auth::user()->email }}</div>
                </div>
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    <main class="flex-1 bg-gray-100">
        {{ $slot ?? '' }}
    </main>
</div>






