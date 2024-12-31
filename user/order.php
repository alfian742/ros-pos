<?php
// Menyertakan konfigurasi
include('../config/config.php');

// Memulai sesi
session_start();

// Cek auth
if (!isset($_SESSION['email']) && !isset($_SESSION['level'])) {
    $_SESSION['error'] = "Maaf, Anda harus masuk terlebih dahulu";
    echo "<script>window.location.href = '" . base_url('auth/login.php') . "';</script>";
    exit();
} elseif ($_SESSION['level'] === 'Admin' || $_SESSION['level'] === 'Cashier') {
    echo "<script>window.location.href = '" . base_url('dashboard/order-cashier/show.php') . "';</script>";
    exit();
}

// Judul Halaman
$title = 'Riwayat Pesanan';

ob_start(); // Start output buffering 
?>

<!-- Single Page Header start -->
<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6"><?= $title; ?></h1>
</div>
<!-- Single Page Header End -->

<!-- Order Page Start -->
<div class="container-fluid py-5">
    <div class="container">
        <?php
        $id_user = $_SESSION['id_user'];

        $sql_pesanan = "SELECT pesanan.*, pembayaran.status AS status_pembayaran
                        FROM pesanan
                        INNER JOIN pembayaran ON pesanan.id_pesanan = pembayaran.id_pesanan
                        WHERE pesanan.id_user = '$id_user'
                        ORDER BY pesanan.tanggal DESC";
        $query_pesanan = mysqli_query($koneksi, $sql_pesanan);

        if (mysqli_num_rows($query_pesanan) > 0):
        ?>
            <div class="table-responsive">
                <table class="table table-lg table-hover w-100" id="myTable" style="white-space: nowrap !important;">
                    <thead>
                        <tr>
                            <th scope="col" class="text-start">Tanggal</th>
                            <th scope="col">ID Pesanan</th>
                            <th scope="col">Total Belanja</th>
                            <th scope="col">Tipe Pesanan</th>
                            <th scope="col">Status Pesanan</th>
                            <th scope="col">Status Pembayaran</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($data_pesanan = mysqli_fetch_array($query_pesanan)): ?>
                            <tr class="align-middle">
                                <td class="text-start">
                                    <?= date('d-m-Y H:i', strtotime($data_pesanan['tanggal'])); ?>
                                </td>
                                <td>
                                    <?= $data_pesanan['id_pesanan']; ?>
                                </td>
                                <td>
                                    <?= 'Rp ' . number_format($data_pesanan['total_pembayaran'], 0, ',', '.'); ?>
                                </td>
                                <td>
                                    <?= $data_pesanan['tipe_pesanan']; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($data_pesanan['status'] == 'Pending'): ?>
                                        <span class="badge bg-warning">Pending</span>
                                    <?php elseif ($data_pesanan['status'] == 'Confirmed'): ?>
                                        <span class="badge bg-success">Confirmed</span>
                                    <?php elseif ($data_pesanan['status'] == 'In Progress'): ?>
                                        <span class="badge bg-info">In Progress</span>
                                    <?php elseif ($data_pesanan['status'] == 'Completed'): ?>
                                        <span class="badge bg-primary">Completed</span>
                                    <?php elseif ($data_pesanan['status'] == 'Cancelled'): ?>
                                        <span class="badge bg-danger">Cancelled</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($data_pesanan['status_pembayaran'] == 'Unpaid'): ?>
                                        <span class="badge bg-danger">Unpaid</span>
                                    <?php elseif ($data_pesanan['status_pembayaran'] == 'Paid'): ?>
                                        <span class="badge bg-primary">Paid</span>
                                    <?php elseif ($data_pesanan['status_pembayaran'] == 'Rejected'): ?>
                                        <span class="badge bg-danger">Rejected</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="<?= base_url('user/order-detail.php?order-id=' . $data_pesanan['id_pesanan']); ?>" class="btn btn-light">
                                            <i class="fas fa-info-circle text-dark me-2"></i> Detail
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile ?>
                    </tbody>
                </table>
            </div>
        <?php
        else:
            include('components/empty-cart.php');
        endif;
        ?>
    </div>
</div>
<!-- Order Page End -->

<?php
$content = ob_get_clean(); // Get content and clean buffer

// Menyertakan layout
include('layout.php');
?>