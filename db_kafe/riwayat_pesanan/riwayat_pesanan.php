<?php
session_start();

if (!isset($_SESSION["login"])) {
    header("Location: ../login.php");
    exit();
}

include "../koneksi.php";

$data_riwayat = mysqli_query(
    $koneksi,
    "SELECT
        riwayat_pesanan.id_riwayat_pesanan,
        pelanggan.nama_pelanggan,
        riwayat_pesanan.tanggal,
        riwayat_pesanan.total
    FROM riwayat_pesanan
    INNER JOIN pelanggan
        ON riwayat_pesanan.id_pelanggan = pelanggan.id_pelanggan
    ORDER BY riwayat_pesanan.id_riwayat_pesanan ASC"
);

if (!$data_riwayat) {
    die("ERROR DATABASE: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan Kafe</title>

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
            display: inline-block;
            text-decoration: none;
            color: #5b3621;
            font-weight: bold;
            margin: 8px 12px;
        }

        .navbar a:hover {
            color: #b17b50;
        }

        .container {
            width: 85%;
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

        .keterangan {
            text-align: center;
            margin-bottom: 25px;
        }

        .tabel-bungkus {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
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

        .nama-pelanggan {
            color: #5b3621;
            font-weight: bold;
            text-decoration: none;
        }

        .nama-pelanggan:hover {
            color: #b17b50;
            text-decoration: underline;
        }

        tbody tr:hover {
            background: #fff8f1;
        }

        .kosong {
            text-align: center;
            padding: 25px;
            color: #8b5e3c;
        }

        @media (max-width: 600px) {
            .container {
                width: 94%;
                padding: 15px;
            }
        }
    </style>
</head>

<body>

<div class="header">
    <h1>☕ RIWAYAT PESANAN KAFE</h1>
    <p>Daftar riwayat pesanan pelanggan</p>
</div>

<div class="navbar">
    <a href="../home.php">HOME</a>
    <a href="../menu/menu.php">MENU</a>
    <a href="../pelanggan/pelanggan.php">PELANGGAN</a>
    <a href="riwayat_pesanan.php">RIWAYAT PESANAN</a>
    <a href="../rekap_penjualan/rekap_penjualan.php">REKAP PENJUALAN</a>
    <a href="../logout.php">LOGOUT</a>
</div>

<div class="container">

    <h2>DAFTAR RIWAYAT PESANAN</h2>

    <p class="keterangan">
        Klik nama pelanggan untuk melihat menu dan jumlah pesanannya.
    </p>

    <div class="tabel-bungkus">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pelanggan</th>
                    <th>Tanggal</th>
                    <th>Total Pesanan</th>
                </tr>
            </thead>

            <tbody>
            <?php
            $no = 1;
            $ada_data = false;

            while ($data = mysqli_fetch_assoc($data_riwayat)) {
                $ada_data = true;
            ?>

                <tr>
                    <td><?= $no++; ?></td>

                    <td>
                        <a class="nama-pelanggan"
                           href="detail_pesanan.php?id=<?= (int)$data["id_riwayat_pesanan"]; ?>">
                            <?= htmlspecialchars($data["nama_pelanggan"]); ?>
                        </a>
                    </td>

                    <td>
                        <?= htmlspecialchars($data["tanggal"]); ?>
                    </td>

                    <td>
                        Rp <?= number_format((float)$data["total"], 0, ",", "."); ?>
                    </td>
                </tr>

            <?php } ?>

            <?php if (!$ada_data) { ?>
                <tr>
                    <td colspan="4" class="kosong">
                        Belum ada riwayat pesanan.
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>