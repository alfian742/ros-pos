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
} elseif ($_SESSION['level'] !== 'Admin' && $_SESSION['level'] !== 'Cashier') {
    echo "<script>window.location.href = '" . base_url('error/403.php') . "';</script>";
    exit();
}

// Update order status
if (isset($_GET['update-order-status']) && isset($_GET['current-status'])) {
    $id_pesanan = $_GET['update-order-status'];
    $status_saat_ini = $_GET['current-status'];
    $status = $_POST['status'];

    $query = mysqli_query($koneksi, "UPDATE pesanan SET status='$status' WHERE id_pesanan='$id_pesanan'");

    if ($query) {
        $_SESSION['success'] = "Status pesanan berhasil diperbarui.";
    } else {
        $_SESSION['error'] = "Status pesanan gagal diperbarui.";
    }

    if ($status_saat_ini === 'In Detail Page') {
        echo "<script>window.location.href = '" . base_url('dashboard/order-user/detail.php?order-id=' . $id_pesanan) . "';</script>";
    } elseif ($status_saat_ini !== 'All') {
        echo "<script>window.location.href = '" . base_url('dashboard/order-user/status.php?status=' . $status_saat_ini) . "';</script>";
    } else {
        echo "<script>window.location.href = '" . base_url('dashboard/order-user/show.php') . "';</script>";
    }
    exit();
}

// Update payment status
if (isset($_GET['update-payment'])) {
    $id_pesanan = $_GET['update-payment'];
    $id_pembayaran = $_POST['id_pembayaran'];
    $total_pembayaran = $_POST['total_pembayaran'];
    $jumlah_pembayaran = $_POST['jumlah_pembayaran'];
    $metode_pembayaran = $_POST['metode_pembayaran'];

    // Validasi jumlah pembayaran
    if ($jumlah_pembayaran >= $total_pembayaran) {
        $query = mysqli_query($koneksi, "UPDATE pembayaran SET metode_pembayaran='$metode_pembayaran', jumlah_pembayaran='$jumlah_pembayaran', status='Paid' WHERE id_pesanan='$id_pesanan'");

        if ($query) {
            $_SESSION['success'] = "Transaksi berhasil disimpan.";
            echo "<script>
                        window.onload = function() {
                            var iframe = document.createElement('iframe');
                            iframe.style.height = '0';
                            iframe.style.width = '0';
                            iframe.style.border = '0';
                            iframe.style.position = 'absolute';
                        
                            iframe.src = '" . base_url('dashboard/report/print-note.php?payment-id=' . $id_pembayaran) . "';
                            document.body.appendChild(iframe);
                        
                            iframe.onload = function () {
                                iframe.contentWindow.print();
                                setTimeout(function () {
                                    document.body.removeChild(iframe);
                                    window.location.href = '" . base_url('dashboard/order-user/detail.php?order-id=' . $id_pesanan) . "';
                                }, 1000);
                            };
                        };
                    </script>";
            exit();
        } else {
            $_SESSION['toast-error'] = "Transaksi gagal disimpan.";
        }
    } else {
        $_SESSION['toast-error'] = "Jumlah pembayaran tidak mencukupi.";
    }

    echo "<script>window.location.href = '" . base_url('dashboard/order-user/detail.php?order-id=' . $id_pesanan) . "';</script>";
    exit();
}
