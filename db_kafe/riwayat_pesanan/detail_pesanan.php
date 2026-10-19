<?php
session_start();
include "../koneksi.php";

if (!isset($_SESSION["login"])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("ID riwayat pesanan tidak valid.");
}

$id_riwayat = (int) $_GET["id"];

$query_riwayat = mysqli_query($koneksi, "
    SELECT rp.id_riwayat_pesanan, p.nama_pelanggan, rp.tanggal, rp.total
    FROM riwayat_pesanan rp
    INNER JOIN pelanggan p
        ON rp.id_pelanggan = p.id_pelanggan
    WHERE rp.id_riwayat_pesanan = $id_riwayat
");

if (!$query_riwayat) {
    die("Gagal mengambil riwayat pesanan: " . mysqli_error($koneksi));
}

$data_riwayat = mysqli_fetch_assoc($query_riwayat);

if (!$data_riwayat) {
    die("Riwayat pesanan tidak ditemukan.");
}

$query_detail = mysqli_query($koneksi, "
    SELECT m.nama_menu, m.harga, r.jumlah, r.subtotal
    FROM rekap r
    INNER JOIN menu m
        ON r.id_menu = m.id_menu
    WHERE r.id_riwayat_pesanan = $id_riwayat
");

if (!$query_detail) {
    die("Gagal mengambil detail pesanan: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px 15px;
            font-family: Arial, sans-serif;
            background: #f7f0e5;
            color: #3e2a20;
        }

        .container {
            max-width: 900px;
            margin: auto;
            padding: 25px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        h2 {
            margin-top: 0;
            color: #6f422b;
            text-align: center;
        }

        .info {
            margin-bottom: 25px;
            line-height: 1.8;
        }

        .tabel {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .tabel th {
            background: #6f422b;
            color: white;
        }

        .tabel th,
        .tabel td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .tabel tr:nth-child(even) {
            background: #faf5ef;
        }

        .total {
            margin-top: 20px;
            text-align: right;
            font-size: 18px;
            font-weight: bold;
        }

        .kembali {
            display: inline-block;
            margin-top: 25px;
            padding: 10px 16px;
            background: #6f422b;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .kembali:hover {
            background: #4d2d1e;
        }

        @media (max-width: 600px) {
            .container {
                padding: 15px;
                overflow-x: auto;
            }

            .tabel {
                min-width: 500px;
            }
        }
    </style>
</head>

<body>
<div class="container">

    <h2>Detail Pesanan</h2>

    <div class="info">
        <strong>ID Pesanan:</strong>
        <?= htmlspecialchars($data_riwayat["id_riwayat_pesanan"]) ?><br>

        <strong>Nama Pelanggan:</strong>
        <?= htmlspecialchars($data_riwayat["nama_pelanggan"]) ?><br>

        <strong>Tanggal:</strong>
        <?= htmlspecialchars($data_riwayat["tanggal"]) ?>
    </div>

    <table class="tabel">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Menu</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
            </tr>
        </thead>

        <tbody>
        <?php
        $no = 1;
        $total_hitung = 0;

        while ($detail = mysqli_fetch_assoc($query_detail)) {
            $total_hitung += (float) $detail["subtotal"];
        ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($detail["nama_menu"]) ?></td>
                <td>Rp <?= number_format((float) $detail["harga"], 0, ',', '.') ?></td>
                <td><?= htmlspecialchars($detail["jumlah"]) ?></td>
                <td>Rp <?= number_format((float) $detail["subtotal"], 0, ',', '.') ?></td>
            </tr>
        <?php } ?>

        <?php if ($no === 1) { ?>
            <tr>
                <td colspan="5">Belum ada detail menu untuk pesanan ini.</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <div class="total">
        Total Pesanan: Rp <?= number_format($total_hitung, 0, ',', '.') ?>
    </div>

    <a href="riwayat_pesanan.php" class="kembali">Kembali ke Riwayat Pesanan</a>

</div>
</body>
</html>