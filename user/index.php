<?php
// Menyertakan konfigurasi
include('../config/config.php');

// Memulai sesi
session_start();

// Judul Halaman
$title = 'Beranda';

ob_start(); // Start output buffering 
?>

<!-- Hero Start -->
<div class="container-fluid py-5 hero-header">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-md-12 col-lg-7 order-1 order-lg-0">
                <h4 class="mb-3 text-secondary text-center text-lg-start">Dibuat Sesuai Pesanan</h4>
                <h1 class="mb-5 display-3 text-primary text-center text-lg-start">Pancake <br> Selembut Awan</h1>
                <div class="d-flex justify-content-center justify-content-lg-start">
                    <a href="#new-products" class="btn btn-primary border-2 border-secondary py-3 px-4 rounded-pill text-white">Pesan Sekarang</a>
                </div>
            </div>
            <div class="col-md-12 col-lg-5 order-0 order-lg-1">
                <div id="carouselId" class="carousel slide position-relative" data-bs-ride="carousel">
                    <div class="carousel-inner" role="listbox">
                        <div class="carousel-item active rounded">
                            <img src="<?= base_url('assets/user/img/slider-img-1.jpg'); ?>" class="img-fluid w-100 h-100 bg-secondary rounded"
                                alt="First slide">
                        </div>
                        <div class="carousel-item rounded">
                            <img src="<?= base_url('assets/user/img/slider-img-2.jpg'); ?>" class="img-fluid w-100 h-100 rounded" alt="Second slide">
                        </div>
                        <div class="carousel-item rounded">
                            <img src="<?= base_url('assets/user/img/slider-img-3.jpg'); ?>" class="img-fluid w-100 h-100 rounded" alt="Third slide">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselId"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselId"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Hero End -->

<!-- Featurs Section Start -->
<div class="container-fluid featurs py-5 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="featurs-item text-center rounded bg-light p-4 h-100">
                    <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                        <i class="fas fa-hand-holding-usd fa-3x text-white"></i>
                    </div>
                    <div class="featurs-content text-center">
                        <h5>Pembelian Gratis</h5>
                        <p class="mb-0">Gratis apabila tidak diberikan struk belanjaan</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="featurs-item text-center rounded bg-light p-4 h-100">
                    <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                        <i class="fas fa-mosque fa-3x text-white"></i>
                    </div>
                    <div class="featurs-content text-center">
                        <h5>Halal</h5>
                        <p class="mb-0">Tidak mengandung daging babi atau produk turunannya</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="featurs-item text-center rounded bg-light p-4 h-100">
                    <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                        <i class="fas fa-exchange-alt fa-3x text-white"></i>
                    </div>
                    <div class="featurs-content text-center">
                        <h5>Kepuasan</h5>
                        <p class="mb-0">Garansi untuk pelanggan apabila terjadi kesalahan penyajian</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="featurs-item text-center rounded bg-light p-4 h-100">
                    <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                        <i class="fa fa-phone-alt fa-3x text-white"></i>
                    </div>
                    <div class="featurs-content text-center">
                        <h5>Dukungan 24/7</h5>
                        <p class="mb-0">Dukungan pelanggan cepat dan andal</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Featurs Section End -->

