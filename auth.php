<?php
// File: auth.php
// Fungsi: "satpam" untuk halaman yang hanya boleh diakses setelah login,
// sekaligus penyedia token CSRF untuk form.

// Memulai session agar PHP bisa membaca status login pengguna
session_start();

// Jika tidak ada user_id di session, berarti belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Membuat token CSRF (kode acak) jika belum ada di session
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// Fungsi untuk memeriksa token yang dikirim form.
// Dipanggil di tambah.php, selesai.php, dan hapus.php.
function cek_csrf(): void
{
    $token = $_POST['csrf'] ?? '';

    // hash_equals() membandingkan dua teks dengan cara yang aman
    if (!is_string($token) || !hash_equals($_SESSION['csrf'], $token)) {
        http_response_code(403);
        die("Permintaan tidak valid.");
    }
}