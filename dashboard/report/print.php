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

if (isset($_POST['periode_awal']) && isset($_POST['periode_akhir'])) {
    $periode_awal = $_POST['periode_awal'] . " 00:00:00";
    $periode_akhir = $_POST['periode_akhir'] . " 23:59:59";


    $sqlPrintLaporan = "SELECT pesanan.*, 
                                pembayaran.id_pembayaran AS id_pembayaran, 
                                pembayaran.status AS status_pembayaran, 
                                pembayaran.metode_pembayaran AS metode_pembayaran,
                                users.nama_lengkap AS nama_lengkap,
                                users.level AS level
                        FROM pesanan
                        INNER JOIN pembayaran ON pesanan.id_pesanan = pembayaran.id_pesanan
                        INNER JOIN users ON pesanan.id_user = users.id_user
                        WHERE pesanan.status = 'Completed'
                        AND pembayaran.status = 'Paid'
                        AND pembayaran.tanggal BETWEEN '$periode_awal' AND '$periode_akhir'
                        ORDER BY pembayaran.tanggal ASC";
    $queryPrintLaporan = mysqli_query($koneksi, $sqlPrintLaporan);
} else {
    $_SESSION['warning'] = "Silakan masukan kembali periode.";
    echo "<script>window.location.href = '" . base_url('admin/report/show.php') . "';</script>";
    exit();
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

            <div class="mb-4 text-center">
                <h4>Laporan Penjualan</h4>
                <span>Priode: <?= date('d-m-Y', strtotime($periode_awal)); ?> s/d <?= date('d-m-Y', strtotime($periode_akhir)); ?></span>
            </div>

            <table class="table table-sm table-bordered">
                <thead class="table-light text-center align-middle">
                    <tr>
                        <th>No.</th>
                        <th>Tanggal</th>
                        <th>ID Pembayaran</th>
                        <th>Nama</th>
                        <th>Role</th>
                        <th>Tipe Pesanan</th>
                        <th>Status Pesanan</th>
                        <th>Status Pembayaran</th>
                        <th>Metode Pembayaran</th>
                        <th>Sub Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    $no = 1;

                    if (mysqli_num_rows($queryPrintLaporan) > 0):
                    ?>
                        <?php while ($data = mysqli_fetch_array($queryPrintLaporan)) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d-m-Y H:i', strtotime($data['tanggal'])); ?></td>
                                <td><?= $data['id_pembayaran'] ?></td>
                                <td><?= $data['nama_lengkap'] ?></td>
                                <td>
                                    <?php
                                    if ($data['level'] === 'User') {
                                        echo 'Pelanggan';
                                    } elseif ($data['level'] === 'Cashier') {
                                        echo 'Kasir';
                                    } else {
                                        echo 'Admin';
                                    }
                                    ?>
                                </td>
                                <td><?= $data['tipe_pesanan'] ?></td>
                                <td class="text-center">
                                    <?= $data['status']; ?>
                                </td>
                                <td class="text-center">
                                    <?= $data['status_pembayaran']; ?>
                                </td>
                                <td class="text-center">
                                    <?= $data['metode_pembayaran']; ?>
                                </td>
                                <td class="text-end">
                                    <?php
                                    $total_pembayaran = $data['total_pembayaran'];

                                    echo 'Rp ' . number_format($total_pembayaran, 0, ',', '.')
                                    ?>
                                </td>
                            </tr>
                            <?php $total += $total_pembayaran; ?>
                        <?php endwhile ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="10" class="text-center">Tidak Ada Data</td>
                        </tr>
                    <?php endif ?>
                    <tr>
                        <th colspan="9" class="text-center">Total</th>
                        <th class="text-end"><?= 'Rp ' . number_format($total, 0, ',', '.'); ?></th>
                    </tr>
                </tbody>
            </table>

            <div class="d-flex justify-content-between gap-2">
                <small>Dicetak oleh: <span class="fw-bold"><?= $_SESSION['nama_lengkap'] . ' [' . (($_SESSION['level'] === 'Cashier') ? 'Kasir' : 'Admin') . ']' ?></span></small>
                <small>Dicetak pada: <span class="fw-bold"><?= date('d-m-Y H:i'); ?></span></small>
            </div>
        </div>
    </main>

    <!-- Vendor JS Files -->
    <script src="<?= base_url('assets/dashboard/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
</body>

</html>