<!-- Menus Start-->
<div class="container-fluid fruite py-5" id="new-products">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center gap-2 mb-5">
            <h2>Menu Terbaru</h2>
            <a href="<?= base_url('user/menu.php'); ?>" class="btn btn-outline-secondary rounded-pill">Lihat Semua <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
        <div class="row g-4">
            <div class="col-lg-12">
                <div class="row justify-content-center g-4">
                    <?php
                    $sql_menu = "SELECT * FROM menu ORDER BY tanggal_buat DESC LIMIT 3";
                    $query_menu = mysqli_query($koneksi, $sql_menu);

                    while ($data_menu = mysqli_fetch_array($query_menu)):
                    ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="rounded position-relative fruite-item border border-secondary d-flex flex-column justify-content-between h-100">
                                <div class="fruite-img">
                                    <img src="<?= get_image_url(base_url('assets/uploads/menu/' . $data_menu['gambar'])); ?>" class="w-100 rounded-top" height="350" style="object-fit: cover;" alt="<?= $data_menu['nama_menu']; ?>">
                                </div>
                                <div class="text-white bg-secondary px-3 py-1 rounded position-absolute" style="top: 10px; left: 10px;"><?= $data_menu['kategori']; ?></div>
                                <div class="px-4 pt-4 pb-2 text-center">
                                    <h4><?= $data_menu['nama_menu']; ?></h4>
                                    <p><?= $data_menu['deskripsi']; ?></p>
                                </div>
                                <div class="px-4 pt-2 pb-4 rounded-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="text-dark fs-5 fw-bold mb-0"><?= 'Rp ' . number_format($data_menu['harga'], 0, ',', '.'); ?></p>
                                        <?php if ($data_menu['status'] === 'Tersedia'): ?>
                                            <?php if (isset($_SESSION['email']) && isset($_SESSION['level'])): ?>
                                                <?php
                                                $sql_tambah_keranjang = "SELECT * FROM keranjang 
                                                                         WHERE id_user='{$_SESSION['id_user']}' 
                                                                         AND id_menu='{$data_menu['id_menu']}' 
                                                                         AND status='Belum Dipesan'";
                                                $query_tambah_keranjang = mysqli_query($koneksi, $sql_tambah_keranjang);

                                                if (mysqli_num_rows($query_tambah_keranjang) == 0):
                                                ?>
                                                    <a href="<?= base_url('user/action.php?add-to-cart=' . $data_menu['id_menu']); ?>" class="btn border border-secondary rounded-pill px-3 text-primary">
                                                        <i class="fa fa-shopping-bag me-2 text-primary"></i> Tambah
                                                    </a>
                                                <?php else: ?>
                                                    <h4 class="text-end text-success mb-0"><i class="fas fa-circle-check"></i></h4>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <!-- Button trigger menu modal -->
                                                <button type="button" class="btn border border-secondary rounded-pill px-3 text-primary" data-bs-toggle="modal" data-bs-target="#menuModal">
                                                    <i class="fa fa-shopping-bag me-2 text-primary"></i> Tambah
                                                </button>
                                            <?php endif ?>
                                        <?php else: ?>
                                            <p class="text-end text-danger mb-0"><?= $data_menu['status']; ?></p>
                                        <?php endif ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Menus End-->

<!-- Galleries Start-->
<div class="container-fluid vesitable py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center gap-2">
            <h2>Galeri</h2>
            <a href="<?= base_url('user/gallery.php'); ?>" class="btn btn-outline-secondary rounded-pill">Lihat Semua <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
        <div class="owl-carousel vegetable-carousel justify-content-center">
            <?php
            $sql_galeri = "SELECT * FROM galeri ORDER BY tanggal_buat DESC LIMIT 4";
            $query_galeri = mysqli_query($koneksi, $sql_galeri);

            while ($data_galeri = mysqli_fetch_array($query_galeri)):
            ?>
                <div class="border border-secondary rounded position-relative vesitable-item d-flex flex-column justify-content-between h-100">
                    <div class="vesitable-img">
                        <img src="<?= get_image_url(base_url('assets/uploads/gallery/' . $data_galeri['gambar'])); ?>" class="w-100 rounded-top" height="350" style="object-fit: cover;" alt="<?= $data_galeri['keterangan']; ?>">
                    </div>
                    <div class="p-4 rounded-bottom">
                        <h6 class="text-center"><?= $data_galeri['keterangan']; ?></h6>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>
<!-- Galleries End -->

<?php
$content = ob_get_clean(); // Get content and clean buffer

// Menyertakan layout
include('layout.php');
?>