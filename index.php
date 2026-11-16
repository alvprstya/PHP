<?php
// File: index.php
// Fungsi: pintu masuk http://localhost/belajarweb/
// Mengarahkan pengguna ke halaman yang sesuai dengan status loginnya.

session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: todo.php");   // sudah login
} else {
    header("Location: login.php");  // belum login
}
exit;