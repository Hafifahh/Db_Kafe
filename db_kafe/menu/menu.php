<?php

session_start();

if (!isset($_SESSION["login"])) {
    header("Location: ../login.php");
    exit();
}

include "../koneksi.php";

$data_menu = mysqli_query(
    $koneksi,
    "SELECT id_menu, nama_menu, harga, deskripsi, foto
     FROM menu"
);

if (!$data_menu) {
    die("ERROR DATABASE: " . mysqli_error($koneksi));
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Menu Kafe</title>

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
        }

        .judul {
            text-align: center;
            margin-bottom: 30px;
        }

        .tambah {
            display: inline-block;
            background: #8b5e3c;
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            margin-bottom: 25px;
            font-weight: bold;
        }

        .katalog {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .card {
            background: white;
            border-radius: 18px;
            padding: 18px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.10);
            text-align: center;
        }

        .foto {
            width: 100%;
            height: 190px;
            object-fit: cover;
            border-radius: 14px;
        }

        .nama {
            font-size: 21px;
            font-weight: bold;
            margin-top: 15px;
        }

        .deskripsi {
            color: #6b5545;
            line-height: 1.5;
            min-height: 50px;
        }

        .harga {
            font-size: 18px;
            font-weight: bold;
            margin: 12px 0;
        }

        .pesan {
            display: inline-block;
            background: #8b5e3c;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }

        .pesan:hover {
            background: #70452b;
        }

        .aksi {
            margin-top: 15px;
            padding-top: 12px;
            border-top: 1px solid #eee;
        }

        .edit {
            color: #8b5e3c;
            text-decoration: none;
            font-weight: bold;
            margin-right: 10px;
        }

        .hapus {
            color: #b44b3b;
            text-decoration: none;
            font-weight: bold;
        }

        @media (max-width: 900px) {
            .katalog {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .katalog {
                grid-template-columns: 1fr;
            }
        }

    </style>

</head>

<body>


<div class="header">

    <h1>☕ MENU KAFE</h1>

    <p>Berbagai pilihan menu yang tersedia</p>

</div>


<div class="navbar">

    <a href="../home.php">HOME</a>

    <a href="menu.php">MENU</a>

    <a href="../pelanggan/pelanggan.php">PELANGGAN</a>

    <a href="../riwayat_pesanan/riwayat_pesanan.php">RIWAYAT PESANAN</a>

    <a href="../rekap_penjualan/rekap_penjualan.php">REKAP PENJUALAN</a>
   

    <a href="../logout.php">LOGOUT</a>

</div>


<div class="container">

    <div class="judul">

        <h2>DAFTAR MENU</h2>

        <p>
            Pilih menu yang ingin kamu pesan
        </p>

    </div>


    <a href="tambah_menu.php" class="tambah">
        + TAMBAH MENU
    </a>


    <div class="katalog">


        <?php

        while ($menu = mysqli_fetch_assoc($data_menu)) {

        ?>

        <div class="card">


            <?php if (!empty($menu["foto"])) { ?>

                <img
                    src="../gambar/<?= htmlspecialchars($menu["foto"]); ?>"
                    class="foto"
                    alt="<?= htmlspecialchars($menu["nama_menu"]); ?>"
                >

            <?php } else { ?>

                <div
                    style="
                        height:190px;
                        background:#eee;
                        border-radius:14px;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                    "
                >
                    Tidak ada foto
                </div>

            <?php } ?>


            <div class="nama">

                <?= htmlspecialchars($menu["nama_menu"]); ?>

            </div>


            


            <div class="harga">

                Rp <?= number_format(
                    $menu["harga"],
                    0,
                    ',',
                    '.'
                ); ?>

            </div>


            <a
                href="pesan.php?id=<?= $menu["id_menu"]; ?>"
                class="pesan"
            >
                PESAN
            </a>


            <div class="aksi">

                <a
                    href="edit_menu.php?id=<?= $menu["id_menu"]; ?>"
                    class="edit"
                >
                    EDIT
                </a>

                <a
                    href="hapus_menu.php?id=<?= $menu["id_menu"]; ?>"
                    class="hapus"
                    onclick="return confirm('Yakin ingin menghapus menu ini?');"
                >
                    HAPUS
                </a>

            </div>


        </div>

        <?php

        }

        ?>


    </div>

</div>


</body>

</html>