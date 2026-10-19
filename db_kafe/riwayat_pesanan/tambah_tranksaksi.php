<?php
session_start();
include "../koneksi.php";

if (!isset($_SESSION["login"])) {
    header("Location: ../login.php");
    exit;
}

$nama_pelanggan = "";
$daftar_menu = [];
$total = 0;
$error = "";

function rupiah($angka) {
    return "Rp" . number_format((float)$angka, 0, ",", ".");
}

/* Menampilkan ringkasan pesanan dan menyimpan pilihan sementara */
if (isset($_POST["pesan_banyak"])) {
    $nama_pelanggan = trim($_POST["nama_pelanggan"] ?? "");
    $pilih = $_POST["pilih"] ?? [];
    $jumlah_pilihan = $_POST["jumlah"] ?? [];

    if ($nama_pelanggan == "" || empty($pilih)) {
        $error = "Masukkan nama pelanggan dan pilih minimal satu menu.";
    } else {
        $draft_items = [];

        foreach ($pilih as $id_menu) {
            $id_menu = (int)$id_menu;
            $jumlah = max(1, (int)($jumlah_pilihan[$id_menu] ?? 1));

            $draft_items[$id_menu] = $jumlah;

            $query = mysqli_query(
                $koneksi,
                "SELECT id_menu, nama_menu, harga
                 FROM menu WHERE id_menu = $id_menu"
            );

            if ($query && mysqli_num_rows($query) > 0) {
                $menu = mysqli_fetch_assoc($query);
                $subtotal = (float)$menu["harga"] * $jumlah;

                $menu["jumlah"] = $jumlah;
                $menu["subtotal"] = $subtotal;

                $daftar_menu[] = $menu;
                $total += $subtotal;
            }
        }

        if (!empty($daftar_menu)) {
            $_SESSION["draft_pesanan"] = [
                "nama_pelanggan" => $nama_pelanggan,
                "items" => $draft_items
            ];
        } else {
            $error = "Menu yang dipilih tidak ditemukan.";
        }
    }
}

