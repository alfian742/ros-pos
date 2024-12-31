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
$title = 'Laporan';

ob_start(); // Start output buffering 
?>

<div class="pagetitle">
    <div class="d-flex justify-content-between align-items-center gap-2 mb-4">
        <h1 class="mb-0 fw-bold"><?= $title ?></h1>
        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#printModal"><i class="bi bi-printer me-1"></i> Cetak Laporan</button>
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
                                <th>ID Pembayaran</th>
                                <th>Nama</th>
                                <th>Role</th>
                                <th>Tipe Pesanan</th>
                                <th>Status Pesanan</th>
                                <th>Status Pembayaran</th>
                                <th>Metode Pembayaran</th>
                                <th>Total Pembayaran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;

                            $sql = "SELECT pesanan.*,
                                            pembayaran.id_pembayaran AS id_pembayaran, 
                                            pembayaran.status AS status_pembayaran, 
                                            pembayaran.metode_pembayaran AS metode_pembayaran,
                                            users.nama_lengkap AS nama_lengkap,
                                            users.level AS level
                                    FROM pesanan
                                    INNER JOIN pembayaran ON pesanan.id_pesanan = pembayaran.id_pesanan
                                    INNER JOIN users ON pesanan.id_user = users.id_user
                                    WHERE pesanan.status = 'Completed' AND pembayaran.status = 'Paid'
                                    ORDER BY pembayaran.tanggal DESC";

                            $query = mysqli_query($koneksi, $sql);

                            while ($data = mysqli_fetch_array($query)) :
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= date('d-m-Y H:i', strtotime($data['tanggal'])); ?></td>
                                    <td><?= $data['id_pembayaran'] ?></td>
                                    <td><?= $data['nama_lengkap'] ?></td>
                                    <td>
                                        <?php if ($data['level'] === 'User'): ?>
                                            <span class="badge bg-info">Pelanggan</span>
                                        <?php elseif ($data['level'] === 'Cashier'): ?>
                                            <span class="badge bg-secondary">Kasir</span>
                                        <?php else: ?>
                                            <span class="badge bg-primary">Admin</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $data['tipe_pesanan'] ?></td>
                                    <td class="text-center">
                                        <?php if ($data['status'] == 'Pending'): ?>
                                            <span class="badge bg-warning">Pending</span>
                                        <?php elseif ($data['status'] == 'Confirmed'): ?>
                                            <span class="badge bg-primary">Confirmed</span>
                                        <?php elseif ($data['status'] == 'In Progress'): ?>
                                            <span class="badge bg-info">In Progress</span>
                                        <?php elseif ($data['status'] == 'Completed'): ?>
                                            <span class="badge bg-success">Completed</span>
                                        <?php elseif ($data['status'] == 'Cancelled'): ?>
                                            <span class="badge bg-danger">Cancelled</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($data['status_pembayaran'] == 'Unpaid'): ?>
                                            <span class="badge bg-danger">Unpaid</span>
                                        <?php elseif ($data['status_pembayaran'] == 'Paid'): ?>
                                            <span class="badge bg-primary">Paid</span>
                                        <?php elseif ($data['status_pembayaran'] == 'Rejected'): ?>
                                            <span class="badge bg-danger">Rejected</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $data['metode_pembayaran'] ?></td>
                                    <td><?= 'Rp ' . number_format($data['total_pembayaran'], 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <a href="<?= base_url('dashboard/report/detail.php?payment-id=' . $data['id_pembayaran']); ?>" class="btn btn-sm btn-light">
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

<!-- Print Modal -->
<div class="modal fade" id="printModal" tabindex="-1" aria-labelledby="printModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <!-- Ubah action sesuai url -->
        <form method="post" action="<?= base_url('dashboard/report/print.php'); ?>" target="printFrame" class="modal-content border-0">
            <div class="modal-header border-0">
                <h1 class="modal-title fs-5" id="printModalLabel">Cetak Laporan</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="periode_awal">Periode Awal</label>
                            <input class="form-control" name="periode_awal" id="periode_awal" type="date" required />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="periode_akhir">Periode Akhir</label>
                            <input class="form-control" name="periode_akhir" id="periode_akhir" type="date" required />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-success" onclick="printPage()"><i class="bi bi-printer me-1"></i> Cetak</button>
            </div>
        </form>
    </div>
</div>

<!-- iframe untuk print laporan -->
<iframe id="printFrame" name="printFrame" class="d-none"></iframe>

<?php
$content = ob_get_clean(); // Get content and clean buffer

// Menyertakan layout
include('../layout.php');
?>