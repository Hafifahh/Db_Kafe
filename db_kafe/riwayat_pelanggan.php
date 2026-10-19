<?php

session_start();

if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit();
}

include "koneksi.php";


$query = mysqli_query(
    $koneksi,
    "SELECT
        pelanggan.nama_pelanggan,
        menu.nama_menu,
        detail_transaksi.jumlah,
        tranksaksi.tanggal,
        tranksaksi.total

    FROM pelanggan

    INNER JOIN tranksaksi
    ON pelanggan.id_pelanggan = tranksaksi.id_pelanggan

    INNER JOIN detail_tranksaksi
    ON tranksaksi.id_tranksaksi = detail_tranksaksi.id_tranksaksi

    INNER JOIN menu
    ON detail_tranksaksi.id_menu = menu.id_menu

    ORDER BY tranksaksi.tanggal DESC"
);


if (!$query) {
    die(
        "Gagal mengambil riwayat: "
        . mysqli_error($koneksi)
    );
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Riwayat Pelanggan</title>

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

        .container {
            width: 90%;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.10);
        }

        h2 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        th {
            background: #8b5e3c;
            color: white;
            padding: 13px;
        }

        td {
            padding: 13px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        .kembali {
            display: inline-block;
            margin-top: 25px;
            padding: 10px 18px;
            background: #8b5e3c;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
        }

    </style>

</head>

<body>

<div class="header">

    <h1>☕ RIWAYAT PELANGGAN</h1>

    <p>Daftar pelanggan yang sudah melakukan pemesanan</p>

</div>


<div class="container">

    <h2>RIWAYAT PEMESANAN</h2>

    <table>

        <tr>

            <th>No</th>

            <th>Nama Pelanggan</th>

            <th>Menu</th>

            <th>Jumlah</th>

            <th>Tanggal</th>

            <th>Total</th>

        </tr>


        <?php

        $no = 1;

        while ($data = mysqli_fetch_assoc($query)) {

        ?>

        <tr>

            <td>
                <?= $no++; ?>
            </td>

            <td>
                <?= $data["nama_pelanggan"]; ?>
            </td>

            <td>
                <?= $data["nama_menu"]; ?>
            </td>

            <td>
                <?= $data["jumlah"]; ?>
            </td>

            <td>
                <?= date(
                    "d-m-Y",
                    strtotime($data["tanggal"])
                ); ?>
            </td>

            <td>
                Rp <?= number_format(
                    $data["total"],
                    0,
                    ',',
                    '.'
                ); ?>
            </td>

        </tr>

        <?php
        }
        ?>

    </table>


    <a href="home.php" class="kembali">
        ← Kembali ke Home
    </a>

</div>

</body>

</html>