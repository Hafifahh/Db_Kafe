<?php

$koneksi = mysqli_connect(
    "127.0.0.1",
    "root",
    "",
    "db_kafe",
    3306
);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

?>