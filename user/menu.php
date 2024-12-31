<?php
// Menyertakan konfigurasi
include('../config/config.php');

// Memulai sesi
session_start();

// Judul Halaman
$title = 'Menu';

ob_start(); // Start output buffering 
?>

<!-- Single Page Header start -->
<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6"><?= $title; ?></h1>
</div>
<!-- Single Page Header End -->

<!-- Menu Start -->
<div class="container-fluid fruite py-5">
    <div class="container">
        <div class="row g-4 mt-4">
            <div class="col-lg-12">
                <div class="row g-4 align-items-center mb-5">
                    <div class="col-lg-4">
                        <form class="input-group w-100 mx-auto d-flex" method="post" action="">
                            <input type="search" class="form-control form-control-lg border-secondary" placeholder="Cari Menu" name="keyword" aria-describedby="search-icon-1">
                            <button id="search-icon-1" class="input-group-text btn btn-secondary text-white px-4" type="submit" name="search"><i class="fa fa-search"></i></button>
                        </form>
                    </div>
                    <div class="col-lg-8">
                        <div class="d-flex justify-content-center justify-content-lg-end gap-2">
                            <a href="<?= base_url('user/menu.php'); ?>" class="btn btn-sm rounded-pill px-lg-3 <?= ($title === 'Menu') ? 'btn-secondary text-white' : 'btn-light'; ?>">Semua</a>

                            <?php
                            $kategori = $_GET['category'] ?? '';
                            $sql_kategori = "SELECT DISTINCT kategori FROM menu";
                            $query_kategori = mysqli_query($koneksi, $sql_kategori);

                            while ($data_kategori = mysqli_fetch_array($query_kategori)):
                            ?>
                                <a href="<?= base_url('user/category.php?category=' . urlencode($data_kategori['kategori'])); ?>" class="btn btn-sm rounded-pill px-lg-3 <?= ($kategori === $data_kategori['kategori']) ? 'btn-secondary text-white' : 'btn-light'; ?>">
                                    <?= $data_kategori['kategori']; ?>
                                </a>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <?php
                $limit = 6;
                $offset = 0;
                $keyword = $_POST['keyword'] ?? '';
                $search_query = "";

                if ($keyword) {
                    $search_query = "WHERE nama_menu LIKE '%$keyword%' OR deskripsi LIKE '%$keyword%' OR kategori LIKE '%$keyword%'";
                }

                $sql_menu = "SELECT * FROM menu $search_query ORDER BY tanggal_buat DESC LIMIT $limit OFFSET $offset";
                $query_menu = mysqli_query($koneksi, $sql_menu);

                if (mysqli_num_rows($query_menu) > 0):
                ?>
                    <div class="row g-4 justify-content-center" id="menu-container">
                        <?php while ($data_menu = mysqli_fetch_array($query_menu)): ?>
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

                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-center align-items-center mt-5">
                                <button id="load-more" class="btn btn-lg btn-secondary text-white">Lihat Lainnya</button>
                            </div>
                        </div>
                    </div>
                <?php
                else:
                    include('components/data-not-found.php');
                endif
                ?>
            </div>
        </div>
    </div>
</div>
<!-- Menu End -->

<script src="<?= base_url('assets/user/lib/jquery/jquery.min.js'); ?>"></script>
<script>
    $(document).ready(function() {
        let offset = 6; // Sudah ada 6 data awal

        $("#load-more").click(function() {
            $.ajax({
                url: '<?= base_url('user/menu-load-more.php'); ?>',
                method: 'POST',
                data: {
                    offset: offset,
                    keyword: '<?= $keyword ?>' // Mengirimkan keyword jika ada pencarian
                },
                success: function(response) {
                    if (response.trim() === 'done') {
                        $("#load-more").hide();
                        $("#menu-container").append('<h5 class="fw-semibold text-center mt-5 mb-0">Semua Data Telah Ditampilkan</h5>');
                    } else {
                        $("#menu-container").append(response);
                        offset += 6;
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error loading more data: ", error);
                }
            });
        });
    });
</script>

<?php
$content = ob_get_clean(); // Get content and clean buffer

// Menyertakan layout
include('layout.php');
?>