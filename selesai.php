<?php
// File: selesai.php
// Fungsi: menandai satu kegiatan menjadi selesai (selesai = 1).

require "auth.php";
require "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: todo.php");
    exit;
}

cek_csrf();

// Memastikan id berupa bilangan bulat; jika bukan, hasilnya false/null
$id = filter_var($_POST['id'] ?? "", FILTER_VALIDATE_INT);

if ($id !== false && $id !== null) {
    $userId = $_SESSION['user_id'];

    // "AND user_id = ?" memastikan hanya kegiatan milik sendiri yang bisa diubah
    $stmt = mysqli_prepare($conn, "UPDATE todos SET selesai = 1 WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $id, $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

header("Location: todo.php");
exit;