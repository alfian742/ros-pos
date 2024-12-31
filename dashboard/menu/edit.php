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

$id_menu = $_GET['menu-id'];
$sql_menu = "SELECT * FROM menu WHERE id_menu = '$id_menu'";
$query_menu = mysqli_query($koneksi, $sql_menu);

if (!mysqli_num_rows($query_menu) > 0) {
    $_SESSION['error'] = "Data tidak ditemukan";
    echo "<script>window.location.href = '" . base_url('dashboard/menu/show.php') . "';</script>";
    exit();
} else {
    $data_menu = mysqli_fetch_array($query_menu);
}

// Judul Halaman
$title = 'Edit Menu';

ob_start(); // Start output buffering 
?>

<div class="pagetitle">
    <div class="d-flex justify-content-start align-items-center gap-2 mb-4">
        <a href="<?= base_url('dashboard/menu/show.php'); ?>" class="btn btn-outline-secondary rounded-circle border-0 btn-back"><i class="bi bi-arrow-left"></i></a>
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
                    $nama_menu = isset($_SESSION['form_data']['nama_menu']) ? htmlspecialchars($_SESSION['form_data']['nama_menu']) : $data_menu['nama_menu'];
                    $harga = isset($_SESSION['form_data']['harga']) ? htmlspecialchars($_SESSION['form_data']['harga']) : $data_menu['harga'];
                    $kategori = isset($_SESSION['form_data']['kategori']) ? htmlspecialchars($_SESSION['form_data']['kategori']) : $data_menu['kategori'];
                    $status = isset($_SESSION['form_data']['status']) ? htmlspecialchars($_SESSION['form_data']['status']) : $data_menu['status'];
                    $deskripsi = isset($_SESSION['form_data']['deskripsi']) ? htmlspecialchars($_SESSION['form_data']['deskripsi']) : $data_menu['deskripsi'];
                    $gambar = get_image_url(base_url('assets/uploads/menu/' . $data_menu['gambar']));
                    ?>

                    <form method="post" action="<?= base_url('dashboard/menu/action.php?update-menu=' . $id_menu); ?>" enctype="multipart/form-data">
                        <div class="row g-4 mb-4">
                            <div class="col-md-8">
                                <div class="row g-4">
                                    <div class="col-md-12">
                                        <label for="nama_menu" class="form-label">Nama Menu</label>
                                        <input type="text" name="nama_menu" class="form-control <?= isset($_SESSION['errors']['nama_menu']) ? 'is-invalid' : '' ?>" id="nama_menu" value="<?= $nama_menu; ?>">
                                        <div class="invalid-feedback">
                                            <?= isset($_SESSION['errors']['nama_menu']) ? $_SESSION['errors']['nama_menu'] : '' ?>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="harga" class="form-label">Harga</label>
                                        <input type="number" name="harga" class="form-control <?= isset($_SESSION['errors']['harga']) ? 'is-invalid' : '' ?>" id="harga" value="<?= $harga; ?>">
                                        <span>Contoh: 25000</span>
                                        <div class="invalid-feedback">
                                            <?= isset($_SESSION['errors']['harga']) ? $_SESSION['errors']['harga'] : '' ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="kategori" class="form-label">Kategori</label>
                                        <select class="form-select <?= isset($_SESSION['errors']['kategori']) ? 'is-invalid' : '' ?>" id="kategori" name="kategori">
                                            <option value="" disabled selected>-- Pilih Kategori --</option>
                                            <option value="Dessert" <?= ($kategori === 'Dessert') ? 'selected' : ''; ?>>Dessert</option>
                                            <option value="Hot Kitchen" <?= ($kategori === 'Hot Kitchen') ? 'selected' : ''; ?>>Hot Kitchen</option>
                                            <option value="Drink" <?= ($kategori === 'Drink') ? 'selected' : ''; ?>>Drink</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            <?= isset($_SESSION['errors']['kategori']) ? $_SESSION['errors']['kategori'] : '' ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select <?= isset($_SESSION['errors']['status']) ? 'is-invalid' : '' ?>" id="status" name="status">
                                            <option value="" disabled selected>-- Pilih Status --</option>
                                            <option value="Tersedia" <?= ($status === 'Tersedia') ? 'selected' : ''; ?>>Tersedia</option>
                                            <option value="Tidak Tersedia" <?= ($status === 'Tidak Tersedia') ? 'selected' : ''; ?>>Tidak Tersedia</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            <?= isset($_SESSION['errors']['status']) ? $_SESSION['errors']['status'] : '' ?>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="deskripsi" class="form-label">Deskripsi Singkat</label>
                                        <textarea class="form-control <?= isset($_SESSION['errors']['deskripsi']) ? 'is-invalid' : '' ?>" id="deskripsi" name="deskripsi" rows="3"><?= $deskripsi; ?></textarea>
                                        <div class="invalid-feedback">
                                            <?= isset($_SESSION['errors']['deskripsi']) ? $_SESSION['errors']['deskripsi'] : '' ?>
                                        </div>
                                    </div>
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