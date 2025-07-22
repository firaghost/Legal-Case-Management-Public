<header class="relative z-50 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 shadow-lg backdrop-blur-sm bg-white/95 dark:bg-gray-900/95">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Mobile menu button -->
            <button class="md:hidden p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200" @click.stop="sidebarOpen = !sidebarOpen; userMenuOpen = false">
                <svg data-lucide="menu" class="w-6 h-6"></svg>
            </button>

            <!-- Enhanced Logo and Title with Legal Organization Branding -->
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-4 group transition-all duration-300 hover:scale-[1.02] focus:outline-none">
                    <div class="relative">
                        <div class="absolute inset-0 rounded-2xl blur opacity-75 group-hover:opacity-100 transition-opacity duration-300" style="background: linear-gradient(135deg, #3ca44c 0%, #1e3a2e 100%);"></div>
                        <div>
                            <x-application-logo class="h-10 w-auto text-white drop-shadow-lg" />
                        </div>
                    </div>
                    <div class="hidden sm:block">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 group-hover:text-green-600 dark:group-hover:text-green-400 transition-all duration-300 tracking-tight">
                            Legal Organization
                        </h1>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium tracking-wide">Legal Case Management System</p>
                    </div>
                </a>
            </div>

            <!-- Right Side Actions -->
            <div class="flex items-center gap-3">
                <!-- Theme Toggle Switch -->
                <link rel="stylesheet" href="{{ asset('css/theme-toggle.css') }}">
                <script src="{{ asset('js/theme-toggle.js') }}" defer></script>
                <div class="switch">
                    <input type="checkbox" id="theme-toggle" name="toggle">
                    <label for="theme-toggle">
                        <i class="bulb">
                            <span class="bulb-center"></span>
                            <span class="filament-1"></span>
                            <span class="filament-2"></span>
                            <span class="reflections">
                                <span></span>
                            </span>
                            <span class="sparks">
                                <i class="spark1"></i>
                                <i class="spark2"></i>
                                <i class="spark3"></i>
                                <i class="spark4"></i>
                            </span>
                        </i>
                    </label>
                </div>
                @auth
                    @if(auth()->check())
                        <!-- Notifications -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="relative p-2 rounded-xl text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                                <svg data-lucide="bell" class="w-5 h-5"></svg>
                                @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
                                @if($unread > 0)
                                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold shadow-lg animate-pulse">
                                        {{ $unread > 9 ? '9+' : $unread }}
                                    </span>
                                @endif
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800/90 dark:backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-600/30 overflow-hidden z-[9999]">
                                <div class="p-4 border-b border-white/20 dark:border-gray-600/30" style="background: linear-gradient(135deg, rgba(60, 164, 76, 0.1) 0%, rgba(30, 58, 46, 0.1) 100%);">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Notifications</h3>
                                        @if($unread > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                {{ $unread }} new
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="max-h-80 overflow-y-auto">
                                    @forelse(auth()->user()->unreadNotifications as $notification)
                                        @php
                                            // Check if this is a reassignment notification for old lawyer
                                            $isReassignmentForOldLawyer = isset($notification->data['type']) && 
                                                                        $notification->data['type'] === 'case_reassigned' &&
                                                                        auth()->id() !== ($notification->data['new_lawyer_id'] ?? null);
                                            $clickableClass = $isReassignmentForOldLawyer ? 
                                                'block px-4 py-3 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200 border-b border-gray-100 dark:border-gray-700 last:border-0 opacity-75' :
                                                'block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 border-b border-gray-100 dark:border-gray-700 last:border-0';
                                        @endphp
                                        
                                        <a href="{{ route('notifications.open', $notification->id) }}" class="{{ $clickableClass }}">
                                            <div class="flex items-start gap-3">
                                                <div class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between">
                                                        <p class="font-medium text-gray-900 dark:text-gray-100 truncate">
                                                            Case #{{ $notification->data['case_file_number'] ?? $notification->data['file_number'] ?? '' }}
                                                        </p>
                                                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">
                                                            {{ $notification->created_at->diffForHumans() }}
                                                        </span>
                                                    </div>
                                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                                        @if(isset($notification->data['type']) && $notification->data['type'] === 'case_reassigned')
                                                            {{ $notification->data['message'] ?? 'Case reassignment notification' }}
                                                        @elseif(isset($notification->data['approval_type']))
                                                            {{ $notification->data['message'] ?? ($notification->data['title'] ?? 'Case approved by supervisor') }}
                                                        @else
                                                            {{ $notification->data['message'] ?? $notification->data['title'] ?? 'Notification' }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="px-4 py-8 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 7H4l5-5v5z"></path>
                                            </svg>
                                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No new notifications</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endif
                @endauth
                <!-- User Menu -->
                <div x-data="{ userMenuOpen: false }" class="relative">
                    <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2 p-2 rounded-xl text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                        @if(Auth::user()->profile_picture)
                            <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile" class="w-8 h-8 rounded-full object-cover border-2 border-blue-200 dark:border-blue-800">
                        @else
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-sm font-bold">
                                {{ strtoupper(substr(Auth::user()->name,0,1)) }}
                            </div>
                        @endif
                        <span class="hidden sm:block text-sm font-medium">{{ Auth::user()->name }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800/90 dark:backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-600/30 overflow-hidden z-[9999]">
                        <div class="p-4 border-b border-white/20 dark:border-gray-600/30" style="background: linear-gradient(135deg, rgba(60, 164, 76, 0.1) 0%, rgba(30, 58, 46, 0.1) 100%);">
                            <div class="flex items-center gap-3">
                                @if(Auth::user()->profile_picture)
                                    <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile" class="w-12 h-12 rounded-full object-cover border-3 border-blue-200 dark:border-blue-800">
                                @else
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-lg font-bold">
                                        {{ strtoupper(substr(Auth::user()->name,0,1)) }}
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-900 dark:text-gray-100 truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 truncate">{{ Auth::user()->email }}</p>
                                    <div class="flex items-center gap-1 mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            <span class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></span>
                                            {{ ucfirst(Auth::user()->role ?? 'User') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-2">
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Edit Profile
                            </a>
                            <div class="border-t border-gray-100 dark:border-gray-700 my-2"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-colors duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>






