<?php
session_start();
include "../koneksi.php";

if (!isset($_SESSION["login"])) {
    header("Location: ../login.php");
    exit();
}

// Ringkasan penjualan
$query_ringkasan = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total_transaksi,
            COALESCE(SUM(total), 0) AS total_pendapatan
     FROM riwayat_pesanan"
);

if (!$query_ringkasan) {
    die("ERROR DATABASE: " . mysqli_error($koneksi));
}

$ringkasan = mysqli_fetch_assoc($query_ringkasan);

// Penjualan per tanggal
$query_harian = mysqli_query(
    $koneksi,
    "SELECT DATE(tanggal) AS tanggal_penjualan,
            COUNT(*) AS jumlah_transaksi,
            COALESCE(SUM(total), 0) AS pendapatan
     FROM riwayat_pesanan
     GROUP BY DATE(tanggal)
     ORDER BY DATE(tanggal) DESC"
);

if (!$query_harian) {
    die("ERROR DATABASE: " . mysqli_error($koneksi));
}

// Jumlah menu terjual
$query_menu = mysqli_query(
    $koneksi,
    "SELECT m.id_menu, m.nama_menu, m.harga, m.foto,
            COALESCE(SUM(r.jumlah), 0) AS jumlah_terjual
     FROM menu m
     LEFT JOIN rekap r ON m.id_menu = r.id_menu
     GROUP BY m.id_menu, m.nama_menu, m.harga, m.foto
     ORDER BY jumlah_terjual DESC, m.nama_menu ASC"
);

if (!$query_menu) {
    die("ERROR DATABASE: " . mysqli_error($koneksi));
}

$daftar_menu = [];

while ($menu = mysqli_fetch_assoc($query_menu)) {
    $daftar_menu[] = $menu;
}

// Menu terlaris dan paling sedikit terjual
$menu_terlaris = [];
$menu_tersedikit = [];

if (count($daftar_menu) > 0) {
    $jumlah_terbanyak = (int)$daftar_menu[0]["jumlah_terjual"];
    $jumlah_tersedikit = (int)$daftar_menu[0]["jumlah_terjual"];

    foreach ($daftar_menu as $menu) {
        $jumlah = (int)$menu["jumlah_terjual"];

        if ($jumlah > $jumlah_terbanyak) {
            $jumlah_terbanyak = $jumlah;
        }

        if ($jumlah < $jumlah_tersedikit) {
            $jumlah_tersedikit = $jumlah;
        }
    }

    foreach ($daftar_menu as $menu) {
        $jumlah = (int)$menu["jumlah_terjual"];

        if ($jumlah === $jumlah_terbanyak) {
            $menu_terlaris[] = $menu;
        }

        if ($jumlah === $jumlah_tersedikit) {
            $menu_tersedikit[] = $menu;
        }
    }
}

// Riwayat transaksi
$query_riwayat = mysqli_query(
    $koneksi,
    "SELECT rp.id_riwayat_pesanan,
            p.nama_pelanggan,
            rp.tanggal,
            rp.total
     FROM riwayat_pesanan rp
     LEFT JOIN pelanggan p
        ON rp.id_pelanggan = p.id_pelanggan
     ORDER BY rp.tanggal DESC"
);

if (!$query_riwayat) {
    die("ERROR DATABASE: " . mysqli_error($koneksi));
}

function rupiah($angka) {
    return "Rp " . number_format((float)$angka, 0, ",", ".");
}

