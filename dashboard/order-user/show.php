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

// Judul Halaman
$title = 'Pesanan Pelanggan';

ob_start(); // Start output buffering 
?>

<div class="pagetitle">
    <div class="d-flex justify-content-between align-items-center gap-2 mb-4">
        <h1 class="mb-0 fw-bold"><?= $title ?></h1>

        <div class="d-flex gap-2">
            <a href="<?= base_url('dashboard/order-user/show.php'); ?>" class="btn btn-sm px-lg-3 btn-dark">Semua</a>

            <?php
            $status = $_GET['status'] ?? '';
            $sql_status_pesanan = "SELECT DISTINCT status FROM pesanan
                                   INNER JOIN users ON pesanan.id_user = users.id_user
                                   WHERE users.level = 'User' 
                                   ORDER BY pesanan.status ASC";
            $query_status_pesanan = mysqli_query($koneksi, $sql_status_pesanan);

            while ($data_status_pesanan = mysqli_fetch_array($query_status_pesanan)):
            ?>
                <a href="<?= base_url('dashboard/order-user/status.php?status=' . urlencode($data_status_pesanan['status'])); ?>" class="btn btn-sm px-lg-3 btn-light border-secondary">
                    <?= $data_status_pesanan['status']; ?>
                </a>
            <?php endwhile; ?>
        </div>
    </div>
</div><!-- End Page Title -->

<section class="section">
    <div class="row">
        <div class="col-lg-12">

            <div class="card">
                <div class="card-body pt-4">
                    <!-- Alerts -->
                    <?php include('../components/alerts.php') ?>

                    <!-- Table with stripped rows -->
                    <table class="table table-hover datatable">
                        <thead class="table-light">
                            <tr>
                                <th>No.</th>
                                <th>Tanggal</th>
                                <th>ID Pesanan</th>
                                <th>Nama Pelanggan</th>
                                <th>Tipe Pesanan</th>
                                <th>Total Belanja</th>
                                <th>Status Pembayaran</th>
                                <th>Status Pesanan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;

                            $sql = "SELECT pesanan.*, 
                                            pembayaran.status AS status_pembayaran, 
                                            pembayaran.metode_pembayaran AS metode_pembayaran,
                                            users.nama_lengkap AS nama_lengkap
                                    FROM pesanan
                                    INNER JOIN pembayaran ON pesanan.id_pesanan = pembayaran.id_pesanan
                                    INNER JOIN users ON pesanan.id_user = users.id_user
                                    WHERE users.level = 'User'
                                    ORDER BY pesanan.tanggal DESC";

                            $query = mysqli_query($koneksi, $sql);

                            while ($data = mysqli_fetch_array($query)) :
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= date('d-m-Y H:i', strtotime($data['tanggal'])); ?></td>
                                    <td><?= $data['id_pesanan'] ?></td>
                                    <td><?= $data['nama_lengkap'] ?></td>
                                    <td><?= $data['tipe_pesanan'] ?></td>
                                    <td><?= 'Rp ' . number_format($data['total_pembayaran'], 0, ',', '.') ?></td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <?php if ($data['status_pembayaran'] == 'Unpaid'): ?>
                                                <span class="badge bg-danger">Unpaid</span>
                                            <?php elseif ($data['status_pembayaran'] == 'Paid'): ?>
                                                <span class="badge bg-primary">Paid</span>
                                            <?php elseif ($data['status_pembayaran'] == 'Rejected'): ?>
                                                <span class="badge bg-danger">Rejected</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <form method="post" action="<?= base_url('dashboard/order-user/action.php?update-order-status=' . $data['id_pesanan'] . '&current-status=All'); ?>">
                                            <div class="input-group">
                                                <select class="form-select form-select-sm" id="status" name="status" required>
                                                    <option value="Pending" <?= ($data['status'] === 'Pending') ? 'selected' : ''; ?> class="text-warning">Pending</option>
                                                    <option value="Confirmed" <?= ($data['status'] === 'Confirmed') ? 'selected' : ''; ?> class="text-primary">Confirmed</option>
                                                    <option value="In Progress" <?= ($data['status'] === 'In Progress') ? 'selected' : ''; ?> class="text-info">In Progres</option>
                                                    <option value="Completed" <?= ($data['status'] === 'Completed') ? 'selected' : ''; ?> class="text-success">Completed</option>
                                                    <option value="Cancelled" <?= ($data['status'] === 'Cancelled') ? 'selected' : ''; ?> class="text-danger">Cancelled</option>
                                                </select>
                                                <button class="btn btn-sm btn-dark" type="submit">Pilih</button>
                                            </div>
                                        </form>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('dashboard/order-user/detail.php?order-id=' . $data['id_pesanan']); ?>" class="btn btn-sm btn-light">
                                            <i class="bi bi-info-circle me-1"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile ?>
                        </tbody>
                    </table>
                    <!-- End Table with stripped rows -->

                </div>
            </div>

        </div>
    </div>
</section>

<?php
$content = ob_get_clean(); // Get content and clean buffer

// Menyertakan layout
include('../layout.php');
?>