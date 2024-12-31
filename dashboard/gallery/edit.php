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

$id_galeri = $_GET['gallery-id'];
$sql_galeri = "SELECT * FROM galeri WHERE id_galeri = '$id_galeri'";
$query_galeri = mysqli_query($koneksi, $sql_galeri);

if (!mysqli_num_rows($query_galeri) > 0) {
    $_SESSION['error'] = "Data tidak ditemukan";
    echo "<script>window.location.href = '" . base_url('dashboard/gallery/show.php') . "';</script>";
    exit();
} else {
    $data_galeri = mysqli_fetch_array($query_galeri);
}

// Judul Halaman
$title = 'Edit Galeri';

ob_start(); // Start output buffering 
?>

<div class="pagetitle">
    <div class="d-flex justify-content-start align-items-center gap-2 mb-4">
        <a href="<?= base_url('dashboard/gallery/show.php'); ?>" class="btn btn-outline-secondary rounded-circle border-0 btn-back"><i class="bi bi-arrow-left"></i></a>
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

                    <?php
                    // Nilai form dari session atau data dari database
                    $keterangan = isset($_SESSION['form_data']['keterangan']) ? htmlspecialchars($_SESSION['form_data']['keterangan']) : $data_galeri['keterangan'];
                    $gambar = get_image_url(base_url('assets/uploads/gallery/' . $data_galeri['gambar']));
                    ?>

                    <form method="post" action="<?= base_url('dashboard/gallery/action.php?update-gallery=' . $id_galeri); ?>" enctype="multipart/form-data">
                        <div class="row g-4 mb-4">
                            <div class="col-md-8">
                                <label for="keterangan" class="form-label">Keterangan Singkat</label>
                                <textarea class="form-control <?= isset($_SESSION['errors']['keterangan']) ? 'is-invalid' : '' ?>" id="keterangan" name="keterangan" rows="10"><?= $keterangan; ?></textarea>
                                <div class="invalid-feedback">
                                    <?= isset($_SESSION['errors']['keterangan']) ? $_SESSION['errors']['keterangan'] : '' ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="gambar" class="form-label d-flex justify-content-center">
                                    <img id="preview-img" src="<?= $gambar; ?>" alt="Pratinjau Gambar" class="rounded object-fit-cover cursor-pointer img-preview" height="250" width="250">
                                </label>
                                <div class="text-center">
                                    <span>Klik untuk unggah gambar</span><br>
                                    <span id="error-img" class="text-danger"></span>
                                </div>
                                <input type="file" class="form-control d-none <?= isset($_SESSION['errors']['gambar']) ? 'is-invalid' : '' ?>" id="gambar" name="gambar" accept="image/jpg, image/jpeg, image/png, image/webp">
                                <div class="invalid-feedback text-center">
                                    <?= isset($_SESSION['errors']['gambar']) ? $_SESSION['errors']['gambar'] : '' ?>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary text-white px-3">Simpan</button>
                    </form>

                    <?php
                    // Reset errors dan form_data setelah submit
                    unset($_SESSION['errors']);
                    unset($_SESSION['form_data']);
                    ?>
                </div>
            </div>

        </div>
    </div>
</section>

<script>
    document.getElementById('gambar').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const errorImg = document.getElementById('error-img');
        const previewImg = document.getElementById('preview-img');

        // Reset error message
        errorImg.textContent = '';

        // Check if file exists
        if (!file) {
            return;
        }

        // Validate file type
        const validExtensions = ['image/jpg', 'image/jpeg', 'image/png', 'image/webp'];
        if (!validExtensions.includes(file.type)) {
            previewImg.src = '<?= base_url('assets/uploads/static/no-image-placeholder.png'); ?>';
            errorImg.textContent = 'Format gambar tidak valid. Hanya JPG, JPEG, PNG, dan WEBP yang diperbolehkan.';
            return;
        }

        // Validate file size
        const maxSize = 1 * 1024 * 1024; // 1 MB
        if (file.size > maxSize) {
            previewImg.src = '<?= base_url('assets/uploads/static/no-image-placeholder.png'); ?>';
            errorImg.textContent = 'Ukuran file harus maksimal 1 MB.';
            return;
        }

        // Create a URL for the image and set it as the src of the preview
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
</script>

<?php
$content = ob_get_clean(); // Get content and clean buffer

// Menyertakan layout
include('../layout.php');
?>