<?php
include('../config/config.php');

session_start();

$limit = 6;
$offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
$kategori = isset($_POST['kategori']) ? $_POST['kategori'] : '';

$sql_menu = "SELECT * FROM menu WHERE kategori='$kategori' ORDER BY tanggal_buat DESC LIMIT $limit OFFSET $offset";
$query_menu = mysqli_query($koneksi, $sql_menu);

if (mysqli_num_rows($query_menu) > 0) :
?>
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
<?php
else :
    echo 'done'; // Kirimkan 'done' jika tidak ada lagi data yang tersisa
endif;
?>