<?php

session_start();

if (!isset($_SESSION["login"])) {
    header("Location: ../login.php");
    exit();
}

include "../koneksi.php";

if (isset($_POST["simpan"])) {

    foreach ($_POST["id_menu"] as $i => $id) {

        $nama = mysqli_real_escape_string(
            $koneksi,
            $_POST["nama_menu"][$i]
        );

        $harga = (int) $_POST["harga"][$i];

        $deskripsi = mysqli_real_escape_string(
            $koneksi,
            $_POST["deskripsi"][$i]
        );

        $id = (int) $id;

        $foto = "";

        if (
            isset($_FILES["foto"]["name"][$i]) &&
            $_FILES["foto"]["name"][$i] != ""
        ) {

            $foto = basename($_FILES["foto"]["name"][$i]);
            $tmp = $_FILES["foto"]["tmp_name"][$i];

            $folder = "../gambar/";

            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);
            }

            if (!move_uploaded_file($tmp, $folder . $foto)) {
                die("Foto gagal diunggah untuk menu " . htmlspecialchars($nama));
            }

            $foto_sql = ", foto='" .
                mysqli_real_escape_string($koneksi, $foto) . "'";
        } else {
            $foto_sql = "";
        }

        $query = mysqli_query(
            $koneksi,
            "UPDATE menu SET
                nama_menu='$nama',
                harga='$harga',
                deskripsi='$deskripsi'
                $foto_sql
             WHERE id_menu='$id'"
        );

        if (!$query) {
            die("Gagal mengubah menu: " . mysqli_error($koneksi));
        }
    }

    header("Location: menu.php");
    exit();
}

$data_menu = mysqli_query(
    $koneksi,
    "SELECT id_menu, nama_menu, harga, deskripsi, foto
     FROM menu
     ORDER BY id_menu ASC"
);

if (!$data_menu) {
    die("Gagal mengambil data menu: " . mysqli_error($koneksi));
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Menu Kafe</title>

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
    padding: 25px;
}
.container {
    width: 700px;
    max-width: 90%;
    margin: 35px auto;
}
.menu-box {
    background: white;
    padding: 25px;
    margin-bottom: 20px;
    border-radius: 18px;
    box-shadow: 0 5px 15px rgba(0,0,0,.07);
}
label {
    display: block;
    font-weight: bold;
    margin-top: 15px;
    margin-bottom: 7px;
}
input, textarea {
    width: 100%;
    box-sizing: border-box;
    padding: 12px;
    border: 1px solid #d8c2ad;
    border-radius: 8px;
}
textarea {
    height: 90px;
    resize: vertical;
}
.foto-lama {
    width: 140px;
    height: 100px;
    object-fit: cover;
    border-radius: 10px;
    display: block;
    margin-bottom: 10px;
}
button {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 8px;
    background: #8b5e3c;
    color: white;
    font-weight: bold;
    cursor: pointer;
}
.kembali {
    display: block;
    text-align: center;
    margin: 20px;
    color: #8b5e3c;
    text-decoration: none;
}
</style>
</head>

<body>

<div class="header">
    <h1>☕ MENU KAFE</h1>
    <p>Edit Data Menu</p>
</div>

<div class="container">

    <h2>EDIT SEMUA MENU</h2>

    <form method="POST" enctype="multipart/form-data">

        <?php
        $i = 0;

        while ($menu = mysqli_fetch_assoc($data_menu)) {
        ?>

        <div class="menu-box">

            <h2><?= htmlspecialchars($menu["nama_menu"]); ?></h2>

            <input
                type="hidden"
                name="id_menu[<?= $i; ?>]"
                value="<?= $menu["id_menu"]; ?>"
            >

            <label>Nama Menu</label>
            <input
                type="text"
                name="nama_menu[<?= $i; ?>]"
                value="<?= htmlspecialchars($menu["nama_menu"]); ?>"
                required
            >

            <label>Foto Menu Saat Ini</label>

            <?php if (!empty($menu["foto"])) { ?>
                <img
                    src="../gambar/<?= rawurlencode($menu["foto"]); ?>"
                    class="foto-lama"
                    alt="Foto menu"
                >
            <?php } else { ?>
                <p>Belum ada foto.</p>
            <?php } ?>

            <label>Ganti Foto (opsional)</label>
            <input
                type="file"
                name="foto[<?= $i; ?>]"
                accept="image/*"
            >

            <label>Deskripsi</label>
            <textarea
                name="deskripsi[<?= $i; ?>]"
                required
            ><?= htmlspecialchars($menu["deskripsi"]); ?></textarea>

            <label>Harga</label>
            <input
                type="number"
                name="harga[<?= $i; ?>]"
                value="<?= $menu["harga"]; ?>"
                min="0"
                required
            >

        </div>

        <?php
            $i++;
        }
        ?>

        <button type="submit" name="simpan">
            SIMPAN SEMUA PERUBAHAN
        </button>

    </form>

    <a href="menu.php" class="kembali">
        ← Kembali ke Menu
    </a>

</div>

</body>
</html>