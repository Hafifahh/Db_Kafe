<?php

session_start();

if (!isset($_SESSION["login"])) {
    header("Location: ../login.php");
    exit();
}

include "../koneksi.php";


$id = $_GET["id"];


/* Ambil data detail */

$data = mysqli_query(
    $koneksi,
    "SELECT * FROM detail_tranksaksi
     WHERE id_detail = '$id'"
);

if (!$data) {
    die("Gagal mengambil data: " . mysqli_error($koneksi));
}

$detail = mysqli_fetch_assoc($data);

if (!$detail) {
    die("Data detail tranksaksi tidak ditemukan.");
}


/* Ambil data menu */

$data_menu = mysqli_query(
    $koneksi,
    "SELECT * FROM menu"
);

if (!$data_menu) {
    die("Gagal mengambil menu: " . mysqli_error($koneksi));
}


/* Proses edit */

if (isset($_POST["simpan"])) {

    $id_menu = $_POST["id_menu"];
    $jumlah = $_POST["jumlah"];


    /* Ambil harga menu */

    $cek_menu = mysqli_query(
        $koneksi,
        "SELECT * FROM menu
         WHERE id_menu = '$id_menu'"
    );

    if (!$cek_menu) {
        die("Gagal mengambil harga menu: " . mysqli_error($koneksi));
    }

    $menu = mysqli_fetch_row($cek_menu);

    $harga = $menu[2];

    $subtotal = $harga * $jumlah;


    /* Update detail */

    $query = mysqli_query(
        $koneksi,
        "UPDATE detail_tranksaksi
         SET
            id_menu = '$id_menu',
            jumlah = '$jumlah',
            subtotal = '$subtotal'
         WHERE id_detail = '$id'"
    );


    if ($query) {

        header("Location: detail_tranksaksi.php");
        exit();

    } else {

        die(
            "Gagal mengubah detail: "
            . mysqli_error($koneksi)
        );
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Edit Detail Tranksaksi</title>

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

        .container {
            width: 500px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.10);
        }

        h2 {
            text-align: center;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input,
        select {
            width: 100%;
            padding: 12px;
            box-sizing: border-box;
            border: 1px solid #d8c2ad;
            border-radius: 8px;
        }

        input:disabled {
            background: #eee;
        }

        button {
            width: 100%;
            margin-top: 25px;
            padding: 13px;
            border: none;
            border-radius: 8px;
            background: #8b5e3c;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #70452b;
        }

        .kembali {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #8b5e3c;
            text-decoration: none;
            font-weight: bold;
        }

    </style>

</head>

<body>


<div class="header">

    <h1>☕ EDIT DETAIL TRANKSAKSI</h1>

    <p>Ubah rincian pesanan</p>

</div>


<div class="container">

    <h2>FORM EDIT DETAIL</h2>


    <form method="POST">


        <label>ID Detail</label>

        <input
            type="number"
            value="<?= $detail["id_detail"]; ?>"
            disabled
        >


        <label>ID Tranksaksi</label>

        <input
            type="number"
            value="<?= $detail["id_tranksaksi"]; ?>"
            disabled
        >


        <label>Menu</label>

        <select name="id_menu" required>

            <?php while ($menu = mysqli_fetch_row($data_menu)) { ?>

                <option
                    value="<?= $menu[0]; ?>"
                    <?= ($menu[0] == $detail["id_menu"]) ? "selected" : ""; ?>
                >

                    <?= $menu[1]; ?>

                    -
                    
                    Rp <?= number_format(
                        $menu[2],
                        0,
                        ',',
                        '.'
                    ); ?>

                </option>

            <?php } ?>

        </select>


        <label>Jumlah</label>

        <input
            type="number"
            name="jumlah"
            min="1"
            value="<?= $detail["jumlah"]; ?>"
            required
        >


        <button type="submit" name="simpan">

            SIMPAN PERUBAHAN

        </button>


    </form>


    <a href="detail_tranksaksi.php" class="kembali">

        ← Kembali ke Detail Tranksaksi

    </a>


</div>


</body>

</html>