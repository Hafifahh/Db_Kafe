<?php
session_start();

if (!isset($_SESSION["login"])) {
    header("Location: ../login.php");
    exit();
}

include "../koneksi.php";

$data_pelanggan = mysqli_query(
    $koneksi,
    "SELECT
        pelanggan.id_pelanggan,
        pelanggan.nama_pelanggan,
        MAX(riwayat_pesanan.id_riwayat_pesanan) AS pesanan_terakhir
    FROM pelanggan
    INNER JOIN riwayat_pesanan
        ON pelanggan.id_pelanggan = riwayat_pesanan.id_pelanggan
    GROUP BY
        pelanggan.id_pelanggan,
        pelanggan.nama_pelanggan
    ORDER BY pesanan_terakhir ASC"
);

if (!$data_pelanggan) {
    die("ERROR DATABASE: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelanggan Kafe</title>

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
    <h1>☕ PELANGGAN KAFE</h1>
    <p>Data pelanggan kafe</p>
</div>

<div class="navbar">
    <a href="../home.php">HOME</a>
    <a href="../menu/menu.php">MENU</a>
    <a href="pelanggan.php">PELANGGAN</a>
    <a href="../riwayat_pesanan/riwayat_pesanan.php">RIWAYAT PESANAN</a>
    <a href="../rekap_penjualan/rekap_penjualan.php">REKAP PENJUALAN</a>
    <a href="../logout.php">LOGOUT</a>
</div>

<div class="container">

    <h2>DAFTAR PELANGGAN</h2>

    <p class="keterangan">
        Daftar pelanggan yang pernah melakukan transaksi.
    </p>

    <div class="tabel-bungkus">
        <table>
            <tr>
                <th>No</th>
                <th>Nama Pelanggan</th>
            </tr>

            <?php
            $no = 1;
            $ada_data = false;

            while ($data = mysqli_fetch_assoc($data_pelanggan)) {
                $ada_data = true;
            ?>

            <tr>
                <td><?= $no++; ?></td>
                <td>
                    <?= htmlspecialchars($data["nama_pelanggan"]); ?>
                </td>
            </tr>

            <?php } ?>

            <?php if (!$ada_data) { ?>
            <tr>
                <td colspan="2" class="kosong">
                    Belum ada pelanggan yang melakukan transaksi.
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>

</div>

</body>
</html>