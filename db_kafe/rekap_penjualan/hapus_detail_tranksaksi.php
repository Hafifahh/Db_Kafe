<?php

session_start();

if (!isset($_SESSION["login"])) {
    header("Location: ../login.php");
    exit();
}

include "../koneksi.php";


$id = $_GET["id"];


$query = mysqli_query(
    $koneksi,
    "DELETE FROM detail_tranksaksi
     WHERE id_detail = '$id'"
);


if ($query) {

    header("Location: detail_tranksaksi.php");
    exit();

} else {

    die(
        "Gagal menghapus detail transaksi: "
        . mysqli_error($koneksi)
    );
}

?>