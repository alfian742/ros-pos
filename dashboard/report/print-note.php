<?php
// Menyertakan konfigurasi
include('../../config/config.php');

// Memulai sesi
session_start();

// Cek Auth
if (!isset($_SESSION['email']) || !isset($_SESSION['level'])) {
    $_SESSION['error'] = "Maaf, Anda harus masuk terlebih dahulu";
    echo "<script>window.location.href = '" . base_url('auth/login.php') . "';</script>";
    exit();
} elseif ($_SESSION['level'] !== 'Admin' && $_SESSION['level'] !== 'Cashier') {
    echo "<script>window.location.href = '" . base_url('error/403.php') . "';</script>";
    exit();
}

$id_pembayaran = $_GET['payment-id'];

$sql_pembayaran = "SELECT pesanan.*, 
                        pembayaran.id_pembayaran AS id_pembayaran, 
                        pembayaran.status AS status_pembayaran, 
                        pembayaran.metode_pembayaran AS metode_pembayaran,
                        pembayaran.jumlah_pembayaran AS jumlah_pembayaran,
                        users.nama_lengkap AS nama_lengkap,
                        users.level AS level
                FROM pesanan
                INNER JOIN pembayaran ON pesanan.id_pesanan = pembayaran.id_pesanan
                INNER JOIN users ON pesanan.id_user = users.id_user
                WHERE pembayaran.id_pembayaran = '$id_pembayaran'";

$query_pembayaran = mysqli_query($koneksi, $sql_pembayaran);

if (!mysqli_num_rows($query_pembayaran) > 0) {
    $_SESSION['error'] = "Data tidak ditemukan";
    echo "<script>window.location.href = '" . base_url('dashboard/report/show.php') . "';</script>";
    exit();
} else {
    $data_pembayaran = mysqli_fetch_array($query_pembayaran);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pan & Co.</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?= base_url('assets/uploads/static/logo-square.jpg'); ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/uploads/static/logo-square.jpg'); ?>">

    <!-- Vendor CSS Files -->
    <link href="<?= base_url('assets/dashboard/vendor/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">

    <style>
        section {
            font-size: 12px !important;
        }

        table {
            font-size: 12px !important;
        }
    </style>
</head>

<body>
    <main class="d-flex justify-content-center">
        <div class="container-fluid">
            <div class="pb-2 mb-4 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <img src="<?= base_url('assets/uploads/static/logo.jpg'); ?>" alt="Logo" height="48">
                    <div class="d-flex flex-column gap-1">
                        <h4 class="mb-0 text-uppercase">Pan & Co.</h4>
                        <small class="fst-italic">Lombok Epicentrum Mall GF 29-30</small>
                    </div>
                </div>
            </div>

            <h4 class="text-center mb-4">Nota Pembelian</h4>

            <section>
                <div class="d-flex justify-content-between gap-2 mb-4">
                    <span class="fw-bold">ID Pembayaran: <?= $data_pembayaran['id_pembayaran']; ?></span>
                    <span class="fw-bold">Tanggal: <?= date('d-m-Y H:i', strtotime($data_pembayaran['tanggal'])); ?></span>
                </div>

                <table class="table table-sm table-bordered">
                    <thead class="table-light text-center align-middle">
                        <tr>
                            <th scope="col">No.</th>
                            <th scope="col">Nama Menu</th>
                            <th scope="col">Harga</th>
                            <th scope="col">Jumlah</th>
                            <th scope="col">Sub Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $total_pembayaran = 0;

                        $sql_keranjang = "SELECT * FROM keranjang INNER JOIN menu 
                                      ON keranjang.id_menu=menu.id_menu
                                      WHERE keranjang.id_pesanan='{$data_pembayaran['id_pesanan']}'
                                      ORDER BY keranjang.id_keranjang ASC";
                        $query_keranjang = mysqli_query($koneksi, $sql_keranjang);

                        while ($data_keranjang = mysqli_fetch_array($query_keranjang)):
                        ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= $data_keranjang['nama_menu']; ?></td>
                                <td class="text-end"><?= 'Rp ' . number_format($data_keranjang['harga'], 0, ',', '.'); ?></td>
                                <td class="text-end"><?= $data_keranjang['jumlah']; ?></td>
                                <td class="text-end">
                                    <?php
                                    $subtotal = $data_keranjang['harga'] * $data_keranjang['jumlah'];

                                    echo 'Rp ' . number_format($subtotal, 0, ',', '.');
                                    ?>
                                </td>
                            </tr>
                        <?php endwhile ?>
                    </tbody>
                </table>

                <div class="row justify-content-end">
                    <div class="col-6">
                        <div class="table-responsive">
                            <table class="table table-borderless table-nowrap">
                                <tr>
                                    <td style="width: 8rem;">Tipe Pesanan</td>
                                    <td style="width: 1rem;">:</td>
                                    <th><?= $data_pembayaran['tipe_pesanan']; ?></th>
                                </tr>
                                <tr>
                                    <td>Nomor Meja</td>
                                    <td>:</td>
                                    <th><?= (!empty($data_pembayaran['nomor_meja'])) ? $data_pembayaran['nomor_meja'] : '-'; ?></th>
                                </tr>
                                <tr>
                                    <td>Metode Pembayaran</td>
                                    <td>:</td>
                                    <th><?= $data_pembayaran['metode_pembayaran']; ?></th>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="table-responsive">
                            <table class="table table-borderless table-nowrap">
                                <tr>
                                    <td style="width: 8rem;">Total</td>
                                    <td style="width: 1rem;">:</td>
                                    <th><?= 'Rp ' . number_format($data_pembayaran['total_pembayaran'], 0, ',', '.'); ?></th>
                                </tr>
                                <tr>
                                    <td>Jumlah Bayar</td>
                                    <td>:</td>
                                    <th><?= 'Rp ' . number_format($data_pembayaran['jumlah_pembayaran'], 0, ',', '.'); ?></th>
                                </tr>
                                <tr>
                                    <td>Kembali</td>
                                    <td>:</td>
                                    <th><?= 'Rp ' . number_format(($data_pembayaran['jumlah_pembayaran'] - $data_pembayaran['total_pembayaran']), 0, ',', '.'); ?></th>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="d-flex flex-column text-center">
                            <span class="mb-5">Mataram, <?= date('d-m-Y'); ?></span>

                            <span class="mb-0 fw-bold"><?= $_SESSION['nama_lengkap']; ?></span>
                            <small><?= $_SESSION['level'] === 'Cashier' ? 'Kasir' : 'Admin'; ?></sma>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- Vendor JS Files -->
    <script src="<?= base_url('assets/dashboard/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
</body>

</html>