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
} elseif ($_SESSION['level'] !== 'Admin') {
    echo "<script>window.location.href = '" . base_url('error/403.php') . "';</script>";
    exit();
}

// Judul Halaman
$title = 'Galeri';

ob_start(); // Start output buffering 
?>

<div class="pagetitle">
    <div class="d-flex justify-content-between align-items-center gap-2 mb-4">
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

                    <div class="d-flex justify-content-end mb-4">
                        <a href="<?= base_url('dashboard/gallery/create.php'); ?>" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-square-fill me-1"></i> Tambah
                        </a>
                    </div>

                    <!-- Table with stripped rows -->
                    <table class="table table-hover datatable">
                        <thead class="table-light">
                            <tr>
                                <th>Gambar</th>
                                <th>Tanggal Buat</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM galeri ORDER BY tanggal_buat DESC";

                            $query = mysqli_query($koneksi, $sql);

                            while ($data = mysqli_fetch_array($query)) :
                            ?>
                                <tr>
                                    <td><img src="<?= get_image_url(base_url('assets/uploads/gallery/' . $data['gambar'])); ?>" class="rounded-circle" style="width: 80px; height: 80px;" alt="<?= $data['nama_menu']; ?>"></td>
                                    <td><?= date('d-m-Y H:i', strtotime($data['tanggal_buat'])); ?></td>
                                    <td><?= $data['keterangan'] ?></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <!-- Edit button -->
                                            <a href="<?= base_url('dashboard/gallery/edit.php?gallery-id=' . $data['id_galeri']); ?>" class="btn btn-sm btn-primary">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>

                                            <!-- Button trigger delete modal -->
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $data['id_galeri']; ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Delete modal -->
                                        <div class="modal fade" id="deleteModal<?= $data['id_galeri']; ?>" tabindex="-1" aria-labelledby="deleteModal<?= $data['id_galeri']; ?>Label" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0">
                                                    <div class="modal-header border-0">
                                                        <h5 class="modal-title" id="deleteModal<?= $data['id_galeri']; ?>Label">Hapus Data</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Hapus item dari galeri?
                                                    </div>
                                                    <div class="modal-footer border-0">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                                                        <a href="<?= base_url('dashboard/gallery/action.php?delete-gallery=' . $data['id_galeri']); ?>" class="btn btn-danger text-white px-4">Ya</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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