function tanggalIndo($tanggal) {
    if (!$tanggal) {
        return "-";
    }

    return date("d-m-Y", strtotime($tanggal));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Penjualan Kafe</title>

    <style>
        * {
            box-sizing: border-box;
        }

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
            width: 90%;
            margin: 35px auto;
        }

        .judul {
            text-align: center;
            margin-bottom: 25px;
        }

        .kartu-ringkasan {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .kartu {
            background: white;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.10);
            text-align: center;
        }

        .kartu h3 {
            margin-top: 0;
            color: #8b5e3c;
        }

        .angka {
            font-size: 23px;
            font-weight: bold;
            overflow-wrap: anywhere;
        }

        .bagian {
            background: white;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.10);
            margin-bottom: 30px;
        }

        .bagian h2 {
            text-align: center;
            margin-top: 0;
        }

        .kartu-menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
        }

        .menu-item {
            background: #fffaf5;
            border: 1px solid #ead8c7;
            border-radius: 14px;
            padding: 15px;
            text-align: center;
        }

        .menu-item img {
            display: block;
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 10px;
            background: #f8eee3;
        }

        .menu-item h3 {
            margin: 6px 0 3px;
            padding: 0;
            line-height: 1.2;
            overflow-wrap: anywhere;
        }

        .menu-item p {
            margin: 2px 0;
            padding: 0;
            line-height: 1.2;
            overflow-wrap: anywhere;
        }

        .menu-unggulan {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
            align-items: stretch;
        }

        .menu-unggulan .menu-item {
            display: grid;
            grid-template-columns: 140px minmax(0, 1fr);
            grid-template-rows: auto 140px;
            column-gap: 15px;
            row-gap: 0;
            align-content: start;
            align-items: start;
            min-width: 0;
            width: 100%;
            padding: 15px;
            text-align: left;
            overflow: hidden;
        }

        .menu-unggulan .label {
            grid-column: 1 / -1;
            grid-row: 1;
            justify-self: start;
            display: inline-block;
            background: #8b5e3c;
            color: white;
            padding: 7px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin: 0 0 10px;
        }

        .menu-unggulan .menu-item img {
            grid-column: 1;
            grid-row: 2;
            display: block;
            width: 140px;
            height: 140px;
            object-fit: cover;
            object-position: center;
            border-radius: 15px;
            background: #f8eee3;
            margin: 0;
        }

        .menu-unggulan .menu-item h3,
        .menu-unggulan .menu-item p {
            grid-column: 2;
            align-self: start;
            justify-self: stretch;
            min-width: 0;
            margin: 0 !important;
            padding: 0;
            overflow-wrap: anywhere;
            line-height: 1.2;
        }

        .menu-unggulan .menu-item h3 {
            grid-row: 2;
            color: #4b2c1c;
            margin-bottom: 4px !important;
        }

        .menu-unggulan .menu-item p:nth-of-type(1) {
            grid-row: 2;
            margin-top: 26px !important;
        }

        .menu-unggulan .menu-item p:nth-of-type(2) {
            grid-row: 2;
            margin-top: 48px !important;
        }

        .tabel-bungkus {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 500px;
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
            }

            .header {
                padding: 24px 12px;
            }

            .bagian {
                padding: 15px;
            }

            .angka {
                font-size: 20px;
            }

            .menu-unggulan {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .menu-unggulan .menu-item {
                grid-template-columns: 100px minmax(0, 1fr);
                grid-template-rows: auto 100px;
                column-gap: 12px;
                row-gap: 0;
                padding: 12px;
            }

            .menu-unggulan .menu-item img {
                width: 100px;
                height: 100px;
                border-radius: 12px;
            }

            .menu-unggulan .menu-item h3 {
                font-size: 15px;
            }

            .menu-unggulan .label {
                padding: 6px 9px;
                font-size: 11px;
            }

            .menu-unggulan .menu-item p:nth-of-type(1) {
                margin-top: 24px !important;
            }

            .menu-unggulan .menu-item p:nth-of-type(2) {
                margin-top: 44px !important;
            }

            .menu-item h3 {
                margin: 5px 0 3px;
            }

            .menu-item p {
                margin: 2px 0;
            }
        }
    </style>
</head>

<body>

<div class="header">
    <h1>☕ REKAP PENJUALAN KAFE</h1>
    <p>Ringkasan transaksi dan penjualan kafe</p>
</div>

<div class="navbar">
    <a href="../home.php">HOME</a>
    <a href="../menu/menu.php">MENU</a>
    <a href="../pelanggan/pelanggan.php">PELANGGAN</a>
    <a href="../riwayat_pesanan/riwayat_pesanan.php">RIWAYAT PESANAN</a>
    <a href="rekap_penjualan.php">REKAP PENJUALAN</a>
    <a href="../logout.php">LOGOUT</a>
</div>

