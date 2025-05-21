<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db.php';

$mahasiswa = $conn->query("SELECT * FROM mahasiswa ORDER BY nama");
$mk = $conn->query("SELECT * FROM mata_kuliah ORDER BY nama_mk");

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_mahasiswa = trim($_POST['id_mahasiswa'] ?? '');
    $id_mk = trim($_POST['id_mk'] ?? '');
    $nilai = trim($_POST['nilai'] ?? '');

    // Validasi
    if (empty($id_mahasiswa) || !is_numeric($id_mahasiswa))
        $errors[] = "Mahasiswa wajib dipilih.";
    if (empty($id_mk) || !is_numeric($id_mk))
        $errors[] = "Mata kuliah wajib dipilih.";
    if (empty($nilai) || !is_numeric($nilai) || $nilai < 0 || $nilai > 100) {
        $errors[] = "Nilai harus antara 0 dan 100.";
    }

    // Cek duplikasi nilai untuk mahasiswa dan mata kuliah
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM nilai WHERE id_mahasiswa = ? AND id_mk = ?");
        $stmt->bind_param("ii", $id_mahasiswa, $id_mk);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $errors[] = "Nilai untuk mahasiswa dan mata kuliah ini sudah ada.";
        }
        $stmt->close();
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO nilai (id_mahasiswa, id_mk, nilai) VALUES (?, ?, ?)");
        $stmt->bind_param("iid", $id_mahasiswa, $id_mk, $nilai);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Nilai berhasil ditambahkan.";
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Gagal menambahkan nilai: " . $conn->error;
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
    <title>Tambah Nilai - SIAKAD</title>
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
                    <li><a href="index.php"
                            class="flex items-center p-3 rounded-lg bg-gray-100 text-primary font-semibold"><span
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
                    <li><a href="../mata_kuliah/index.php" class="block p-2 hover:bg-gray-100 rounded">📘 Mata
                            Kuliah</a></li>
                    <li><a href="index.php" class="block p-2 bg-gray-100 text-primary font-semibold rounded">📝 Input
                            Nilai</a></li>
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
                    <h1 class="text-2xl font-bold text-gray-900">Tambah Nilai</h1>
                    <p class="text-gray-600 mt-2">Masukkan nilai mahasiswa untuk mata kuliah tertentu.</p>
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
                            <label for="id_mahasiswa" class="block text-sm font-medium text-gray-700">Mahasiswa <span
                                    class="text-red-500">*</span></label>
                            <select id="id_mahasiswa" name="id_mahasiswa" required
                                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20">
                                <option value="">-- Pilih Mahasiswa --</option>
                                <?php while ($m = $mahasiswa->fetch_assoc()): ?>
                                    <option value="<?= $m['id'] ?>" <?= (isset($_POST['id_mahasiswa']) && $_POST['id_mahasiswa'] == $m['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($m['nama']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div>
                            <label for="id_mk" class="block text-sm font-medium text-gray-700">Mata Kuliah <span
                                    class="text-red-500">*</span></label>
                            <select id="id_mk" name="id_mk" required
                                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20">
                                <option value="">-- Pilih Mata Kuliah --</option>
                                <?php while ($k = $mk->fetch_assoc()): ?>
                                    <option value="<?= $k['id'] ?>" <?= (isset($_POST['id_mk']) && $_POST['id_mk'] == $k['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($k['nama_mk']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div>
                            <label for="nilai" class="block text-sm font-medium text-gray-700">Nilai <span
                                    class="text-red-500">*</span></label>
                            <input type="number" id="nilai" name="nilai" step="0.01" min="0" max="100"
                                value="<?= htmlspecialchars($_POST['nilai'] ?? '') ?>" required
                                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20">
                        </div>
                        <div class="flex gap-4">
                            <button type="submit"
                                class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">Simpan</button>
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