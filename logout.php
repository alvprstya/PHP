<?php
// File: logout.php
// Fungsi: mengakhiri sesi login pengguna.

// Session harus dimulai dulu agar bisa dihapus
session_start();

// Mengosongkan semua data session
$_SESSION = [];

// Menghapus cookie session di browser
if (ini_get("session.use_cookies")) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p["path"], $p["domain"], $p["secure"], $p["httponly"]);
}

// Menghancurkan session di server
session_destroy();

// Kembali ke halaman login
header("Location: login.php");
exit;