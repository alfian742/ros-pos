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
}

$id_user = $_SESSION['id_user'];
$id_pesanan = $_GET['order-id'];

if ($_SESSION['level'] === 'Admin' || $_SESSION['level'] === 'Cashier') {
    echo "<script>window.location.href = '" . base_url('dashboard/order-cashier/detail.php?order-id=' . $id_pesanan) . "';</script>";
    exit();
}

$sql_keranjang = "SELECT * FROM keranjang INNER JOIN menu 
                  ON keranjang.id_menu=menu.id_menu
                  WHERE keranjang.id_pesanan='$id_pesanan'
                  AND keranjang.id_user='$id_user' 
                  ORDER BY keranjang.id_keranjang ASC";
$query_keranjang = mysqli_query($koneksi, $sql_keranjang);

// Judul Halaman
$title = 'Detail Pesanan';

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
        <div class="d-flex justify-content-between gap-2 mb-4">
            <a href="<?= base_url('user/order.php'); ?>" class="btn btn-outline-secondary rounded-pill"><i class="fas fa-arrow-left me-2"></i> Riwayat Pesanan</a>
        </div>

        <?php if (mysqli_num_rows($query_keranjang) > 0): ?>
            <div class="table-responsive">
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
                        <?php while ($data_keranjang = mysqli_fetch_array($query_keranjang)): ?>
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
            </div>

            <!-- Bagian untuk total belanja -->
            <div class="row g-4 justify-content-end mt-5">
                <div class="col-8"></div>
                <div class="col-sm-8 col-md-7 col-lg-6 col-xl-4">
                    <div class="bg-light rounded">
                        <div class="p-4">
                            <h1 class="display-6 mb-4"><span class="fw-normal">Belanja</span></h1>
                            <?php
                            $sql_pesanan = "SELECT pesanan.*, 
                                                    pembayaran.id_pembayaran AS id_pembayaran, 
                                                    pembayaran.status AS status_pembayaran, 
                                                    pembayaran.metode_pembayaran AS metode_pembayaran,
                                                    pembayaran.jumlah_pembayaran AS jumlah_pembayaran
                                            FROM pesanan
                                            INNER JOIN pembayaran ON pesanan.id_pesanan = pembayaran.id_pesanan
                                            WHERE pesanan.id_pesanan = '$id_pesanan' AND pesanan.id_user = '$id_user'";
                            $query_pesanan = mysqli_query($koneksi, $sql_pesanan);

                            $data_pesanan = mysqli_fetch_array($query_pesanan);
                            ?>

                            <?php if ($data_pesanan['status_pembayaran'] === 'Paid'): ?>
                                <div class="d-flex justify-content-between mb-4">
                                    <!-- Menampilkan total belanja -->
                                    <h6 class="mb-0 fw-bold me-4">ID Pembayaran:</h6>
                                    <p class="mb-0 fw-bold">
                                        <?= $data_pesanan['id_pembayaran']; ?>
                                    </p>
                                </div>

                                <div class="d-flex justify-content-between mb-4">
                                    <!-- Menampilkan total belanja -->
                                    <h6 class="mb-0 fw-bold me-4">Total Belanja:</h6>
                                    <p class="mb-0 fw-bold">
                                        <?= 'Rp ' . number_format($data_pesanan['total_pembayaran'], 0, ',', '.'); ?>
                                    </p>
                                </div>

                                <div class="d-flex justify-content-between mb-4">
                                    <!-- Menampilkan total belanja -->
                                    <h6 class="mb-0 fw-bold me-4">Jumlah Bayar:</h6>
                                    <p class="mb-0 fw-bold">
                                        <?= 'Rp ' . number_format($data_pesanan['jumlah_pembayaran'], 0, ',', '.'); ?>
                                    </p>
                                </div>

                                <div class="d-flex justify-content-between border-bottom pb-4 mb-4">
                                    <!-- Menampilkan total belanja -->
                                    <h6 class="mb-0 fw-bold me-4">Kembali:</h6>
                                    <p class="mb-0 fw-bold">
                                        <?= 'Rp ' . number_format(($data_pesanan['jumlah_pembayaran'] - $data_pesanan['total_pembayaran']), 0, ',', '.'); ?>
                                    </p>
                                </div>
                            <?php else: ?>
                                <div class="d-flex justify-content-between border-bottom pb-4 mb-4">
                                    <!-- Menampilkan total belanja -->
                                    <h5 class="mb-0 me-4">Total Belanja:</h5>
                                    <p class="mb-0">
                                        <?= 'Rp ' . number_format($data_pesanan['total_pembayaran'], 0, ',', '.'); ?>
                                    </p>
                                </div>
                            <?php endif ?>

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

                            <div class="d-flex justify-content-between mb-4">
                                <!-- Menampilkan status -->
                                <h6 class="mb-0 me-4">Status Pesanan:</h6>
                                <p class="mb-0">
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
                                </p>
                            </div>

                            <?php if ($data_pesanan['status'] != 'Cancelled'): ?>
                                <?php if (!empty($data_pesanan['metode_pembayaran'])): ?>
                                    <div class="d-flex justify-content-between mb-4">
                                        <!-- Menampilkan metode pembayaran -->
                                        <h6 class="mb-0 me-4">Metode Pembayaran:</h6>
                                        <p class="mb-0"><?= $data_pesanan['metode_pembayaran']; ?></p>
                                    </div>
                                <?php endif ?>

                                <div class="d-flex justify-content-between mb-4">
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

                                <?php if ($data_pesanan['status_pembayaran'] == 'Unpaid'): ?>
                                    <p class="border-top pt-4 mt-4"><span class="text-danger">*</span> Silakan melakukan pembayaran di kasir. Terimakasih.</p>
                                <?php endif ?>
                            <?php endif ?>

                            <?php if ($data_pesanan['status'] == 'Pending'): ?>
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <a href="<?= base_url('user/order-edit.php?order-id=' . $data_pesanan['id_pesanan']); ?>" class="btn btn-secondary text-white rounded-pill w-100">Edit Pesanan</a>
                                    </div>
                                    <div class="col-12">
                                        <a href="#" class="btn btn-outline-danger rounded-pill w-100" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">Batalkan Pesanan</a>
                                    </div>
                                </div>

                                <!-- Logout modal -->
                                <div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0">
                                            <div class="modal-header border-0">
                                                <h5 class="modal-title" id="cancelOrderModalLabel">Batalkan Pesanan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Apakah Anda ingin membatalkan pesanan ini?
                                            </div>
                                            <div class="modal-footer border-0">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tidak</button>
                                                <a href="<?= base_url('user/action.php?cancel-order=' . $data_pesanan['id_pesanan']); ?>" class="btn btn-secondary text-white px-4">Ya</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
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