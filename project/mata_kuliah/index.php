<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db.php';

$cari = isset($_GET['cari']) ? trim($_GET['cari']) : '';
$query = "SELECT * FROM mata_kuliah";
if (!empty($cari)) {
    $cari = $conn->real_escape_string($cari);
    $query .= " WHERE kode_mk LIKE '%$cari%' OR nama_mk LIKE '%$cari%'";
}
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mata Kuliah - SIAKAD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/styles.css">
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
                    <li><a href="../index.php"
                            class="flex items-center p-3 rounded-lg hover:bg-gray-100 transition"><span
                                class="mr-3">🏠</span> Dashboard</a></li>
                    <li><a href="../mahasiswa/index.php"
                            class="flex items-center p-3 rounded-lg hover:bg-gray-100 transition"><span
                                class="mr-3">👤</span> Mahasiswa</a></li>
                    <li><a href="index.php"
                            class="flex items-center p-3 rounded-lg bg-gray-100 text-primary font-semibold"><span
                                class="mr-3">📘</span> Mata Kuliah</a></li>
                    <li><a href="../nilai/index.php"
                            class="flex items-center p-3 rounded-lg hover:bg-gray-100 transition"><span
                                class="mr-3">📝</span> Input Nilai</a></li>
                    <li><a href="../laporan/rata_mahasiswa.php"
                            class="flex items-center p-3 rounded-lg hover:bg-gray-100 transition"><span
                                class="mr-3">📈</span> Rata-rata Mahasiswa</a></li>
                    <li><a href="../laporan/rekap_mk.php"
                            class="flex items-center p-3 rounded-lg hover:bg-gray-100 transition"><span
                                class="mr-3">📄</span> Rekap Nilai per Mata Kuliah</a></li>
                    <li><a href="../auth/logout.php"
                            class="flex items-center p-3 rounded-lg hover:bg-red-50 text-red-600 transition"><span
                                class="mr-3">🚪</span> Logout</a></li>
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
                    <li><a href="../index.php" class="block p-2 hover:bg-gray-100 rounded">🏠 Dashboard</a></li>
                    <li><a href="../mahasiswa/index.php" class="block p-2 hover:bg-gray-100 rounded">👤 Mahasiswa</a>
                    </li>
                    <li><a href="index.php" class="block p-2 bg-gray-100 text-primary font-semibold rounded">📘 Mata
                            Kuliah</a></li>
                    <li><a href="../nilai/index.php" class="block p-2 hover:bg-gray-100 rounded">📝 Input Nilai</a></li>
                    <li><a href="../laporan/rata_mahasiswa.php" class="block p-2 hover:bg-gray-100 rounded">📈 Rata-rata
                            Mahasiswa</a></li>
                    <li><a href="../laporan/rekap_mk.php" class="block p-2 hover:bg-gray-100 rounded">📄 Rekap Nilai per
                            Mata Kuliah</a></li>
                    <li><a href="../auth/logout.php" class="block p-2 hover:bg-red-50 text-red-600 rounded">🚪
                            Logout</a></li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-1 p-6 mt-16 md:mt-0">
            <div class="max-w-6xl mx-auto">
                <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
                    <h1 class="text-2xl font-bold text-gray-900">Daftar Mata Kuliah</h1>
                    <p class="text-gray-600 mt-2">Kelola data mata kuliah dengan mudah.</p>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6">
                    <!-- Notifications -->
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                            <?= htmlspecialchars($_SESSION['success']) ?>
                        </div>
                        <?php unset($_SESSION['success']); endif; ?>
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                            <?= htmlspecialchars($_SESSION['error']) ?>
                        </div>
                        <?php unset($_SESSION['error']); endif; ?>

                    <!-- Search and Actions -->
                    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                        <form method="GET" class="w-full sm:w-auto">
                            <div class="flex items-center gap-2">
                                <input type="text" name="cari" placeholder="Cari kode atau nama MK..."
                                    value="<?= htmlspecialchars($cari) ?>"
                                    class="form-control w-full sm:w-64 rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20">
                                <button type="submit"
                                    class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">Cari</button>
                                <a href="index.php" class="text-primary hover:text-blue-700">Reset</a>
                            </div>
                        </form>
                        <a href="tambah.php"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Mata Kuliah
                        </a>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">No</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Kode MK</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Mata Kuliah</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">SKS</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php $no = 1;
                                while ($row = $result->fetch_assoc()): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-600"><?= $no++ ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-800"><?= htmlspecialchars($row['kode_mk']) ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-800"><?= htmlspecialchars($row['nama_mk']) ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-800"><?= htmlspecialchars($row['sks']) ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <a href="edit.php?id=<?= $row['id'] ?>"
                                                class="text-blue-600 hover:text-blue-800">Edit</a>
                                            <span class="text-gray-300 mx-2">|</span>
                                            <a href="hapus.php?id=<?= $row['id'] ?>"
                                                class="text-red-600 hover:text-red-800 delete-btn"
                                                data-id="<?= $row['id'] ?>">Hapus</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                                <?php if ($result->num_rows === 0): ?>
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data
                                            mata kuliah.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
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
            if (window.innerWidth >= 768) {
                if (e.clientX <= 100) {
                    sidebar.style.left = '0';
                } else if (e.clientX > 300) {
                    sidebar.style.left = '-256px';
                }
            }
        });

        // Ensure sidebar is hidden on page load and resize
        window.addEventListener('load', () => {
            if (window.innerWidth >= 768) sidebar.style.left = '-256px';
        });
        window.addEventListener('resize', () => {
            if (window.innerWidth < 768) sidebar.style.left = '-256px';
            else sidebar.style.left = '-256px';
        });

        // Custom delete confirmation
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const url = button.getAttribute('href');
                if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                    window.location.href = url;
                }
            });
        });
    </script>
</body>

</html>
<?php $conn->close(); ?>