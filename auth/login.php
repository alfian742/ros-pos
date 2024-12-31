<?php
// Menyertakan konfigurasi
include('../config/config.php');

// Memulai sesi
session_start();

// Judul Halaman
$title = 'Masuk';

ob_start(); // Start output buffering 
?>

<div class="card mb-3">
    <div class="card-body">
        <div class="py-2">
            <h5 class="card-title text-center pb-0 fs-4">Masuk</h5>
            <p class="text-center small">Silakan masuk dengan akun Anda</p>
        </div>

        <!-- Alerts -->
        <?php include('components/alerts.php'); ?>

        <?php
        // Ambil data admin di tabel user
        $user = mysqli_query($koneksi, "SELECT * FROM users WHERE level='Admin'");

        // Cek data
        if (mysqli_num_rows($user) > 0) :
        ?>
            <form class="row g-3 py-2" method="post" action="<?= base_url('auth/action.php'); ?>">
                <div class="col-12">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control <?= isset($_SESSION['errors']['email']) ? 'is-invalid' : '' ?>" id="email" autofocus>
                    <div class="invalid-feedback">
                        <?= isset($_SESSION['errors']['email']) ? $_SESSION['errors']['email'] : '' ?>
                    </div>
                </div>

                <div class="col-12">
                    <label for="password" class="form-label">Kata Sandi</label>
                    <input type="password" name="password" class="form-control <?= isset($_SESSION['errors']['password']) ? 'is-invalid' : '' ?>" id="password">
                    <div class="invalid-feedback">
                        <?= isset($_SESSION['errors']['password']) ? $_SESSION['errors']['password'] : '' ?>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" value="true" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Ingat saya</label>
                    </div>
                </div>

                <div class="col-12">
                    <button class="btn btn-warning w-100 fw-semibold" type="submit" name="login">Masuk</button>
                </div>

                <div class="col-12 text-center">
                    <p class="small mb-3">Belum punya akun? <a href="<?= base_url('auth/register.php'); ?>" class="link-warning">Daftar</a></p>
                    <p class="small mb-0"><a href="<?= base_url(); ?>" class="link-warning">Kembali ke Beranda</a></p>
                </div>
            </form>

            <?php unset($_SESSION['errors']); ?>

        <?php else: ?>

            <form class="row g-3 py-2" method="post" action="<?= base_url('auth/action.php'); ?>">
                <div class="col-12">
                    <button class="btn btn-warning w-100 fw-semibold" type="submit" name="create-admin-account">Buat Akun</button>
                </div>
            </form>

        <?php endif ?>
    </div>
</div>

<?php
$content = ob_get_clean(); // Get content and clean buffer

// Menyertakan layout
include('layout.php');
?>