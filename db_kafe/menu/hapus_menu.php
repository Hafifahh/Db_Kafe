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
    "DELETE FROM menu WHERE id_menu = '$id'"
);

if ($query) {
    header("Location: menu.php");
    exit();
} else {
    echo "Data gagal dihapus: " . mysqli_error($koneksi);
}
?>