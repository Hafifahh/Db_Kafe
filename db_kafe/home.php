<?php
session_start();

if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Sistem Informasi Kafe</title>

    <link rel="stylesheet" href="src/style.css">
</head>

<body class="home-body">

    <header class="home-header">
        <h1>SISTEM INFORMASI KAFE</h1>
        <p>Selamat Datang di Sistem Informasi Kafe</p>
    </header>

    <nav class="navbar">

        <a href="home.php">HOME</a>

        <a href="menu/menu.php">MENU</a>

        <a href="pelanggan/pelanggan.php">PELANGGAN</a>

        <a href="riwayat_pesanan/riwayat_pesanan.php">RIWAYAT PESANAN</a>

        <a href="rekap_penjualan/rekap_penjualan.php">REKAP PENJUALAN</a>
           
        </a>

        <a href="logout.php">LOGOUT</a>

    </nav>

    <main class="home-content">

        <div class="welcome-box">

            <h2>SELAMAT DATANG 👋</h2>

            <p>
                Selamat datang di Sistem Informasi Kafe.
                Silakan pilih menu yang ingin digunakan.
            </p>

        </div>

        <div class="menu-cards">

    <a href="menu/menu.php" class="home-card">
        <div class="card-icon">☕</div>
        <h3>MENU</h3>
        <p>Kelola data menu kafe</p>
    </a>

    <a href="pelanggan/pelanggan.php" class="home-card">
        <div class="card-icon">👤</div>
        <h3>PELANGGAN</h3>
        <p>Kelola data pelanggan</p>
    </a>

    <a href="riwayat_pesanan/riwayat_pesanan.php" class="home-card">
        <div class="card-icon">🛒</div>
        <h3>RIWAYAT PESANAN</h3>
        <p>Lihat riwayat pesanan kafe</p>
    </a>

    <a href="rekap_penjualan/rekap_penjualan.php" class="home-card">
        <div class="card-icon">📋</div>
        <h3>REKAP PENJUALAN</h3>
        <p>Lihat hasil rekap kafe</p>
    </a>

</div>
    </main>

</body>
</html>