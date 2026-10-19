<?php
session_start();

if (!isset($_SESSION["login"])) {
    header("Location: ../login.php");
    exit();
}

include "../koneksi.php";

$error = "";

if (isset($_POST["simpan"])) {
    $nama_menu = mysqli_real_escape_string($koneksi, $_POST["nama_menu"]);
    $harga = (int) $_POST["harga"];
    $harga = (int) $_POST["harga"];

    $deskripsi = mysqli_real_escape_string($koneksi, $_POST["deskripsi"]);
    $foto = "";

    if (!empty($_FILES["foto"]["name"])) {
        $foto = basename($_FILES["foto"]["name"]);
        $folder = "../gambar/";

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        if (!move_uploaded_file($_FILES["foto"]["tmp_name"], $folder . $foto)) {
            $error = "Foto gagal diunggah.";
        }
    }

    if ($error == "") {
        $query = mysqli_query(
            $koneksi,
            "INSERT INTO menu (nama_menu, harga, deskripsi, foto)
             VALUES ('$nama_menu', '$harga', '$deskripsi', '$foto')"
        );

        if ($query) {
            header("Location: menu.php");
            exit();
        } else {
            $error = "Gagal menambahkan menu: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tambah Menu Kafe</title>
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
    max-width: 500px;
    margin: 35px auto;
    padding: 25px;
    background: white;
    border-radius: 18px;
    box-shadow: 0 8px 20px rgba(0,0,0,.1);
}
label {
    display: block;
    margin: 15px 0 7px;
    font-weight: bold;
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
button {
    width: 100%;
    margin-top: 22px;
    padding: 13px;
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
    margin-top: 18px;
    color: #8b5e3c;
    text-decoration: none;
}
.error {
    color: #b00020;
    margin: 12px 0;
}
</style>
</head>
<body>

<div class="header">
    <h1>☕ TAMBAH MENU</h1>
    <p>Tambahkan menu baru ke kafe</p>
</div>

<div class="container">
    <h2>FORM TAMBAH MENU</h2>

    <?php if ($error != "") { ?>
        <p class="error"><?= htmlspecialchars($error); ?></p>
    <?php } ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Nama Menu</label>
        <input type="text" name="nama_menu" required>

        <label>Harga</label>
        <input type="number" name="harga" min="0" step="1" required>

        <label>Deskripsi</label>
        <textarea name="deskripsi" required></textarea>

        <label>Foto Menu</label>
        <input type="file" name="foto" accept="image/*">

        <button type="submit" name="simpan">SIMPAN MENU</button>
    </form>

    <a href="menu.php" class="kembali">← Kembali ke Menu</a>
</div>

</body>
</html>