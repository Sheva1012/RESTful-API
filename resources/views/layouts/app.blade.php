<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Peminjaman Asset') - PT Akui Bird Nest Indonesia</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col" x-data="globalApp()" x-cloak>

    <!-- Simple Top Navigation Bar -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between h-14">
                <!-- Brand Title (Simple & Clean) -->
                <a href="{{ route('web.dashboard') }}" class="flex flex-col">
                    <span class="font-bold text-gray-900 text-base leading-tight">Sistem Peminjaman Asset</span>
                    <span class="text-xs text-gray-500">PT Akui Bird Nest Indonesia</span>
                </a>

                <!-- Navigation Links -->
                <div class="hidden md:flex space-x-1 text-sm font-medium">
                    <a href="{{ route('web.dashboard') }}" class="px-3 py-1.5 rounded-md transition {{ request()->routeIs('web.dashboard') ? 'bg-gray-100 text-gray-900 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('web.assets') }}" class="px-3 py-1.5 rounded-md transition {{ request()->routeIs('web.assets') ? 'bg-gray-100 text-gray-900 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        Asset
                    </a>
                    <a href="{{ route('web.loans') }}" class="px-3 py-1.5 rounded-md transition {{ request()->routeIs('web.loans') ? 'bg-gray-100 text-gray-900 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        Peminjaman
                    </a>
                    <a href="{{ route('web.borrowers') }}" class="px-3 py-1.5 rounded-md transition {{ request()->routeIs('web.borrowers') ? 'bg-gray-100 text-gray-900 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        Peminjam
                    </a>
                    <a href="{{ route('web.docs') }}" class="px-3 py-1.5 rounded-md transition {{ request()->routeIs('web.docs') ? 'bg-gray-100 text-gray-900 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        API Docs
                    </a>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Links -->
        <div class="md:hidden flex border-t border-gray-100 overflow-x-auto text-xs py-1.5 px-3 space-x-1">
            <a href="{{ route('web.dashboard') }}" class="px-2.5 py-1 rounded {{ request()->routeIs('web.dashboard') ? 'bg-gray-100 font-bold text-gray-900' : 'text-gray-600' }}">Dashboard</a>
            <a href="{{ route('web.assets') }}" class="px-2.5 py-1 rounded {{ request()->routeIs('web.assets') ? 'bg-gray-100 font-bold text-gray-900' : 'text-gray-600' }}">Asset</a>
            <a href="{{ route('web.loans') }}" class="px-2.5 py-1 rounded {{ request()->routeIs('web.loans') ? 'bg-gray-100 font-bold text-gray-900' : 'text-gray-600' }}">Peminjaman</a>
            <a href="{{ route('web.borrowers') }}" class="px-2.5 py-1 rounded {{ request()->routeIs('web.borrowers') ? 'bg-gray-100 font-bold text-gray-900' : 'text-gray-600' }}">Peminjam</a>
            <a href="{{ route('web.docs') }}" class="px-2.5 py-1 rounded {{ request()->routeIs('web.docs') ? 'bg-gray-100 font-bold text-gray-900' : 'text-gray-600' }}">API Docs</a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-6 flex-1 w-full">
        @yield('content')
    </main>

    <!-- Simple Footer -->
    <footer class="bg-white border-t border-gray-200 py-4 text-center text-xs text-gray-500 mt-auto">
        <div class="max-w-6xl mx-auto px-4">
            <p>Sistem Peminjaman Asset - PT Akui Bird Nest Indonesia</p>
        </div>
    </footer>

    <!-- Global Scripts -->
    <script>
        function globalApp() {
            return {};
        }
    </script>
    @stack('scripts')
</body>
</html>
