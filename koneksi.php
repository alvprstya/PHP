<?php
// File: koneksi.php
// Fungsi: menghubungkan PHP dengan database MySQL.
// File ini akan dipanggil (require) oleh file PHP lain yang butuh database.

// Data untuk masuk ke MySQL (default XAMPP: user root, tanpa password)
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_belajarweb";

try {
    // Membuat koneksi ke database
    $conn = mysqli_connect($host, $user, $pass, $db);

    // Mengatur karakter agar sesuai dengan database (utf8mb4)
    mysqli_set_charset($conn, "utf8mb4");
} catch (mysqli_sql_exception $e) {
    // Jika gagal, hentikan program dan tampilkan pesan singkat
    die("Koneksi database gagal. Pastikan MySQL di XAMPP sudah berjalan.");
}