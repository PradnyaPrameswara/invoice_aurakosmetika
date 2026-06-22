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
<body class="flex flex-col min-h-screen bg-blue-50 text-gray-900">
    <nav class="bg-gradient-to-r from-blue-800 to-blue-950 p-4 shadow-xl">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="leading-tight">
                    <div class="flex flex-col items-center">
                        <div class="text-white text-xl md:text-2xl font-semibold tracking-wide font-serif">AURA GLOBAL</div>
                        <div class="text-blue-100 text-[10px] md:text-xs tracking-[0.45em] uppercase font-serif">KOSMETIKA</div>
                    </div>
                </div>
            </div>
            
            <!-- Hanya tampilkan navigasi dan tombol logout jika pengguna sudah login -->
            @auth
                <!-- Tombol Toggle Navigasi untuk Mobile -->
                <div class="md:hidden">
                    <button id="nav-toggle" class="text-white focus:outline-none">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>

                <!-- Navigasi Utama (Tersembunyi di Mobile secara default) -->
                <div id="nav-menu" class="hidden md:flex flex-col md:flex-row md:items-center space-y-2 md:space-y-0 md:space-x-6 mt-4 md:mt-0">
                    <a href="{{ route('admin.index') }}" class="text-blue-100 hover:text-white px-3 py-2 rounded-md text-lg font-medium hover:bg-blue-800 transition duration-300 ease-in-out">
                        <i class="fas fa-user-shield mr-2"></i>Admin
                    </a>
                    <a href="{{ route('barang.index') }}" class="text-blue-100 hover:text-white px-3 py-2 rounded-md text-lg font-medium hover:bg-blue-800 transition duration-300 ease-in-out">
                        <i class="fas fa-box mr-2"></i>Barang
                    </a>
                    <a href="{{ route('pelanggan.index') }}" class="text-blue-100 hover:text-white px-3 py-2 rounded-md text-lg font-medium hover:bg-blue-800 transition duration-300 ease-in-out">
                        <i class="fas fa-users mr-2"></i>Pelanggan
                    </a>
                    <a href="{{ route('invoice.index') }}" class="text-blue-100 hover:text-white px-3 py-2 rounded-md text-lg font-medium hover:bg-blue-800 transition duration-300 ease-in-out">
                        <i class="fas fa-file-invoice-dollar mr-2"></i>Invoice
                    </a>
                    
                    <!-- Tombol Logout -->
                    <form action="{{ route('logout') }}" method="POST" class="inline-block">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </form>
                </div>
            @endauth
        </div>
    </nav>

    <main class="flex-grow py-8 px-4">
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
                    navMenu.classList.toggle('absolute');
                    navMenu.classList.toggle('top-16'); /* Sesuaikan dengan tinggi navbar */
                    navMenu.classList.toggle('left-0');
                    navMenu.classList.toggle('w-full');
                    navMenu.classList.toggle('bg-blue-800');
                    navMenu.classList.toggle('p-4');
                    navMenu.classList.toggle('shadow-lg');
                    navMenu.classList.toggle('z-50'); /* Pastikan di atas konten lain */
                });
            }
        });
    </script>
</body>
</html>
