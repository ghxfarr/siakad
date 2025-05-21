<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Akademik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/styles.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#1e40af',
                        secondary: '#4b5563',
                    },
                },
            },
        };
    </script>
</head>

<body class="bg-gray-100">
    <!-- Sidebar -->
    <div class="flex min-h-screen">
        <aside id="sidebar"
            class="w-64 bg-white shadow-lg h-full fixed top-0 left-[-256px] transition-all duration-300 z-30">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-primary">SIAKAD</h2>
                <p class="text-sm text-gray-600 mt-1">Sistem Informasi Akademik</p>
            </div>
            <nav class="mt-6">
                <ul class="space-y-2 px-4">
                    <li>
                        <a href="mahasiswa/index.php"
                            class="flex items-center p-3 rounded-lg hover:bg-gray-100 transition">
                            <span class="mr-3">👤</span> Mahasiswa
                        </a>
                    </li>
                    <li>
                        <a href="mata_kuliah/index.php"
                            class="flex items-center p-3 rounded-lg hover:bg-gray-100 transition">
                            <span class="mr-3">📘</span> Mata Kuliah
                        </a>
                    </li>
                    <li>
                        <a href="nilai/index.php" class="flex items-center p-3 rounded-lg hover:bg-gray-100 transition">
                            <span class="mr-3">📝</span> Input Nilai
                        </a>
                    </li>
                    <li>
                        <a href="laporan/rata_mahasiswa.php"
                            class="flex items-center p-3 rounded-lg hover:bg-gray-100 transition">
                            <span class="mr-3">📈</span> Rata-rata Mahasiswa
                        </a>
                    </li>
                    <li>
                        <a href="laporan/rekap_mk.php"
                            class="flex items-center p-3 rounded-lg hover:bg-gray-100 transition">
                            <span class="mr-3">📄</span> Rekap Nilai per Mata Kuliah
                        </a>
                    </li>
                    <li>
                        <a href="auth/logout.php"
                            class="flex items-center p-3 rounded-lg hover:bg-red-50 text-red-600 transition">
                            <span class="mr-3">🚪</span> Logout
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Mobile Navbar -->
        <nav class="md:hidden bg-white shadow fixed w-full z-40 top-0">
            <div class="flex justify-between items-center p-4">
                <h2 class="text-xl font-bold text-primary">SIAKAD</h2>
                <button id="menu-toggle" class="text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                </button>
            </div>
            <div id="mobile-menu" class="hidden bg-white">
                <ul class="space-y-2 p-4">
                    <li><a href="mahasiswa/index.php" class="block p-2 hover:bg-gray-100 rounded">👤 Mahasiswa</a></li>
                    <li><a href="mata_kuliah/index.php" class="block p-2 hover:bg-gray-100 rounded">📘 Mata Kuliah</a>
                    </li>
                    <li><a href="nilai/index.php" class="block p-2 hover:bg-gray-100 rounded">📝 Input Nilai</a></li>
                    <li><a href="laporan/rata_mahasiswa.php" class="block p-2 hover:bg-gray-100 rounded">📈 Rata-rata
                            Mahasiswa</a></li>
                    <li><a href="laporan/rekap_mk.php" class="block p-2 hover:bg-gray-100 rounded">📄 Rekap Nilai per
                            Mata Kuliah</a></li>
                    <li><a href="auth/logout.php" class="block p-2 hover:bg-red-50 text-red-600 rounded">🚪 Logout</a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-1 p-6 mt-16 md:mt-0">
            <div class="max-w-6xl mx-auto">
                <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Selamat Datang di SIAKAD</h1>
                    <p class="text-gray-600 mt-2">Kelola data mahasiswa, mata kuliah, nilai, dan laporan akademik dengan
                        mudah dan efisien.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Data Master</h3>
                        <ul class="space-y-2">
                            <li><a href="mahasiswa/index.php" class="flex items-center p-2 hover:bg-gray-100 rounded">👤
                                    Mahasiswa</a></li>
                            <li><a href="mata_kuliah/index.php"
                                    class="flex items-center p-2 hover:bg-gray-100 rounded">📘 Mata Kuliah</a></li>
                            <li><a href="nilai/index.php" class="flex items-center p-2 hover:bg-gray-100 rounded">📝
                                    Input Nilai</a></li>
                        </ul>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Laporan Akademik</h3>
                        <ul class="space-y-2">
                            <li><a href="laporan/rata_mahasiswa.php"
                                    class="flex items-center p-2 hover:bg-gray-100 rounded">📈 Rata-rata Mahasiswa</a>
                            </li>
                            <li><a href="laporan/rekap_mk.php"
                                    class="flex items-center p-2 hover:bg-gray-100 rounded">📄 Rekap Nilai per Mata Kuliah</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <footer class="text-center text-gray-500 mt-8">
                    <p>© <?= date('Y') ?> Teknik Informatika | President University</p>
                </footer>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mobile menu toggle
        const menuToggle = document.getElementById('menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Sidebar hover animation for desktop
        const sidebar = document.getElementById('sidebar');
        document.addEventListener('mousemove', (e) => {
            if (window.innerWidth >= 768) { // Only on desktop
                if (e.clientX <= 100) { // Show sidebar when cursor is within 100px from left
                    sidebar.style.left = '0';
                } else if (e.clientX > 300) { // Hide sidebar when cursor is beyond 300px
                    sidebar.style.left = '-256px';
                }
            }
        });

        // Ensure sidebar is hidden on page load for desktop
        window.addEventListener('load', () => {
            if (window.innerWidth >= 768) {
                sidebar.style.left = '-256px';
            }
        });

        // Adjust sidebar visibility on window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth < 768) {
                sidebar.style.left = '-256px'; // Ensure sidebar is hidden on mobile
            } else {
                sidebar.style.left = '-256px'; // Reset to hidden on desktop
            }
        });
    </script>
</body>

</html>