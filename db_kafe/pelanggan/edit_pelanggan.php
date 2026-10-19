<?php
session_start();

if (!isset($_SESSION["login"])) {
    header("Location: ../login.php");
    exit();
}

include "../koneksi.php";

$id = $_GET["id"];

$data = mysqli_query(
    $koneksi,
    "SELECT * FROM pelanggan WHERE id_pelanggan = '$id'"
);

$pelanggan = mysqli_fetch_assoc($data);

if (isset($_POST["ubah"])) {

    $nama_pelanggan = $_POST["nama_pelanggan"];

    $query = mysqli_query(
        $koneksi,
        "UPDATE pelanggan
         SET nama_pelanggan = '$nama_pelanggan'
         WHERE id_pelanggan = '$id'"
    );

    if ($query) {
        header("Location: pelanggan.php");
        exit();
    } else {
        echo "Data gagal diubah: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Pelanggan</title>

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
            width: 500px;
            max-width: 90%;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #d8c1aa;
            border-radius: 8px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: #8b5e3c;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #6f452c;
        }

        .kembali {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #8b5e3c;
            text-decoration: none;
        }
    </style>
</head>

<body>

<div class="header">
    <h1>☕ PELANGGAN KAFE</h1>
    <p>Edit Data Pelanggan</p>
</div>

<div class="container">

    <h2>EDIT PELANGGAN</h2>

    <form method="POST">

        <label>Nama Pelanggan</label>

        <input
            type="text"
            name="nama_pelanggan"
            value="<?= $pelanggan['nama_pelanggan']; ?>"
            required>

        <button type="submit" name="ubah">
            SIMPAN PERUBAHAN
        </button>

    </form>

    <a href="pelanggan.php" class="kembali">
        ← Kembali ke Data Pelanggan
    </a>

</div>

</body>
</html>