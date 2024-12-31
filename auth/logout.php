<?php
// Menyertakan konfigurasi
include('../config/config.php');

// Memulai sesi
session_start();

// Hapus semua variabel sesi
$_SESSION = [];

// Hapus sesi
session_destroy();

// Mulai ulang sesi baru untuk menyimpan pesan logout
session_start();
$_SESSION['toast-success'] = 'Anda berhasil keluar.';

// Hapus cookie jika ada
if (isset($_COOKIE['id_user'])) {
    setcookie('id_user', '', time() - 3600, "/");
}
if (isset($_COOKIE['nama_lengkap'])) {
    setcookie('nama_lengkap', '', time() - 3600, "/");
}
if (isset($_COOKIE['email'])) {
    setcookie('email', '', time() - 3600, "/");
}
if (isset($_COOKIE['level'])) {
    setcookie('level', '', time() - 3600, "/");
}

// Redirect ke beranda
echo "<script>window.location.href = '" . base_url() . "';</script>";
exit();
