<x-app-layout>
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Modern Welcome Header -->
            <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-800 dark:to-indigo-800 rounded-3xl shadow-2xl mb-8">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.1"%3E%3Ccircle cx="30" cy="30" r="4"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>
                <div class="relative px-8 py-12">
                    <div class="flex items-center gap-6">
                        <div class="flex-shrink-0">
                            <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                                <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2L2 7v10c0 5.55 3.84 10 9 11 1.09-.21 2.11-.56 3-1.03V7l-2-1.5V17c0 .55-.45 1-1 1s-1-.45-1-1V6l6-4.5L22 7v10c0 5.55-3.84 10-9 11-5.16-1-9-5.45-9-11V7l10-5z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h1 class="text-4xl font-bold text-white mb-2">
                                Welcome back, <span class="text-yellow-200">{{ Auth::user()->name }}</span>!
                            </h1>
                            <p class="text-xl text-white/90 font-medium">Welcome To Legal Case System.</p>
                            <div class="flex items-center gap-2 mt-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/20 text-white">
                                    <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                                    {{ ucfirst(Auth::user()->role ?? 'default') }}
                                </span>
                                <span class="text-white/80 text-sm">{{ now()->format('l, F j, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
</x-app-layout>






