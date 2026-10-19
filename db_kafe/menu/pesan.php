<?php
session_start();

if (!isset($_SESSION["login"])) {
    header("Location: ../login.php");
    exit;
}

include "../koneksi.php";

$query = mysqli_query($koneksi, "SELECT * FROM menu");

/* Pulihkan pesanan sebelumnya */
$draft = $_SESSION["draft_pesanan"] ?? [
    "nama_pelanggan" => "",
    "items" => []
];

$nama_awal = $draft["nama_pelanggan"];
$pilihan_awal = $draft["items"];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Menu Kafe</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 25px;
            font-family: Arial, sans-serif;
            background: #fff8f0;
            color: #49352a;
        }

        .container {
            max-width: 1000px;
            margin: auto;
        }

        h1, .keterangan { text-align: center; }

        .keterangan { margin-bottom: 25px; }

        .pelanggan {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
        }

        .pelanggan label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .pelanggan input {
            width: 100%;
            padding: 12px;
            border: 1px solid #d8c5b5;
            border-radius: 7px;
        }

        .daftar-menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .menu {
            background: white;
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 3px 10px #00000010;
        }

        .menu img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 8px;
        }

        .menu h3 { margin-bottom: 8px; }
        .harga { font-weight: bold; }

        .deskripsi {
            font-size: 14px;
            color: #77665b;
        }

        .pilih {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 15px;
        }

        .jumlah {
            width: 75px;
            padding: 7px;
        }

        button {
            width: 100%;
            margin-top: 25px;
            padding: 14px;
            border: none;
            border-radius: 8px;
            background: #795548;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover { background: #5d4037; }

        .kembali {
            display: block;
            text-align: center;
            margin-top: 18px;
            color: #795548;
        }
    </style>
</head>

<body>
<div class="container">

    <h1>PESAN MENU KAFE</h1>
    <p class="keterangan">
        Pilih satu atau beberapa menu yang ingin dipesan.
    </p>

    <form action="../riwayat_pesanan/tambah_tranksaksi.php"
          method="POST">

        <div class="pelanggan">
            <label>Nama Pelanggan</label>
            <input
                type="text"
                name="nama_pelanggan"
                placeholder="Masukkan nama pelanggan"
                value="<?= htmlspecialchars($nama_awal, ENT_QUOTES) ?>"
                required
            >
        </div>

        <div class="daftar-menu">

            <?php while ($menu = mysqli_fetch_assoc($query)) {
                $id = (int)$menu["id_menu"];
                $dipilih = isset($pilihan_awal[$id]);
                $jumlah_awal = $dipilih
                    ? (int)$pilihan_awal[$id]
                    : 1;
            ?>

                <div class="menu">

                    <?php if (!empty($menu["foto"])) { ?>
                        <img
                            src="../gambar/<?= rawurlencode($menu["foto"]); ?>"
                            alt="<?= htmlspecialchars($menu["nama_menu"]); ?>"
                        >
                    <?php } ?>

                    <h3>
                        <?= htmlspecialchars($menu["nama_menu"]); ?>
                    </h3>

                    <p class="harga">
                        Rp <?= number_format($menu["harga"], 0, ',', '.'); ?>
                    </p>

                    <p class="deskripsi">
                        <?= htmlspecialchars($menu["deskripsi"] ?? ""); ?>
                    </p>

                    <div class="pilih">
                        <input
                            type="checkbox"
                            name="pilih[]"
                            value="<?= $id ?>"
                            <?= $dipilih ? "checked" : "" ?>
                        >
                        <label>Pilih menu</label>
                    </div>

                    <p>Jumlah:</p>

                    <input
                        class="jumlah"
                        type="number"
                        name="jumlah[<?= $id ?>]"
                        value="<?= max(1, $jumlah_awal) ?>"
                        min="1"
                    >

                </div>

            <?php } ?>

        </div>

        <button type="submit" name="pesan_banyak">
            LANJUT KE TRANSAKSI
        </button>

    </form>

    <a class="kembali" href="menu.php">Kembali ke Menu</a>

</div>
</body>
</html>