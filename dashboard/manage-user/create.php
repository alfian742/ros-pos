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
$title = 'Tambah Pengguna';

ob_start(); // Start output buffering 
?>

<section class="section">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card">
                <div class="card-body pt-4">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <a href="<?= base_url('dashboard/manage-user/role.php?role=Admin'); ?>" class="btn btn-outline-secondary rounded-circle border-0 btn-back"><i class="bi bi-arrow-left"></i></a>
                        <h4 class="mb-0 fw-bold"><?= $title ?></h4>
                    </div>

                    <!-- Alerts -->
                    <?php include('../components/alerts.php') ?>

                    <?php
                    // Value Form dari session (jika ada error)
                    $email = isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : '';
                    $nama_lengkap = isset($_SESSION['form_data']['nama_lengkap']) ? htmlspecialchars($_SESSION['form_data']['nama_lengkap']) : '';
                    $level = isset($_SESSION['form_data']['level']) ? htmlspecialchars($_SESSION['form_data']['level']) : '';
                    ?>

                    <form method="post" action="<?= base_url('dashboard/manage-user/action.php'); ?>">
                        <div class="row g-4 mb-4">
                            <div class="col-md-12">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" class="form-control <?= isset($_SESSION['errors']['email']) ? 'is-invalid' : '' ?>" id="email" value="<?= $email; ?>">
                                <div class="invalid-feedback">
                                    <?= isset($_SESSION['errors']['email']) ? $_SESSION['errors']['email'] : '' ?>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" class="form-control <?= isset($_SESSION['errors']['nama_lengkap']) ? 'is-invalid' : '' ?>" id="nama_lengkap" value="<?= $nama_lengkap; ?>">
                                <div class="invalid-feedback">
                                    <?= isset($_SESSION['errors']['nama_lengkap']) ? $_SESSION['errors']['nama_lengkap'] : '' ?>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label for="level" class="form-label">Role</label>
                                <select class="form-select <?= isset($_SESSION['errors']['level']) ? 'is-invalid' : '' ?>" id="level" name="level">
                                    <option value="" disabled selected>-- Pilih Role --</option>
                                    <option value="Admin" <?= ($level === 'Admin') ? 'selected' : ''; ?>>Admin</option>
                                    <option value="Cashier" <?= ($level === 'Cashier') ? 'selected' : ''; ?>>Kasir</option>
                                </select>
                                <div class="invalid-feedback">
                                    <?= isset($_SESSION['errors']['level']) ? $_SESSION['errors']['level'] : '' ?>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <button type="submit" class="btn btn-primary text-white px-3" name="create-user">Simpan</button>
                        </div>
                    </form>

                    <?php
                    unset($_SESSION['errors']);
                    unset($_SESSION['form_data']);
                    ?>
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