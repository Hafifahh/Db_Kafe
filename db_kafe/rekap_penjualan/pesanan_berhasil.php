<?php
session_start();

if (!isset($_SESSION["login"])) {
    header("Location: ../login.php");
    exit();
}

include "../koneksi.php";

$id_tranksaksi = isset($_SESSION["id_tranksaksi"])
    ? (int) $_SESSION["id_tranksaksi"]
    : 0;

if ($id_tranksaksi <= 0) {
    header("Location: ../menu/menu.php");
    exit();
}

$query_transaksi = mysqli_query(
    $koneksi,
    "SELECT
        tranksaksi.id_tranksaksi,
        tranksaksi.tanggal,
        tranksaksi.total,
        pelanggan.nama_pelanggan
    FROM tranksaksi
    INNER JOIN pelanggan
        ON tranksaksi.id_pelanggan = pelanggan.id_pelanggan
    WHERE tranksaksi.id_tranksaksi = $id_tranksaksi"
);

if (!$query_transaksi) {
    die("Gagal mengambil transaksi: " . mysqli_error($koneksi));
}

$transaksi = mysqli_fetch_assoc($query_transaksi);

if (!$transaksi) {
    die("Data transaksi tidak ditemukan.");
}

$query_detail = mysqli_query(
    $koneksi,
    "SELECT
        menu.nama_menu,
        menu.harga,
        detail_tranksaksi.jumlah,
        detail_tranksaksi.subtotal
    FROM detail_tranksaksi
    INNER JOIN menu
        ON detail_tranksaksi.id_menu = menu.id_menu
    WHERE detail_tranksaksi.id_tranksaksi = $id_tranksaksi"
);

if (!$query_detail) {
    die("Gagal mengambil detail pesanan: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pesanan Berhasil</title>

<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f8eee3;
    color: #5b3621;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 20px;
    box-sizing: border-box;
}

.box {
    width: 520px;
    max-width: 100%;
    background: white;
    padding: 35px;
    border-radius: 25px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    box-sizing: border-box;
}

.icon {
    font-size: 60px;
    margin-bottom: 15px;
    color: #8b5e3c;
}

h1 {
    color: #4b2c1c;
}

.ringkasan {
    background: #f8eee3;
    padding: 20px;
    border-radius: 15px;
    margin: 25px 0;
    text-align: left;
}

.ringkasan h3 {
    text-align: center;
    margin-top: 0;
    font-size: 22px;
}

.detail {
    display: flex;
    justify-content: space-between;
    gap: 15px;
    margin: 12px 0;
}

.nama-menu {
    flex: 1;
}

.harga-menu {
    text-align: right;
    font-weight: bold;
}

.total {
    border-top: 1px solid #d8c2ad;
    margin-top: 15px;
    padding-top: 15px;
    display: flex;
    justify-content: space-between;
    gap: 15px;
    font-size: 20px;
    font-weight: bold;
}

a {
    display: inline-block;
    background: #8b5e3c;
    color: white;
    padding: 13px 25px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: bold;
}

a:hover {
    background: #70452b;
}
</style>
</head>

<body>

<div class="box">

    <div class="icon">✓</div>

    <h1>PESANAN BERHASIL</h1>

    <p>Pesanan kamu berhasil disimpan.</p>

    <div class="ringkasan">

        <h3>Rincian Pesanan</h3>

        <div class="detail">
            <span>Nama pelanggan</span>
            <strong>
                <?= htmlspecialchars($transaksi["nama_pelanggan"]); ?>
            </strong>
        </div>

        <div class="detail">
            <span>ID transaksi</span>
            <strong><?= $transaksi["id_tranksaksi"]; ?></strong>
        </div>

        <div class="detail">
            <span>Tanggal</span>
            <strong><?= htmlspecialchars($transaksi["tanggal"]); ?></strong>
        </div>

        <hr>

        <?php while ($item = mysqli_fetch_assoc($query_detail)) { ?>

            <div class="detail">
                <div class="nama-menu">
                    <?= htmlspecialchars($item["nama_menu"]); ?>
                    <br>
                    <small>
                        <?= (int) $item["jumlah"]; ?> pcs ×
                        Rp <?= number_format($item["harga"], 0, ',', '.'); ?>
                    </small>
                </div>

                <div class="harga-menu">
                    Rp <?= number_format($item["subtotal"], 0, ',', '.'); ?>
                </div>
            </div>

        <?php } ?>

        <div class="total">
            <span>TOTAL</span>
            <span>
                Rp <?= number_format($transaksi["total"], 0, ',', '.'); ?>
            </span>
        </div>

    </div>

    <a href="../menu/menu.php">KEMBALI KE MENU</a>

</div>

</body>
</html>