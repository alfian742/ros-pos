<?php
// Menyertakan konfigurasi
include('../config/config.php');

// Memulai sesi
session_start();

// Cek Auth
if (!isset($_SESSION['email']) || !isset($_SESSION['level'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Set header untuk JSON dan mencegah cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("Content-Type: application/json");

// Query untuk mendapatkan jumlah item notifikasi
$sql_notifikasi = "SELECT id_pesanan, status FROM pesanan WHERE id_user='{$_SESSION['id_user']}' AND status NOT IN ('Completed', 'Cancelled')";
$query_notifikasi = mysqli_query($koneksi, $sql_notifikasi);

// Membuat daftar notifikasi
$notifications = [];
while ($data = mysqli_fetch_assoc($query_notifikasi)) {
    $notifications[] = [
        'id_pesanan' => $data['id_pesanan'],
        'status' => $data['status']
    ];
}

// Menghitung jumlah item notifikasi
$jumlah_notifikasi = count($notifications);

// Mengembalikan data dalam format JSON
echo json_encode([
    'jumlah_notifikasi' => $jumlah_notifikasi,
    'notifications' => $notifications
]);
