<?php
// File: todo.php
// Fungsi: halaman utama To Do List (hanya bisa dibuka setelah login).

require "auth.php";     // satpam: belum login akan dilempar ke login.php
require "koneksi.php";  // menghasilkan variabel $conn

// ===== IDENTITAS MAHASISWA =====
$nama = "Alvian Prasetya";               
$nim  = "255314061";
$foto = "assets/foto-profile.jpeg";       
// ================================================

// Mengambil pesan sekali tampil (flash) dari tambah.php, lalu menghapusnya
$pesanError = $_SESSION['flash_error'] ?? "";
unset($_SESSION['flash_error']);

// Mengambil kegiatan milik user yang sedang login saja (prepared statement)
$userId = $_SESSION['user_id'];
$stmt = mysqli_prepare($conn, "SELECT id, kegiatan, selesai FROM todos WHERE user_id = ? ORDER BY id ASC");
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $idTodo, $teksTodo, $statusTodo);

// Memindahkan hasil ke array agar mudah ditampilkan di HTML
$daftar = [];
while (mysqli_stmt_fetch($stmt)) {
    $daftar[] = ['id' => $idTodo, 'kegiatan' => $teksTodo, 'selesai' => $statusTodo];
}
mysqli_stmt_close($stmt);

// Fungsi singkat agar penulisan htmlspecialchars tidak berulang
function e($teks)
{
    return htmlspecialchars((string)$teks, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To Do List</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <nav class="navbar">
        <div class="logo">To Do <span>List</span></div>
        <div class="nav-user">
            <span>Whats Up <?= e($_SESSION['username']) ?></span>
            <a href="logout.php" class="t-btn t-btn-kecil">Logout</a>
        </div>
    </nav>

    <main class="todo-wrap">

        <!-- HEADER IDENTITAS (syarat dosen: Nama, NIM, Foto) -->
        <header class="tugas-header">
            <img class="tugas-foto" src="<?= e($foto) ?>" alt="Foto <?= e($nama) ?>">
            <div>
                <div class="nama"><?= e($nama) ?></div>
                <div class="nim">NIM: <?= e($nim) ?></div>
            </div>
        </header>

        <div class="card-content tugas-card">
            <p class="sub-title">DAFTAR KEGIATAN</p>
            <h1 class="auth-title">To Do List</h1>

            <?php if ($pesanError !== ""): ?>
                <div class="pesan-error"><?= e($pesanError) ?></div>
            <?php endif; ?>

            <!-- FORM TAMBAH: dikirim ke tambah.php dengan method POST -->
            <form class="todo-form" method="post" action="tambah.php" autocomplete="off">
                <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
                <input type="text" name="kegiatan" maxlength="255" placeholder="Tulis kegiatan..." required>
                <button type="submit" class="t-btn">Tambah</button>
            </form>

            <!-- DAFTAR KEGIATAN -->
            <?php if (count($daftar) === 0): ?>
                <p class="todo-kosong">Belum ada kegiatan. Tambahkan satu di atas.</p>
            <?php else: ?>
                <ul class="todo-list">
                    <?php foreach ($daftar as $item): ?>
                        <!-- class "selesai" ditambahkan jika status = 1 (untuk gaya coret) -->
                        <li class="todo-item <?= $item['selesai'] ? 'selesai' : '' ?>">
                            <span class="todo-text"><?= e($item['kegiatan']) ?></span>

                            <div class="todo-aksi">
                                <?php if (!$item['selesai']): ?>
                                    <!-- Tombol Selesai hanya tampil untuk kegiatan yang belum selesai -->
                                    <form method="post" action="selesai.php">
                                        <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
                                        <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                        <button type="submit" class="aksi-link aksi-selesai">Selesai</button>
                                    </form>
                                <?php endif; ?>

                                <form method="post" action="hapus.php">
                                    <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
                                    <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                    <button type="submit" class="aksi-link aksi-hapus">Hapus</button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </main>

    <footer>&copy; <?= date('Y') ?> <?= e($nama) ?></footer>

</body>
</html>