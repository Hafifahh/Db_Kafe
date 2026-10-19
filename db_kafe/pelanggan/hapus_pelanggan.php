<?php

session_start();

if (!isset($_SESSION["login"])) {
    header("Location: ../login.php");
    exit();
}

include "../koneksi.php";

$id = $_GET["id"];

/* Cek apakah pelanggan sudah punya transaksi */
$cek = mysqli_query(
    $koneksi,
    "SELECT id_tranksaksi
     FROM tranksaksi
     WHERE id_pelanggan = '$id'"
);

if (!$cek) {
    die("Gagal mengecek transaksi: " . mysqli_error($koneksi));
}

/* Kalau sudah punya transaksi */
if (mysqli_num_rows($cek) > 0) {

    echo "<script>
        alert('Pelanggan tidak dapat dihapus karena sudah memiliki riwayat transaksi.');
        window.location='pelanggan.php';
    </script>";

    exit();
}

/* Kalau belum pernah transaksi, boleh dihapus */
$query = mysqli_query(
    $koneksi,
    "DELETE FROM pelanggan
     WHERE id_pelanggan = '$id'"
);

if ($query) {

    header("Location: pelanggan.php");
    exit();

} else {

    echo "Data gagal dihapus: " . mysqli_error($koneksi);
}

?>