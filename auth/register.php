<?php
// Menyertakan konfigurasi
include('../config/config.php');

// Memulai sesi
session_start();

// Judul Halaman
$title = 'Daftar';

ob_start(); // Start output buffering 
?>

<div class="card mb-3">
    <div class="card-body">
        <div class="py-2">
            <h5 class="card-title text-center pb-0 fs-4">Daftar</h5>
            <p class="text-center small">Data Anda tidak akan dibagikan</p>
        </div>

        <!-- Alerts -->
        <?php include('components/alerts.php'); ?>

        <?php
        // Value Form dari session (jika ada error)
        $email = isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : '';
        $nama_lengkap = isset($_SESSION['form_data']['nama_lengkap']) ? htmlspecialchars($_SESSION['form_data']['nama_lengkap']) : '';
        ?>

        <form class="row g-3 py-2" method="post" action="<?= base_url('auth/action.php'); ?>">
            <div class="col-12">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" class="form-control <?= isset($_SESSION['errors']['email']) ? 'is-invalid' : '' ?>" id="email" value="<?= $email; ?>" autofocus>
                <div class="invalid-feedback">
                    <?= isset($_SESSION['errors']['email']) ? $_SESSION['errors']['email'] : '' ?>
                </div>
            </div>

            <div class="col-12">
                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control <?= isset($_SESSION['errors']['nama_lengkap']) ? 'is-invalid' : '' ?>" id="nama_lengkap" value="<?= $nama_lengkap; ?>">
                <div class="invalid-feedback">
                    <?= isset($_SESSION['errors']['nama_lengkap']) ? $_SESSION['errors']['nama_lengkap'] : '' ?>
                </div>
            </div>

            <div class="col-12">
                <label for="password" class="form-label">Kata Sandi</label>
                <input type="password" name="password" class="form-control <?= isset($_SESSION['errors']['password']) ? 'is-invalid' : '' ?>" id="password" minlength="8">
                <small id="password_feedback" class="text-danger"></small>
                <div class="invalid-feedback">
                    <?= isset($_SESSION['errors']['password']) ? $_SESSION['errors']['password'] : '' ?>
                </div>
            </div>

            <div class="col-12">
                <label for="password_confirm" class="form-label">Konfirmasi Kata Sandi</label>
                <input type="password" name="password_confirm" class="form-control <?= isset($_SESSION['errors']['password_confirm']) ? 'is-invalid' : '' ?>" id="password_confirm">
                <small id="password_confirm_feedback" class="text-danger"></small>
                <div class="invalid-feedback">
                    <?= isset($_SESSION['errors']['password_confirm']) ? $_SESSION['errors']['password_confirm'] : '' ?>
                </div>
            </div>

            <div class="col-12">
                <button class="btn btn-warning w-100 fw-semibold" type="submit" name="register">Daftar</button>
            </div>

            <div class="col-12 text-center">
                <p class="small mb-3">Sudah punya akun? <a href="<?= base_url('auth/login.php'); ?>" class="link-warning">Masuk</a></p>
                <p class="small mb-0"><a href="<?= base_url(); ?>" class="link-warning">Kembali ke Beranda</a></p>
            </div>
        </form>

        <?php
        unset($_SESSION['errors']);
        unset($_SESSION['form_data']);
        ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        const passwordConfirmInput = document.getElementById('password_confirm');
        const passwordFeedback = document.getElementById('password_feedback');
        const passwordConfirmFeedback = document.getElementById('password_confirm_feedback');

        passwordInput.addEventListener('input', validatePassword);
        passwordConfirmInput.addEventListener('input', validatePassword);

        function validatePassword() {
            const password = passwordInput.value;
            const passwordConfirm = passwordConfirmInput.value;

            // Validasi untuk kata sandi
            if (password.length < 8) {
                passwordFeedback.textContent = 'Kata sandi minimal 8 karakter.';
            } else {
                passwordFeedback.textContent = ''; // Hapus pesan jika valid
            }

            // Validasi untuk konfirmasi kata sandi
            if (passwordConfirm) {
                if (password && password !== passwordConfirm) {
                    passwordConfirmFeedback.textContent = 'Konfirmasi kata sandi tidak cocok.';
                } else {
                    passwordConfirmFeedback.textContent = ''; // Hapus pesan jika valid
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