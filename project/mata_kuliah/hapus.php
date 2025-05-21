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
$stmt = $conn->prepare("DELETE FROM mata_kuliah WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['success'] = "Mata kuliah berhasil dihapus.";
} else {
    $_SESSION['error'] = "Gagal menghapus mata kuliah.";
}

$stmt->close();
$conn->close();
header("Location: index.php");
exit;
?>