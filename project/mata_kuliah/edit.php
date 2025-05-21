<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID mata kuliah tidak valid.";
    header("Location: index.php");
    exit;
}

$id = (int) $_GET['id'];
$result = $conn->query("SELECT * FROM mata_kuliah WHERE id = $id");
if ($result->num_rows === 0) {
    $_SESSION['error'] = "Data mata kuliah tidak ditemukan.";
    header("Location: index.php");
    exit;
}
$data = $result->fetch_assoc();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_mk = trim($_POST['kode_mk'] ?? '');
    $nama_mk = trim($_POST['nama_mk'] ?? '');
    $sks = trim($_POST['sks'] ?? '');

    // Validasi
    if (empty($kode_mk))
        $errors[] = "Kode MK wajib diisi.";
    if (empty($nama_mk))
        $errors[] = "Nama mata kuliah wajib diisi.";
    if (empty($sks) || !is_numeric($sks) || $sks < 1 || $sks > 10) {
        $errors[] = "SKS harus berupa angka antara 1 dan 10.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE mata_kuliah SET kode_mk = ?, nama_mk = ?, sks = ? WHERE id = ?");
        $stmt->bind_param("ssii", $kode_mk, $nama_mk, $sks, $id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Mata kuliah berhasil diperbarui.";
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Gagal memperbarui data: " . $conn->error;
        }
        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mata Kuliah - SIAKAD</title>
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
                    <h1 class="text-2xl font-bold text-gray-900">Edit Mata Kuliah</h1>
                    <p class="text-gray-600 mt-2">Perbarui data mata kuliah.</p>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6">
                    <?php if (!empty($errors)): ?>
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                            <ul class="list-disc pl-5">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="space-y-6">
                        <div>
                            <label for="kode_mk" class="block text-sm font-medium text-gray-700">Kode MK <span
                                    class="text-red-500">*</span></label>
                            <input type="text" id="kode_mk" name="kode_mk"
                                value="<?= htmlspecialchars($data['kode_mk']) ?>" required
                                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20">
                        </div>
                        <div>
                            <label for="nama_mk" class="block text-sm font-medium text-gray-700">Nama Mata Kuliah <span
                                    class="text-red-500">*</span></label>
                            <input type="text" id="nama_mk" name="nama_mk"
                                value="<?= htmlspecialchars($data['nama_mk']) ?>" required
                                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20">
                        </div>
                        <div>
                            <label for="sks" class="block text-sm font-medium text-gray-700">SKS <span
                                    class="text-red-500">*</span></label>
                            <input type="number" id="sks" name="sks" value="<?= htmlspecialchars($data['sks']) ?>"
                                required min="1" max="10"
                                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20">
                        </div>
                        <div class="flex gap-4">
                            <button type="submit"
                                class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">Update</button>
                            <a href="index.php"
                                class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">Batal</a>
                        </div>
                    </form>
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