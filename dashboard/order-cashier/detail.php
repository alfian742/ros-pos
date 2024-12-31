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

$id_pesanan = $_GET['order-id'];

$sql_pesanan = "SELECT pesanan.*, 
                        pembayaran.status AS status_pembayaran, 
                        pembayaran.metode_pembayaran AS metode_pembayaran,
                        users.nama_lengkap AS nama_lengkap,
                        users.level AS level
                FROM pesanan
                INNER JOIN pembayaran ON pesanan.id_pesanan = pembayaran.id_pesanan
                INNER JOIN users ON pesanan.id_user = users.id_user
                WHERE users.level != 'User' AND pesanan.id_pesanan = '$id_pesanan'";

$query_pesanan = mysqli_query($koneksi, $sql_pesanan);

if (!mysqli_num_rows($query_pesanan) > 0) {
    $_SESSION['error'] = "Data tidak ditemukan";
    echo "<script>window.location.href = '" . base_url('dashboard/order-cashier/show.php') . "';</script>";
    exit();
} else {
    $data_pesanan = mysqli_fetch_array($query_pesanan);
}


// Judul Halaman
$title = 'Detail Pesanan Kasir';

ob_start(); // Start output buffering 
?>

<div class="pagetitle">
    <div class="d-flex justify-content-start align-items-center gap-2 mb-4">
        <a href="<?= base_url('dashboard/order-cashier/show.php'); ?>" class="btn btn-outline-secondary rounded-circle border-0 btn-back"><i class="bi bi-arrow-left"></i></a>
        <h1 class="mb-0 fw-bold"><?= $title ?></h1>
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
                                              WHERE keranjang.id_pesanan='{$data_pesanan['id_pesanan']}'
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
                        <div class="col-lg-4">
                            <div class="bg-light rounded">
                                <div class="p-4">
                                    <h1 class="display-6 mb-4"><span class="fw-normal">Belanja</span></h1>

                                    <div class="d-flex justify-content-between mb-4">
                                        <!-- Menampilkan total belanja -->
                                        <h5 class="mb-0 me-4">Total Belanja:</h5>
                                        <p class="mb-0">
                                            <?= 'Rp ' . number_format($data_pesanan['total_pembayaran'], 0, ',', '.'); ?>
                                        </p>
                                    </div>

                                    <div class="d-flex flex-column gap-2 border-bottom pb-4 mb-4">
                                        <!-- Menampilkan total belanja -->
                                        <h5 class="mb-0">Nama Admin/Kasir:</h5>
                                        <h5 class="mb-0 fw-bold">
                                            <?= $data_pesanan['nama_lengkap']; ?>
                                        </h5>
                                    </div>

                                    <div class="d-flex justify-content-between mb-4">
                                        <!-- Menampilkan status -->
                                        <h6 class="mb-0 me-4">ID Pesanan:</h6>
                                        <p class="mb-0"><?= $data_pesanan['id_pesanan']; ?></p>
                                    </div>

                                    <div class="d-flex justify-content-between mb-4">
                                        <!-- Menampilkan status -->
                                        <h6 class="mb-0 me-4">Tanggal Pesan:</h6>
                                        <p class="mb-0"><?= date('d-m-Y H:i', strtotime($data_pesanan['tanggal'])); ?></p>
                                    </div>

                                    <div class="d-flex justify-content-between mb-4">
                                        <!-- Menampilkan metode pembayaran -->
                                        <h6 class="mb-0 me-4">Tipe Pesanan:</h6>
                                        <p class="mb-0"><?= $data_pesanan['tipe_pesanan']; ?></p>
                                    </div>

                                    <?php if ($data_pesanan['tipe_pesanan'] !== 'Takeaway'): ?>
                                        <div class="d-flex justify-content-between mb-4">
                                            <!-- Menampilkan metode pembayaran -->
                                            <h6 class="mb-0 me-4">Nomor Meja:</h6>
                                            <p class="mb-0"><?= $data_pesanan['nomor_meja']; ?></p>
                                        </div>
                                    <?php endif ?>

                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <!-- Menampilkan status -->
                                        <h6 class="mb-0 me-4">Status Pesanan:</h6>
                                        <div class="mb-0 d-flex justify-content-end">
                                            <?php
                                            if ($data_pesanan['status'] == 'Pending') {
                                                $select_style = 'text-warning';
                                            } elseif ($data_pesanan['status'] == 'Confirmed') {
                                                $select_style = 'text-primary';
                                            } elseif ($data_pesanan['status'] == 'In Progress') {
                                                $select_style = 'text-info';
                                            } elseif ($data_pesanan['status'] == 'Completed') {
                                                $select_style = 'text-success';
                                            } elseif ($data_pesanan['status'] == 'Cancelled') {
                                                $select_style = 'text-danger';
                                            }
                                            ?>

                                            <form method="post" action="<?= base_url('dashboard/order-cashier/action.php?update-order-status=' . $data_pesanan['id_pesanan'] . '&current-status=' . urldecode('In Detail Page')); ?>">
                                                <div class="input-group">
                                                    <select class="form-select form-select-sm <?= $select_style; ?>" id="status" name="status" required>
                                                        <option value="Pending" <?= ($data_pesanan['status'] === 'Pending') ? 'selected' : ''; ?> class="text-warning">Pending</option>
                                                        <option value="Confirmed" <?= ($data_pesanan['status'] === 'Confirmed') ? 'selected' : ''; ?> class="text-primary">Confirmed</option>
                                                        <option value="In Progress" <?= ($data_pesanan['status'] === 'In Progress') ? 'selected' : ''; ?> class="text-info">In Progres</option>
                                                        <option value="Completed" <?= ($data_pesanan['status'] === 'Completed') ? 'selected' : ''; ?> class="text-success">Completed</option>
                                                        <option value="Cancelled" <?= ($data_pesanan['status'] === 'Cancelled') ? 'selected' : ''; ?> class="text-danger">Cancelled</option>
                                                    </select>
                                                    <button class="btn btn-sm btn-dark" type="submit">Pilih</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <?php if (!empty($data_pesanan['metode_pembayaran'])): ?>
                                        <div class="d-flex justify-content-between mb-4">
                                            <!-- Menampilkan metode pembayaran -->
                                            <h6 class="mb-0 me-4">Metode Pembayaran:</h6>
                                            <p class="mb-0"><?= $data_pesanan['metode_pembayaran']; ?></p>
                                        </div>
                                    <?php endif ?>

                                    <div class="d-flex justify-content-between">
                                        <!-- Menampilkan status pembayaran -->
                                        <h6 class="mb-0 me-4">Status Pembayaran:</h6>
                                        <p class="mb-0">
                                            <?php if ($data_pesanan['status_pembayaran'] == 'Unpaid'): ?>
                                                <span class="badge bg-danger">Unpaid</span>
                                            <?php elseif ($data_pesanan['status_pembayaran'] == 'Paid'): ?>
                                                <span class="badge bg-primary">Paid</span>
                                            <?php elseif ($data_pesanan['status_pembayaran'] == 'Rejected'): ?>
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