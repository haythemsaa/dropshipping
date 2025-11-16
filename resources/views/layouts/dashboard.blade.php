<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard - Dropshipping TN')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md" x-data="{ open: true }">
            <div class="p-6">
                <a href="{{ route('home') }}" class="text-2xl font-bold text-indigo-600">
                    Dropshipping TN
                </a>
            </div>

            <nav class="mt-6">
                @yield('sidebar')
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm">
                <div class="px-8 py-4 flex justify-between items-center">
                    <h1 class="text-2xl font-semibold text-gray-900">@yield('header', 'Dashboard')</h1>

                    <div class="flex items-center space-x-4">
                        <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900">
                            Voir le site
                        </a>

                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-gray-700 hover:text-gray-900">
                                <span class="mr-2">{{ Auth::user()->name }}</span>
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Mon profil
                                </a>
                                <hr class="my-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Déconnexion
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            <div class="px-8 py-4">
                @if(session('success'))
                    <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-4">
                        <p class="text-green-700">{{ session('success') }}</p>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                        <p class="text-red-700">{{ session('error') }}</p>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                        <p class="text-yellow-700">{{ session('warning') }}</p>
                    </div>
                @endif

                @if(session('info'))
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                        <p class="text-blue-700">{{ session('info') }}</p>
                    </div>
                @endif
            </div>

            <!-- Page Content -->
            <main class="flex-1 px-8 py-6">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
</body>
</html>