<div class="container">

    <div class="judul">
        <h2>RINGKASAN PENJUALAN</h2>
        <p>Informasi transaksi dan penjualan kafe.</p>
    </div>

    <div class="kartu-ringkasan">
        <div class="kartu">
            <h3>Total Transaksi</h3>
            <div class="angka">
                <?= (int)$ringkasan["total_transaksi"]; ?>
            </div>
        </div>

        <div class="kartu">
            <h3>Total Pendapatan</h3>
            <div class="angka">
                <?= rupiah($ringkasan["total_pendapatan"]); ?>
            </div>
        </div>

        <div class="kartu">
            <h3>Total Jenis Menu</h3>
            <div class="angka">
                <?= count($daftar_menu); ?>
            </div>
        </div>
    </div>

    <div class="bagian">
        <h2>MENU TERLARIS DAN PALING SEDIKIT TERJUAL</h2>

        <div class="kartu-menu menu-unggulan">

            <?php foreach ($menu_terlaris as $menu) { ?>
                <div class="menu-item">
                    <span class="label">Menu Terlaris</span>

                    <?php if (!empty($menu["foto"])) { ?>
                        <img
                            src="../src/<?= htmlspecialchars($menu["foto"]); ?>"
                            alt="<?= htmlspecialchars($menu["nama_menu"]); ?>"
                        >
                    <?php } ?>

                    <h3><?= htmlspecialchars($menu["nama_menu"]); ?></h3>
                    <p><?= rupiah($menu["harga"]); ?></p>
                    <p>
                        Terjual:
                        <strong><?= (int)$menu["jumlah_terjual"]; ?></strong>
                    </p>
                </div>
            <?php } ?>

            <?php foreach ($menu_tersedikit as $menu) { ?>
                <div class="menu-item">
                    <span class="label">Paling Sedikit Terjual</span>

                    <?php if (!empty($menu["foto"])) { ?>
                        <img
                            src="../src/<?= htmlspecialchars($menu["foto"]); ?>"
                            alt="<?= htmlspecialchars($menu["nama_menu"]); ?>"
                        >
                    <?php } ?>

                    <h3><?= htmlspecialchars($menu["nama_menu"]); ?></h3>
                    <p><?= rupiah($menu["harga"]); ?></p>
                    <p>
                        Terjual:
                        <strong><?= (int)$menu["jumlah_terjual"]; ?></strong>
                    </p>
                </div>
            <?php } ?>

            <?php if (count($daftar_menu) === 0) { ?>
                <p class="kosong">Belum ada data menu.</p>
            <?php } ?>

        </div>
    </div>

    <div class="bagian">
        <h2>DAFTAR SEMUA MENU</h2>

        <div class="kartu-menu">
            <?php if (count($daftar_menu) > 0) { ?>
                <?php foreach ($daftar_menu as $menu) { ?>
                    <div class="menu-item">

                        <?php if (!empty($menu["foto"])) { ?>
                            <img
                                src="../src/<?= htmlspecialchars($menu["foto"]); ?>"
                                alt="<?= htmlspecialchars($menu["nama_menu"]); ?>"
                            >
                        <?php } ?>

                        <h3><?= htmlspecialchars($menu["nama_menu"]); ?></h3>
                        <p><?= rupiah($menu["harga"]); ?></p>
                        <p>
                            Jumlah terjual:
                            <strong><?= (int)$menu["jumlah_terjual"]; ?></strong>
                        </p>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <p class="kosong">Belum ada data menu.</p>
            <?php } ?>
        </div>
    </div>

    <div class="bagian">
        <h2>PENJUALAN PER TANGGAL</h2>

        <div class="tabel-bungkus">
            <table>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Jumlah Transaksi</th>
                    <th>Pendapatan</th>
                </tr>

                <?php
                $no = 1;
                $ada_harian = false;

                while ($data = mysqli_fetch_assoc($query_harian)) {
                    $ada_harian = true;
                ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= tanggalIndo($data["tanggal_penjualan"]); ?></td>
                        <td><?= (int)$data["jumlah_transaksi"]; ?></td>
                        <td><?= rupiah($data["pendapatan"]); ?></td>
                    </tr>
                <?php } ?>

                <?php if (!$ada_harian) { ?>
                    <tr>
                        <td colspan="4" class="kosong">
                            Belum ada data penjualan.
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>

    <div class="bagian">
        <h2>RIWAYAT TRANSAKSI</h2>

        <div class="tabel-bungkus">
            <table>
                <tr>
                    <th>No</th>
                    <th>Nama Pelanggan</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                </tr>

                <?php
                $no = 1;
                $ada_riwayat = false;

                while ($data = mysqli_fetch_assoc($query_riwayat)) {
                    $ada_riwayat = true;
                ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td>
                            <?= htmlspecialchars($data["nama_pelanggan"] ?? "-"); ?>
                        </td>
                        <td><?= tanggalIndo($data["tanggal"]); ?></td>
                        <td><?= rupiah($data["total"]); ?></td>
                    </tr>
                <?php } ?>

                <?php if (!$ada_riwayat) { ?>
                    <tr>
                        <td colspan="4" class="kosong">
                            Belum ada riwayat transaksi.
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>

</div>

</body>
</html>