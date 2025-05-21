<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db.php';

$cari = isset($_GET['cari']) ? trim($_GET['cari']) : '';
$query = "SELECT m.nama, m.npm, AVG(n.nilai) AS rata_rata
          FROM nilai n
          JOIN mahasiswa m ON n.id_mahasiswa = m.id
          GROUP BY n.id_mahasiswa";
if (!empty($cari)) {
    $cari = $conn->real_escape_string($cari);
    $query .= " HAVING m.nama LIKE '%$cari%' OR m.npm LIKE '%$cari%'";
}
$result = $conn->query($query);

// Handle CSV export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="rata_rata_mahasiswa.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['No', 'Nama Mahasiswa', 'NPM', 'Rata-rata Nilai']);
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $no++,
            $row['nama'],
            $row['npm'],
            number_format($row['rata_rata'], 2)
        ]);
    }
    fclose($output);
    $conn->close();
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rata-rata Nilai Mahasiswa - SIAKAD</title>
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
                    <li><a href="../mata_kuliah/index.php"
                            class="flex items-center p-3 rounded-lg hover:bg-gray-100 transition"><span
                                class="mr-3">📘</span> Mata Kuliah</a></li>
                    <li><a href="../nilai/index.php"
                            class="flex items-center p-3 rounded-lg hover:bg-gray-100 transition"><span
                                class="mr-3">📝</span> Input Nilai</a></li>
                    <li><a href="rata_mahasiswa.php"
                            class="flex items-center p-3 rounded-lg bg-gray-100 text-primary font-semibold"><span
                                class="mr-3">📈</span> Rata-rata Mahasiswa</a></li>
                    <li><a href="rekap_mk.php"
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
                    <li><a href="../mata_kuliah/index.php" class="block p-2 hover:bg-gray-100 rounded">📘 Mata
                            Kuliah</a></li>
                    <li><a href="../nilai/index.php" class="block p-2 hover:bg-gray-100 rounded">📝 Input Nilai</a></li>
                    <li><a href="rata_mahasiswa.php" class="block p-2 bg-gray-100 text-primary font-semibold rounded">📈
                            Rata-rata Mahasiswa</a></li>
                    <li><a href="rekap_mk.php" class="block p-2 hover:bg-gray-100 rounded">📄 Rekap Nilai per Mata Kuliah</a>
                    </li>
                    <li><a href="../auth/logout.php" class="block p-2 hover:bg-red-50 text-red-600 rounded">🚪
                            Logout</a></li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-1 p-6 mt-16 md:mt-0">
            <div class="max-w-6xl mx-auto">
                <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
                    <h1 class="text-2xl font-bold text-gray-900">Laporan Rata-rata Nilai Mahasiswa</h1>
                    <p class="text-gray-600 mt-2">Lihat rata-rata nilai semua mahasiswa.</p>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6">
                    <!-- Search and Export -->
                    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                        <form method="GET" class="w-full sm:w-auto">
                            <div class="flex items-center gap-2">
                                <input type="text" name="cari" placeholder="Cari nama atau NPM..."
                                    value="<?= htmlspecialchars($cari) ?>"
                                    class="form-control w-full sm:w-64 rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20">
                                <button type="submit"
                                    class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">Cari</button>
                                <a href="rata_mahasiswa.php" class="text-primary hover:text-blue-700">Reset</a>
                            </div>
                        </form>
                        <a href="?export=csv"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Ekspor ke CSV
                        </a>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">No</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Nama Mahasiswa
                                    </th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">NPM</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Rata-rata Nilai
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php $no = 1;
                                while ($row = $result->fetch_assoc()): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-600"><?= $no++ ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-800"><?= htmlspecialchars($row['nama']) ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-800"><?= htmlspecialchars($row['npm']) ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-800">
                                            <?= number_format($row['rata_rata'], 2) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                                <?php if ($result->num_rows === 0): ?>
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data
                                            rata-rata nilai.</td>
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
    </script>
</body>

</html>
<?php $conn->close(); ?>