/* Konfirmasi dan simpan pesanan */
if (isset($_POST["konfirmasi"])) {
    $nama_pelanggan = trim($_POST["nama_pelanggan"] ?? "");
    $id_menu_post = $_POST["id_menu"] ?? [];
    $jumlah_post = $_POST["jumlah"] ?? [];

    /* Pertahankan draft jika penyimpanan gagal */
    $draft_items = [];

    foreach ($id_menu_post as $index => $id_menu) {
        $id_menu = (int)$id_menu;
        $jumlah = max(1, (int)($jumlah_post[$index] ?? 1));
        $draft_items[$id_menu] = $jumlah;
    }

    $_SESSION["draft_pesanan"] = [
        "nama_pelanggan" => $nama_pelanggan,
        "items" => $draft_items
    ];

    if ($nama_pelanggan == "" || empty($id_menu_post)) {
        $error = "Data pesanan tidak lengkap.";
    } else {
        mysqli_begin_transaction($koneksi);

        try {
            $nama_aman = mysqli_real_escape_string(
                $koneksi,
                $nama_pelanggan
            );

            $cari = mysqli_query(
                $koneksi,
                "SELECT id_pelanggan FROM pelanggan
                 WHERE nama_pelanggan = '$nama_aman' LIMIT 1"
            );

            if (!$cari) {
                throw new Exception(mysqli_error($koneksi));
            }

            if (mysqli_num_rows($cari) > 0) {
                $pelanggan = mysqli_fetch_assoc($cari);
                $id_pelanggan = (int)$pelanggan["id_pelanggan"];
            } else {
                $tambah = mysqli_query(
                    $koneksi,
                    "INSERT INTO pelanggan (nama_pelanggan, no_telp)
                     VALUES ('$nama_aman', '')"
                );

                if (!$tambah) {
                    throw new Exception(mysqli_error($koneksi));
                }

                $id_pelanggan = mysqli_insert_id($koneksi);
            }

            $total = 0;
            $items = [];

            foreach ($id_menu_post as $index => $id_menu) {
                $id_menu = (int)$id_menu;
                $jumlah = max(1, (int)($jumlah_post[$index] ?? 1));

                $qmenu = mysqli_query(
                    $koneksi,
                    "SELECT id_menu, harga FROM menu
                     WHERE id_menu = $id_menu"
                );

                if (!$qmenu || mysqli_num_rows($qmenu) == 0) {
                    throw new Exception("Menu tidak ditemukan.");
                }

                $data_menu = mysqli_fetch_assoc($qmenu);
                $subtotal = (float)$data_menu["harga"] * $jumlah;
                $total += $subtotal;

                $items[] = [
                    "id_menu" => $id_menu,
                    "jumlah" => $jumlah,
                    "subtotal" => $subtotal
                ];
            }

            $tanggal = date("Y-m-d H:i:s");

            $simpan = mysqli_query(
                $koneksi,
                "INSERT INTO riwayat_pesanan
                 (id_pelanggan, tanggal, total)
                 VALUES ($id_pelanggan, '$tanggal', $total)"
            );

            if (!$simpan) {
                throw new Exception(mysqli_error($koneksi));
            }

            $id_riwayat = mysqli_insert_id($koneksi);

            foreach ($items as $item) {
                $id_menu = $item["id_menu"];
                $jumlah = $item["jumlah"];
                $subtotal = $item["subtotal"];

                $detail = mysqli_query(
                    $koneksi,
                    "INSERT INTO rekap
                     (id_riwayat_pesanan, id_menu, jumlah, subtotal)
                     VALUES ($id_riwayat, $id_menu, $jumlah, $subtotal)"
                );

                if (!$detail) {
                    throw new Exception(mysqli_error($koneksi));
                }
            }

            mysqli_commit($koneksi);

            /* Hapus draft setelah pesanan berhasil disimpan */
            unset($_SESSION["draft_pesanan"]);

            header("Location: ../pesanan_berhasil.php");
            exit;

        } catch (Exception $e) {
            mysqli_rollback($koneksi);
            $error = "Pesanan gagal disimpan: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ringkasan Pesanan Kafe</title>

<style>
* { box-sizing: border-box; }

body {
    margin: 0;
    padding: 30px 14px;
    font-family: Arial, sans-serif;
    background: #f8eee3;
    color: #4b2c1c;
}

.struk {
    max-width: 620px;
    margin: 15px auto;
    padding: 20px;
    background: #f5e8da;
    border-radius: 25px;
    box-shadow: 0 8px 25px rgba(75, 44, 28, 0.10);
}

.kertas {
    padding: 28px;
    background: #fffdf9;
    border: 2px solid #d8c5b2;
    border-radius: 20px;
}

.kepala {
    text-align: center;
    padding-bottom: 22px;
    border-bottom: 1px solid #d8c5b2;
}

.kepala h1 {
    margin: 0;
    font-size: 27px;
    color: #4b2c1c;
}

.kepala p {
    margin: 10px 0 0;
    color: #8b6a51;
}

.nama-pelanggan {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    padding: 20px 0;
    border-bottom: 1px solid #d8c5b2;
    font-size: 16px;
}

.nama-pelanggan strong:last-child {
    overflow-wrap: anywhere;
    text-align: right;
}

.item {
    padding: 14px 0;
    border-bottom: 1px dashed #d8c5b2;
}

.nama-menu {
    margin-bottom: 7px;
    font-size: 17px;
    font-weight: bold;
}

.rincian {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    color: #76563e;
    line-height: 1.5;
}

.rincian span:last-child {
    font-weight: bold;
    text-align: right;
    white-space: nowrap;
}

.jumlah-total {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    padding: 20px 0;
    margin-top: 5px;
    border-bottom: 1px solid #d8c5b2;
    color: #76563e;
}

.total-harga {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding-top: 20px;
    font-weight: bold;
}

.total-harga span:first-child { font-size: 16px; }

.total-harga span:last-child {
    font-size: 25px;
    color: #8b5e3c;
    text-align: right;
}

button, .kembali {
    display: block;
    width: 100%;
    margin-top: 15px;
    padding: 15px;
    border: none;
    border-radius: 10px;
    background: #8b5e3c;
    color: white;
    text-align: center;
    text-decoration: none;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
}

button:hover, .kembali:hover { background: #6e452c; }

.error {
    max-width: 620px;
    margin: 0 auto 15px;
    padding: 14px;
    border-radius: 10px;
    background: #f8d7da;
    color: #842029;
}

.catatan {
    text-align: center;
    margin-top: 25px;
    color: #9b806a;
    font-size: 12px;
    line-height: 1.8;
}

.kosong {
    padding: 20px 0;
    text-align: center;
    color: #8b6a51;
}

@media (max-width: 480px) {
    body { padding: 15px 9px; }
    .struk { padding: 10px; border-radius: 18px; }
    .kertas { padding: 17px; border-radius: 14px; }
    .kepala h1 { font-size: 22px; }
    .nama-pelanggan { font-size: 14px; }
    .nama-menu { font-size: 15px; }
    .rincian, .jumlah-total { font-size: 14px; }
    .total-harga span:last-child { font-size: 21px; }
}
</style>
</head>

<body>

<?php if ($error != "") { ?>
    <div class="error">
        <?= htmlspecialchars($error) ?>
    </div>
<?php } ?>

<div class="struk">
<div class="kertas">

    <div class="kepala">
        <h1>RINGKASAN PESANAN</h1>
        <p>Periksa pesanan sebelum dikonfirmasi</p>
    </div>

    <?php if (!empty($daftar_menu)) { ?>

        <div class="nama-pelanggan">
            <span>Nama pelanggan</span>
            <strong><?= htmlspecialchars($nama_pelanggan) ?></strong>
        </div>

        <?php foreach ($daftar_menu as $menu) { ?>
            <div class="item">
                <div class="nama-menu">
                    <?= htmlspecialchars($menu["nama_menu"]) ?>
                </div>

                <div class="rincian">
                    <span>
                        <?= (int)$menu["jumlah"] ?>
                        ×
                        <?= rupiah($menu["harga"]) ?>
                    </span>
                    <span><?= rupiah($menu["subtotal"]) ?></span>
                </div>
            </div>
        <?php } ?>

        <?php
        $total_item = 0;
        foreach ($daftar_menu as $menu) {
            $total_item += (int)$menu["jumlah"];
        }
        ?>

        <div class="jumlah-total">
            <span>Total jumlah</span>
            <strong><?= $total_item ?> item</strong>
        </div>

        <div class="total-harga">
            <span>TOTAL HARGA</span>
            <span><?= rupiah($total) ?></span>
        </div>

        <form method="POST">
            <input type="hidden" name="nama_pelanggan"
                   value="<?= htmlspecialchars($nama_pelanggan, ENT_QUOTES) ?>">

            <?php foreach ($daftar_menu as $menu) { ?>
                <input type="hidden" name="id_menu[]"
                       value="<?= (int)$menu["id_menu"] ?>">

                <input type="hidden" name="jumlah[]"
                       value="<?= (int)$menu["jumlah"] ?>">
            <?php } ?>

            <button type="submit" name="konfirmasi">
                KONFIRMASI PESANAN
            </button>
        </form>

        <a class="kembali" href="../menu/pesan.php">
            KEMBALI KE MENU
        </a>

        <div class="catatan">
            ───── Terima kasih ─────<br>
            Semoga harimu menyenangkan ☕
        </div>

    <?php } else { ?>

        <div class="kosong">
            <?php if ($error == "") { ?>
                Belum ada menu yang dipilih.
            <?php } else { ?>
                Periksa kembali data pesananmu.
            <?php } ?>
        </div>

        <a class="kembali" href="../menu/pesan.php">
            KEMBALI KE MENU
        </a>

    <?php } ?>

</div>
</div>

</body>
</html>