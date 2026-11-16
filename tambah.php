<?php
// File: tambah.php
// Fungsi: menyimpan kegiatan baru ke database, lalu kembali ke todo.php.

require "auth.php";
require "koneksi.php";

// Hanya menerima kiriman dari form (POST); membuka lewat URL langsung ditolak
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: todo.php");
    exit;
}

cek_csrf(); // memastikan form berasal dari halaman kita

// Mengambil teks kegiatan dan membuang spasi di awal/akhir
$kegiatan = trim($_POST['kegiatan'] ?? "");

// Validasi: tidak boleh kosong dan maksimal 255 karakter
if ($kegiatan === "") {
    $_SESSION['flash_error'] = "Kegiatan tidak boleh kosong.";
} elseif (mb_strlen($kegiatan, 'UTF-8') > 255) {
    $_SESSION['flash_error'] = "Kegiatan maksimal 255 karakter.";
} else {
    // Menyimpan ke database; user_id diambil dari session, bukan dari form
    $userId = $_SESSION['user_id'];
    $stmt = mysqli_prepare($conn, "INSERT INTO todos (user_id, kegiatan) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "is", $userId, $kegiatan);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// Kembali ke halaman utama. Dengan redirect, refresh tidak menambah data dobel.
header("Location: todo.php");
exit;