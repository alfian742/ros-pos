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


// Judul Halaman
$title = 'Detail Pembayaran';

ob_start(); // Start output buffering 
?>

<div class="pagetitle">
    <div class="d-flex justify-content-between align-items-center gap-2 mb-4">
        <div class="d-flex justify-content-start align-items-center">
            <a href="<?= base_url('dashboard/report/show.php'); ?>" class="btn btn-outline-secondary rounded-circle border-0 btn-back"><i class="bi bi-arrow-left"></i></a>
            <h1 class="mb-0 fw-bold"><?= $title ?></h1>
        </div>
        <button type="button" onclick="printNota('<?= base_url('dashboard/report/print-note.php?payment-id=' . $id_pembayaran); ?>');" class="btn btn-sm btn-success"><i class="bi bi-printer me-1"></i> Cetak Nota</button>
    </div>
</div><!-- End Page Title -->

<section class="section">
    <div class="row">
        <div class="col-lg-12">

            <div class="card">
                <div class="card-body pt-4">
                    <!-- Alerts -->
                    <?php include('../components/alerts.php') ?>

                    <table class="table table-hover" style="white-space: nowrap !important;">
                        <thead>
                            <tr>
                                <th scope="col">Produk</th>
                                <th scope="col">Nama Menu</th>
                                <th scope="col">Harga</th>
                                <th scope="col">Jumlah</th>
                                <th scope="col">Sub Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql_keranjang = "SELECT * FROM keranjang INNER JOIN menu 
                                              ON keranjang.id_menu=menu.id_menu
                                              WHERE keranjang.id_pesanan='{$data_pembayaran['id_pesanan']}'
                                              ORDER BY keranjang.id_keranjang ASC";
                            $query_keranjang = mysqli_query($koneksi, $sql_keranjang);

                            while ($data_keranjang = mysqli_fetch_array($query_keranjang)):
                            ?>
                                <tr>
                                    <th scope="row">
                                        <div class="d-flex align-items-center">
                                            <img src="<?= get_image_url(base_url('assets/uploads/menu/' . $data_keranjang['gambar'])); ?>" class="rounded-circle" style="width: 80px; height: 80px;" alt="<?= $data_keranjang['nama_menu']; ?>">
                                        </div>
                                    </th>
                                    <td>
                                        <p class="mb-0 mt-4"><?= $data_keranjang['nama_menu']; ?></p>
                                    </td>
                                    <td>
                                        <p class="mb-0 mt-4"><?= 'Rp ' . number_format($data_keranjang['harga'], 0, ',', '.'); ?></p>
                                    </td>
                                    <td>
                                        <p class="mb-0 mt-4"><?= $data_keranjang['jumlah']; ?></p>
                                    </td>
                                    <td>
                                        <p class="mb-0 mt-4">
                                            <?php
                                            $subtotal = $data_keranjang['harga'] * $data_keranjang['jumlah'];

                                            echo 'Rp ' . number_format($subtotal, 0, ',', '.');
                                            ?>
                                        </p>
                                    </td>
                                </tr>
                            <?php endwhile ?>
                        </tbody>
                    </table>

                    <!-- Bagian untuk total belanja -->
                    <div class="row g-4 justify-content-end mt-5">
                        <div class="col-lg-5">
                            <div class="bg-light rounded">
                                <div class="p-4">
                                    <h1 class="display-6 mb-4"><span class="fw-normal">Belanja</span></h1>

                                    <div class="d-flex flex-column border-bottom pb-4 mb-4">
                                        <!-- Menampilkan total belanja -->
                                        <h5 class="mb-2">Nama:</h5>

                                        <div class="d-flex justify-content-between align-items-center gap-2">
                                            <h5 class="mb-0 fw-bold">
                                                <?= $data_pembayaran['nama_lengkap']; ?>
                                            </h5>
                                            <?php if ($data_pembayaran['level'] === 'User'): ?>
                                                <span class="badge bg-info">Pelanggan</span>
                                            <?php elseif ($data_pembayaran['level'] === 'Cashier'): ?>
                                                <span class="badge bg-secondary">Kasir</span>
                                            <?php else: ?>
                                                <span class="badge bg-primary">Admin</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between mb-4">
                                        <!-- Menampilkan total belanja -->
                                        <h5 class="mb-0 fw-bold me-4">ID Pembayaran:</h5>
                                        <p class="mb-0 fw-bold">
                                            <?= $data_pembayaran['id_pembayaran']; ?>
                                        </p>
                                    </div>

                                    <div class="d-flex justify-content-between mb-4">
                                        <!-- Menampilkan total belanja -->
                                        <h5 class="mb-0 fw-bold me-4">Total Belanja:</h5>
                                        <p class="mb-0 fw-bold">
                                            <?= 'Rp ' . number_format($data_pembayaran['total_pembayaran'], 0, ',', '.'); ?>
                                        </p>
                                    </div>

                                    <div class="d-flex justify-content-between mb-4">
                                        <!-- Menampilkan total belanja -->
                                        <h5 class="mb-0 fw-bold me-4">Jumlah Bayar:</h5>
                                        <p class="mb-0 fw-bold">
                                            <?= 'Rp ' . number_format($data_pembayaran['jumlah_pembayaran'], 0, ',', '.'); ?>
                                        </p>
                                    </div>

                                    <div class="d-flex justify-content-between border-bottom pb-4 mb-4">
                                        <!-- Menampilkan total belanja -->
                                        <h5 class="mb-0 fw-bold me-4">Kembali:</h5>
                                        <p class="mb-0 fw-bold">
                                            <?= 'Rp ' . number_format(($data_pembayaran['jumlah_pembayaran'] - $data_pembayaran['total_pembayaran']), 0, ',', '.'); ?>
                                        </p>
                                    </div>

                                    <div class="d-flex justify-content-between mb-4">
                                        <!-- Menampilkan status -->
                                        <h6 class="mb-0 me-4">ID Pesanan:</h6>
                                        <p class="mb-0"><?= $data_pembayaran['id_pesanan']; ?></p>
                                    </div>

                                    <div class="d-flex justify-content-between mb-4">
                                        <!-- Menampilkan status -->
                                        <h6 class="mb-0 me-4">Tanggal Pesan:</h6>
                                        <p class="mb-0"><?= date('d-m-Y H:i', strtotime($data_pembayaran['tanggal'])); ?></p>
                                    </div>

                                    <div class="d-flex justify-content-between mb-4">
                                        <!-- Menampilkan metode pembayaran -->
                                        <h6 class="mb-0 me-4">Tipe Pesanan:</h6>
                                        <p class="mb-0"><?= $data_pembayaran['tipe_pesanan']; ?></p>
                                    </div>

                                    <?php if ($data_pembayaran['tipe_pesanan'] !== 'Takeaway'): ?>
                                        <div class="d-flex justify-content-between mb-4">
                                            <!-- Menampilkan metode pembayaran -->
                                            <h6 class="mb-0 me-4">Nomor Meja:</h6>
                                            <p class="mb-0"><?= $data_pembayaran['nomor_meja']; ?></p>
                                        </div>
                                    <?php endif ?>

                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <!-- Menampilkan status -->
                                        <h6 class="mb-0 me-4">Status Pesanan:</h6>
                                        <p class="mb-0">
                                            <?php if ($data_pembayaran['status'] == 'Pending'): ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php elseif ($data_pembayaran['status'] == 'Confirmed'): ?>
                                                <span class="badge bg-primary">Confirmed</span>
                                            <?php elseif ($data_pembayaran['status'] == 'In Progress'): ?>
                                                <span class="badge bg-info">In Progress</span>
                                            <?php elseif ($data_pembayaran['status'] == 'Completed'): ?>
                                                <span class="badge bg-success">Completed</span>
                                            <?php elseif ($data_pembayaran['status'] == 'Cancelled'): ?>
                                                <span class="badge bg-danger">Cancelled</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>

                                    <?php if (!empty($data_pembayaran['metode_pembayaran'])): ?>
                                        <div class="d-flex justify-content-between mb-4">
                                            <!-- Menampilkan metode pembayaran -->
                                            <h6 class="mb-0 me-4">Metode Pembayaran:</h6>
                                            <p class="mb-0"><?= $data_pembayaran['metode_pembayaran']; ?></p>
                                        </div>
                                    <?php endif ?>

                                    <div class="d-flex justify-content-between">
                                        <!-- Menampilkan status pembayaran -->
                                        <h6 class="mb-0 me-4">Status Pembayaran:</h6>
                                        <p class="mb-0">
                                            <?php if ($data_pembayaran['status_pembayaran'] == 'Unpaid'): ?>
                                                <span class="badge bg-danger">Unpaid</span>
                                            <?php elseif ($data_pembayaran['status_pembayaran'] == 'Paid'): ?>
                                                <span class="badge bg-primary">Paid</span>
                                            <?php elseif ($data_pembayaran['status_pembayaran'] == 'Rejected'): ?>
                                                <span class="badge bg-danger">Rejected</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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