<?php
// File: login.php
// Fungsi: menampilkan form login dan memeriksa akun dari database.

// Memulai session untuk menyimpan status login
session_start();

// Jika sudah login, tidak perlu melihat form login lagi
if (isset($_SESSION['user_id'])) {
    header("Location: todo.php");
    exit;
}

// Menghubungkan ke database (menghasilkan variabel $conn)
require "koneksi.php";

$error    = "";   // tempat menyimpan pesan kesalahan
$username = "";   // agar username tetap terisi jika login gagal

// Bagian ini hanya berjalan ketika form dikirim (tombol Submit ditekan)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Mengambil input. trim() membuang spasi di awal/akhir.
    // "?? ''" mencegah error jika datanya tidak ada.
    $username = trim($_POST['username'] ?? "");
    $password = $_POST['password'] ?? "";

    // Validasi: kedua kolom wajib diisi
    if ($username === "" || $password === "") {
        $error = "Username dan password wajib diisi.";
    } elseif (strlen($username) > 50) {
        // Kolom username di database maksimal 50 karakter
        $error = "Username atau password salah.";
    } else {
        // Mencari user berdasarkan username (prepared statement)
        $stmt = mysqli_prepare($conn, "SELECT id, username, password FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);

        // Hasil query ditampung ke tiga variabel ini
        mysqli_stmt_bind_result($stmt, $idDb, $usernameDb, $hashDb);

        // fetch() mengambil satu baris. Bernilai true jika username ditemukan.
        // password_verify() mencocokkan password input dengan hash di database.
        if (mysqli_stmt_fetch($stmt) && password_verify($password, $hashDb)) {

            // Membuat ID session baru agar aman dari pencurian session
            session_regenerate_id(true);

            // Menyimpan data login di session
            $_SESSION['user_id']  = $idDb;
            $_SESSION['username'] = $usernameDb;

            // Login berhasil, arahkan ke halaman To Do List
            header("Location: todo.php");
            exit;
        } else {
            // Pesan sengaja dibuat sama agar penyerang tidak tahu mana yang salah
            $error = "Username atau password salah.";
        }

        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - To Do List</title>
    <!-- Memanggil file CSS yang sudah disiapkan -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <nav class="navbar">
        <div class="logo">To Do <span>List</span></div>
    </nav>

    <main class="auth-wrap">
        <!-- tugas-card dibutuhkan agar input memakai gaya dari CSS -->
        <div class="card-content tugas-card auth-card">
            <p class="sub-title">SELAMAT DATANG</p>
            <h1 class="auth-title">Login</h1>

            <?php if ($error !== ""): ?>
                <!-- Pesan kesalahan; htmlspecialchars() untuk keamanan output -->
                <div class="pesan-error">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="post" action="login.php" autocomplete="off">
                <div class="t-row">
                    <label for="username">User Name</label>
                    <input type="text" id="username" name="username" maxlength="50" required
                           value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="t-row">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="t-btn">Submit</button>
            </form>
        </div>
    </main>

</body>
</html>