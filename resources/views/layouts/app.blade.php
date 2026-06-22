<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen Invoice</title>
    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Font Awesome CDN untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="flex min-h-screen flex-col bg-blue-50 text-slate-900 antialiased">
    <nav class="sticky top-0 z-40 border-b border-white/10 bg-gradient-to-r from-blue-900 to-blue-950 shadow-lg shadow-blue-950/10">
        <div class="mx-auto flex h-18 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3">
                <div class="leading-tight">
                    <div class="flex flex-col items-center">
                        <div class="font-serif text-lg font-semibold tracking-[0.14em] text-white md:text-xl">AURA GLOBAL</div>
                        <div class="font-serif text-[9px] uppercase tracking-[0.5em] text-blue-200 md:text-[10px]">KOSMETIKA</div>
                    </div>
                </div>
            </div>
            
            <!-- Hanya tampilkan navigasi dan tombol logout jika pengguna sudah login -->
            @auth
                <!-- Tombol Toggle Navigasi untuk Mobile -->
                <div class="md:hidden">
                    <button id="nav-toggle" type="button" aria-controls="nav-menu" aria-expanded="false" class="grid h-10 w-10 place-items-center rounded-xl border border-white/15 text-white transition hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-blue-300">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>

                <!-- Navigasi Utama (Tersembunyi di Mobile secara default) -->
                <div id="nav-menu" class="hidden md:flex md:items-center md:gap-1">
                    <a href="{{ route('admin.index') }}" class="nav-link {{ request()->routeIs('admin.*') ? 'nav-link-active' : '' }}">
                        <i class="fas fa-user-shield mr-2"></i>Admin
                    </a>
                    <a href="{{ route('barang.index') }}" class="nav-link {{ request()->routeIs('barang.*') ? 'nav-link-active' : '' }}">
                        <i class="fas fa-box mr-2"></i>Barang
                    </a>
                    <a href="{{ route('pelanggan.index') }}" class="nav-link {{ request()->routeIs('pelanggan.*') ? 'nav-link-active' : '' }}">
                        <i class="fas fa-users mr-2"></i>Pelanggan
                    </a>
                    <a href="{{ route('invoice.index') }}" class="nav-link {{ request()->routeIs('invoice.*') ? 'nav-link-active' : '' }}">
                        <i class="fas fa-file-invoice-dollar mr-2"></i>Invoice
                    </a>
                    
                    <!-- Tombol Logout -->
                    <form action="{{ route('logout') }}" method="POST" class="inline-block">
                        @csrf
                        <button type="submit" class="ml-0 mt-2 w-full rounded-xl border border-white/15 px-4 py-2.5 text-left text-sm font-semibold text-blue-100 transition hover:bg-white/10 hover:text-white md:ml-2 md:mt-0 md:w-auto">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </form>
                </div>
            @endauth
        </div>
    </nav>

    <main class="flex-grow px-4 py-7 sm:px-6 sm:py-10 lg:px-8">
        @yield('content')
    </main>

    <footer class="bg-gray-800 text-white text-center p-6 mt-auto shadow-inner">
        <p class="text-sm">&copy; {{ date('Y') }} Sistem Manajemen Invoice. Dibuat dengan ❤️ dan Laravel.</p>
    </footer>

    <script>
        // JavaScript untuk toggle navigasi di mobile
        document.addEventListener('DOMContentLoaded', function() {
            const navToggle = document.getElementById('nav-toggle');
            const navMenu = document.getElementById('nav-menu');

            if (navToggle && navMenu) { // Pastikan elemen ada sebelum menambahkan event listener
                navToggle.addEventListener('click', function() {
                    navMenu.classList.toggle('hidden');
                    navMenu.classList.toggle('flex');
                    navMenu.classList.toggle('mobile-nav-open');
                    navToggle.setAttribute('aria-expanded', navToggle.getAttribute('aria-expanded') === 'true' ? 'false' : 'true');
                });
            }
        });
    </script>
</body>
</html>
