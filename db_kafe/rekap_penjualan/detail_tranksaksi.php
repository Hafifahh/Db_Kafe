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

$data_tranksaksi = null;
$detail_pesanan = null;

if ($id_tranksaksi > 0) {

    $query_tranksaksi = mysqli_query(
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

    if (!$query_tranksaksi) {
        die("Gagal mengambil transaksi: " . mysqli_error($koneksi));
    }

    $data_tranksaksi = mysqli_fetch_assoc($query_tranksaksi);

    if ($data_tranksaksi) {
        $detail_pesanan = mysqli_query(
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

        if (!$detail_pesanan) {
            die("Gagal mengambil detail: " . mysqli_error($koneksi));
        }
    }
}

$query = mysqli_query(
    $koneksi,
    "SELECT
        detail_tranksaksi.id_detail,
        detail_tranksaksi.id_tranksaksi,
        menu.nama_menu,
        detail_tranksaksi.jumlah,
        detail_tranksaksi.subtotal,
        tranksaksi.tanggal
    FROM detail_tranksaksi
    INNER JOIN menu
        ON detail_tranksaksi.id_menu = menu.id_menu
    INNER JOIN tranksaksi
        ON detail_tranksaksi.id_tranksaksi = tranksaksi.id_tranksaksi
    ORDER BY detail_tranksaksi.id_detail DESC"
);

if (!$query) {
    die("Gagal mengambil data detail: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Detail Tranksaksi</title>

<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f8eee3;
    color: #5b3621;
}

.header {
    background: #4b2c1c;
    color: white;
    text-align: center;
    padding: 30px;
}

.header h1 {
    margin: 0;
}

.navbar {
    background: white;
    padding: 18px;
    text-align: center;
}

.navbar a {
    text-decoration: none;
    color: #5b3621;
    font-weight: bold;
    margin: 0 12px;
}

.container {
    width: 90%;
    margin: 40px auto;
    background: white;
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.10);
    box-sizing: border-box;
}

h2 {
    text-align: center;
}

.ringkasan {
    max-width: 650px;
    margin: 30px auto;
    background: #f8eee3;
    padding: 25px;
    border-radius: 15px;
    box-sizing: border-box;
}

.ringkasan h3 {
    text-align: center;
    margin-top: 0;
    font-size: 22px;
}

.baris {
    display: flex;
    justify-content: space-between;
    gap: 15px;
    margin: 12px 0;
}

.total {
    border-top: 1px solid #d8c2ad;
    margin-top: 15px;
    padding-top: 15px;
    display: flex;
    justify-content: space-between;
    font-size: 20px;
    font-weight: bold;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 25px;
}

th {
    background: #8b5e3c;
    color: white;
    padding: 12px;
}

td {
    padding: 12px;
    text-align: center;
    border-bottom: 1px solid #ddd;
}

.edit {
    color: #8b5e3c;
    text-decoration: none;
    font-weight: bold;
}

.hapus {
    color: #b44b3b;
    text-decoration: none;
    font-weight: bold;
}

.pesan-sekarang {
    display: block;
    width: 220px;
    max-width: 100%;
    margin: 25px auto 0;
    background: #8b5e3c;
    color: white;
    padding: 13px;
    border-radius: 10px;
    text-align: center;
    text-decoration: none;
    font-weight: bold;
    box-sizing: border-box;
}

.pesan-sekarang:hover {
    background: #70452b;
}

.tabel-bungkus {
    overflow-x: auto;
}

@media (max-width: 700px) {
    .container {
        padding: 15px;
    }

    .navbar a {
        display: inline-block;
        margin: 8px;
    }
}
</style>
</head>

<body>

<div class="header">
    <h1>☕ DETAIL TRANKSAKSI</h1>
    <p>Rincian pesanan kafe</p>
</div>

<div class="navbar">
    <a href="../home.php">HOME</a>
    <a href="../menu/menu.php">MENU</a>
    <a href="../pelanggan/pelanggan.php">PELANGGAN</a>
    <a href="../tranksaksi/tranksaksi.php">TRANKSAKSI</a>
    <a href="detail_tranksaksi.php">DETAIL TRANKSAKSI</a>
    <a href="../logout.php">LOGOUT</a>
</div>

<div class="container">

<?php if ($data_tranksaksi && $detail_pesanan) { ?>

    <h2>RINGKASAN PESANAN</h2>

    <div class="ringkasan">
        <h3>Pesanan Kafe</h3>

        <div class="baris">
            <span>Nama pelanggan</span>
            <strong>
                <?= htmlspecialchars($data_tranksaksi["nama_pelanggan"]); ?>
            </strong>
        </div>

        <div class="baris">
            <span>ID tranksaksi</span>
            <strong><?= $data_tranksaksi["id_tranksaksi"]; ?></strong>
        </div>

        <div class="baris">
            <span>Tanggal</span>
            <strong><?= htmlspecialchars($data_tranksaksi["tanggal"]); ?></strong>
        </div>

        <hr>

        <h3>Menu yang dipesan</h3>

        <?php while ($item = mysqli_fetch_assoc($detail_pesanan)) { ?>
            <div class="baris">
                <span>
                    <?= htmlspecialchars($item["nama_menu"]); ?>
                    × <?= (int) $item["jumlah"]; ?>
                </span>

                <strong>
                    Rp <?= number_format($item["subtotal"], 0, ',', '.'); ?>
                </strong>
            </div>
        <?php } ?>

        <div class="total">
            <span>TOTAL</span>
            <span>
                Rp <?= number_format($data_tranksaksi["total"], 0, ',', '.'); ?>
            </span>
        </div>

        <a href="pesanan_berhasil.php" class="pesan-sekarang">
            PESAN SEKARANG
        </a>
    </div>

<?php } ?>

    <h2>DAFTAR DETAIL TRANKSAKSI</h2>

    <div class="tabel-bungkus">
        <table>
            <tr>
                <th>No</th>
                <th>ID Detail</th>
                <th>ID Tranksaksi</th>
                <th>Menu</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>

            <?php
            $no = 1;

            while ($data = mysqli_fetch_assoc($query)) {
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= $data["id_detail"]; ?></td>
                <td><?= $data["id_tranksaksi"]; ?></td>
                <td><?= htmlspecialchars($data["nama_menu"]); ?></td>
                <td><?= $data["jumlah"]; ?></td>
                <td>
                    Rp <?= number_format($data["subtotal"], 0, ',', '.'); ?>
                </td>
                <td><?= htmlspecialchars($data["tanggal"]); ?></td>
                <td>
                    <a
                        href="edit_detail_tranksaksi.php?id=<?= $data["id_detail"]; ?>"
                        class="edit">EDIT</a>

                    &nbsp;

                    <a
                        href="hapus_detail_tranksaksi.php?id=<?= $data["id_detail"]; ?>"
                        class="hapus"
                        onclick="return confirm('Yakin ingin menghapus detail ini?');">
                        HAPUS
                    </a>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>

</div>

</body>
</html>