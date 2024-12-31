<?php
// Menyertakan konfigurasi
include('../config/config.php');

// Memulai sesi
session_start();

// Cek Auth
if (!isset($_SESSION['email']) || !isset($_SESSION['level'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
} elseif ($_SESSION['level'] !== 'Admin' && $_SESSION['level'] !== 'Cashier') {
    echo json_encode(['error' => 'Forbidden']);
    exit();
}

// Set header untuk JSON dan mencegah cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("Content-Type: application/json");

// Query untuk mendapatkan jumlah item notifikasi
$sql_jumlah_item = "SELECT * FROM pesanan 
                    INNER JOIN users ON pesanan.id_user = users.id_user
                    WHERE pesanan.status='Pending' AND users.level='User'";
$query_jumlah_item = mysqli_query($koneksi, $sql_jumlah_item);
$jumlah_item = mysqli_num_rows($query_jumlah_item);

// Membuat daftar notifikasi
$notifications = [];
while ($data = mysqli_fetch_assoc($query_jumlah_item)) {
    $notifications[] = [
        'id_pesanan' => $data['id_pesanan'],
        'nama_lengkap' => $data['nama_lengkap'],
        'tanggal' => $data['tanggal']
    ];
}

// Mengembalikan data dalam format JSON
echo json_encode([
    'jumlah_item' => $jumlah_item,
    'notifications' => $notifications
]);
