<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}

/* Ambil pesanan terakhir yang tersimpan */
$query = mysqli_query($koneksi, "
    SELECT rp.id_riwayat_pesanan,
           rp.total,
           p.nama_pelanggan
    FROM riwayat_pesanan rp
    JOIN pelanggan p
        ON rp.id_pelanggan = p.id_pelanggan
    ORDER BY rp.id_riwayat_pesanan DESC
    LIMIT 1
");

$pesanan = mysqli_fetch_assoc($query);
$detail = [];

if ($pesanan) {
    $id_riwayat = $pesanan["id_riwayat_pesanan"];

    $query_detail = mysqli_query($koneksi, "
        SELECT m.nama_menu, m.harga, r.jumlah, r.subtotal
        FROM rekap r
        JOIN menu m ON r.id_menu = m.id_menu
        WHERE r.id_riwayat_pesanan = '$id_riwayat'
    ");

    while ($row = mysqli_fetch_assoc($query_detail)) {
        $detail[] = $row;
    }
}

function rupiah($angka) {
    return "Rp " . number_format($angka, 0, ",", ".");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 25px;
            font-family: Arial, sans-serif;
            background: #f5e6d7;
            color: #4b2c1c;
        }

        .container {
            max-width: 600px;
            margin: 35px auto;
            padding: 30px;
            background: #fffaf5;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(75, 44, 28, 0.15);
        }

        .ikon {
            font-size: 55px;
            text-align: center;
            margin-bottom: 10px;
        }

        h1 {
            text-align: center;
            color: #5b3522;
            margin: 0 0 10px;
        }

        .pesan {
            text-align: center;
            margin-bottom: 25px;
            color: #79553e;
        }

        .info {
            padding: 15px;
            background: #f5e6d7;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .info p {
            margin: 8px 0;
        }

        h3 {
            border-bottom: 2px solid #e3c7ad;
            padding-bottom: 8px;
        }

        .item {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid #ead8c7;
        }

        .total {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
        }

        .tombol {
            display: block;
            text-align: center;
            text-decoration: none;
            background: #6b422b;
            color: white;
            padding: 13px;
            border-radius: 8px;
            margin-top: 25px;
            font-weight: bold;
        }

        .tombol:hover {
            background: #4b2c1c;
        }

        .kosong {
            text-align: center;
            padding: 20px 0;
        }
    </style>
</head>

<body>
    <div class="container">

        <?php if ($pesanan): ?>

            <div class="ikon">✅</div>

            <h1>Pesanan Berhasil!</h1>

            <p class="pesan">
                Terima kasih, pesanan kamu berhasil disimpan.
            </p>

            <div class="info">
                <p>
                    <strong>Nama Pelanggan:</strong>
                    <?= htmlspecialchars($pesanan["nama_pelanggan"]) ?>
                </p>
            </div>

            <h3>Detail Pesanan</h3>

            <?php foreach ($detail as $item): ?>
                <div class="item">
                    <div>
                        <strong>
                            <?= htmlspecialchars($item["nama_menu"]) ?>
                        </strong>
                        <br>
                        <?= (int)$item["jumlah"] ?> x
                        <?= rupiah($item["harga"]) ?>
                    </div>

                    <div>
                        <?= rupiah($item["subtotal"]) ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="total">
                <span>Total Pembayaran</span>
                <span><?= rupiah($pesanan["total"]) ?></span>
            </div>

        <?php else: ?>

            <div class="ikon">ℹ️</div>
            <h1>Belum Ada Pesanan</h1>
            <p class="pesan">Belum ada data pesanan yang tersimpan.</p>

        <?php endif; ?>

        <a href="home.php" class="tombol">KEMBALI KE HOME</a>

    </div>
</body>
</html>