<?php
// File: hapus.php
// Fungsi: menghapus satu kegiatan dari database.

require "auth.php";
require "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: todo.php");
    exit;
}

cek_csrf();

$id = filter_var($_POST['id'] ?? "", FILTER_VALIDATE_INT);

if ($id !== false && $id !== null) {
    $userId = $_SESSION['user_id'];

    // WHERE id = ? hanya menghapus SATU baris; user_id mencegah menghapus milik orang lain
    $stmt = mysqli_prepare($conn, "DELETE FROM todos WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $id, $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

header("Location: todo.php");
exit;