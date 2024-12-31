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
} elseif ($_SESSION['level'] !== 'User') {
    echo "<script>window.location.href = '" . base_url('error/403.php') . "';</script>";
    exit();
}

// Judul Halaman
$title = 'Profil';

ob_start(); // Start output buffering 
?>

<!-- Single Page Header start -->
<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6"><?= $title; ?></h1>
</div>
<!-- Single Page Header End -->

<!-- Profile Page Start -->
<div class="container-fluid py-5">
    <div class="container">

        <?php
        $id_user = $_SESSION['id_user'];
        $nama_lengkap = $_SESSION['nama_lengkap'];
        $email = $_SESSION['email'];

        $sql_user = "SELECT * FROM users WHERE id_user='$id_user'";
        $query_user = mysqli_query($koneksi, $sql_user);

        $data_user = mysqli_fetch_array($query_user);
        ?>

        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="d-flex justify-content-between gap-2 mb-4">
                    <a href="<?= base_url(); ?>" class="btn btn-outline-secondary rounded-pill"><i class="fas fa-arrow-left me-2"></i> Beranda</a>
                </div>
                <div class="card border-secondary">
                    <div class="card-body">
                        <form method="post" action="<?= base_url('auth/action.php?update-profile=' . $id_user); ?>">
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
                                    <label for="password" class="form-label">Kata Sandi Lama</label>
                                    <input type="password" name="password" class="form-control <?= isset($_SESSION['errors']['password']) ? 'is-invalid' : '' ?>" id="password">
                                    <small>Hanya diisi ketika memperbarui kata sandi.</small>
                                    <div class="invalid-feedback">
                                        <?= isset($_SESSION['errors']['password']) ? $_SESSION['errors']['password'] : '' ?>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <label for="new_password" class="form-label">Kata Sandi Baru</label>
                                    <input type="password" name="new_password" class="form-control <?= isset($_SESSION['errors']['new_password']) ? 'is-invalid' : '' ?>" id="new_password" readonly minlength="8">
                                    <small id="new_password_feedback" class="text-danger"></small>
                                    <div class="invalid-feedback">
                                        <?= isset($_SESSION['errors']['new_password']) ? $_SESSION['errors']['new_password'] : '' ?>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <label for="new_password_confirm" class="form-label">Konfirmasi Kata Sandi Baru</label>
                                    <input type="password" name="new_password_confirm" class="form-control <?= isset($_SESSION['errors']['new_password_confirm']) ? 'is-invalid' : '' ?>" id="new_password_confirm" readonly>
                                    <small id="new_password_confirm_feedback" class="text-danger"></small>
                                    <div class="invalid-feedback">
                                        <?= isset($_SESSION['errors']['new_password_confirm']) ? $_SESSION['errors']['new_password_confirm'] : '' ?>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-secondary text-white px-3">Simpan</button>
                        </form>

                        <?php unset($_SESSION['errors']); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Profile Page End -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        const newPasswordInput = document.getElementById('new_password');
        const newPasswordConfirmInput = document.getElementById('new_password_confirm');
        const newPasswordFeedback = document.getElementById('new_password_feedback');
        const newPasswordConfirmFeedback = document.getElementById('new_password_confirm_feedback');

        passwordInput.addEventListener('input', function() {
            newPasswordInput.readOnly = !passwordInput.value;
            newPasswordConfirmInput.readOnly = !passwordInput.value;
            newPasswordFeedback.textContent = '';
            newPasswordConfirmFeedback.textContent = '';
        });

        newPasswordInput.addEventListener('input', validatePassword);
        newPasswordConfirmInput.addEventListener('input', validatePassword);

        function validatePassword() {
            const newPassword = newPasswordInput.value;
            const newPasswordConfirm = newPasswordConfirmInput.value;

            // Validasi untuk kata sandi baru
            if (newPassword.length < 8) {
                newPasswordFeedback.textContent = 'Kata sandi baru minimal 8 karakter.';
            } else {
                newPasswordFeedback.textContent = ''; // Hapus pesan jika valid
            }

            // Validasi untuk konfirmasi kata sandi baru
            if (newPasswordConfirm) {
                if (newPassword && newPassword !== newPasswordConfirm) {
                    newPasswordConfirmFeedback.textContent = 'Kata sandi baru dan konfirmasi tidak cocok.';
                } else {
                    newPasswordConfirmFeedback.textContent = ''; // Hapus pesan jika valid
                }
            }
        }
    });
</script>

<?php
$content = ob_get_clean(); // Get content and clean buffer

// Menyertakan layout
include('layout.php');
?>