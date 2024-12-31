<?php
// Menyertakan konfigurasi
include('../config/config.php');

// Memulai sesi
session_start();

// Judul Halaman
$title = '500';

ob_start(); // Start output buffering 
?>

<main>
    <div class="container">
        <section class="section min-vh-100 d-flex flex-column align-items-center justify-content-center">
            <img src="<?= base_url('assets/uploads/static/500.svg'); ?>" class="img-fluid" alt="Internal Server Error" height="200" width="200">
            <h3 class="display-1 fw-bold text-warning text-center">500</h3>
            <h4 class="mb-4 text-center">Terjadi Kesalahan Internal Server.</h4>
            <a class="btn btn-lg btn-warning rounded-pill" href="javascript:history.back()">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </section>
    </div>
</main><!-- End #main -->

<?php
$content = ob_get_clean(); // Get content and clean buffer

// Menyertakan layout
include('layout.php');
?>