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
}

// Tambah item ke keranjang
if (isset($_GET['add-to-cart'])) {
    $id_user = $_SESSION['id_user'];
    $id_menu = $_GET['add-to-cart'];

    $sql_keranjang = "INSERT INTO keranjang (id_keranjang, id_user, id_pesanan, id_menu, jumlah, status) 
                      VALUES (NULL, '$id_user', NULL, '$id_menu', 1, 'Belum Dipesan')";
    $query_keranjang = mysqli_query($koneksi, $sql_keranjang);

    if ($query_keranjang) {
        $_SESSION['toast-success'] = "Menu ditambahkan ke keranjang.";
        echo "<script>window.location.href = '" . base_url('user/menu.php') . "';</script>";
    } else {
        $_SESSION['toast-error'] = "Menu gagal ditambahkan ke keranjang.";
        echo "<script>window.location.href = '" . base_url('user/menu.php') . "';</script>";
    }
    exit();
}

// Hapus item dari keranjang
if (isset($_GET['delete-cart'])) {
    $id_keranjang = $_GET['delete-cart'];

    $sql_keranjang = "DELETE FROM keranjang WHERE id_keranjang='$id_keranjang'";
    $query_keranjang = mysqli_query($koneksi, $sql_keranjang);

    if ($query_keranjang) {
        $_SESSION['toast-success'] = "Menu dihapus dari keranjang.";
        echo "<script>window.location.href = '" . base_url('user/cart.php') . "';</script>";
    } else {
        $_SESSION['toast-error'] = "Menu gagal dihapus dari keranjang.";
        echo "<script>window.location.href = '" . base_url('user/cart.php') . "';</script>";
    }
    exit();
}

// Melakukan Pesanan
if (isset($_POST['pesan_sekarang'])) {
    $id_user = $_SESSION['id_user'];

    $tipe_pesanan = $_POST['tipe'];
    $nomor_meja = $_POST['nomor_meja'];

    if ($tipe_pesanan === 'Takeaway') {
        $nomor_meja = NULL; // Set menjadi NULL untuk Takeaway
    } else {
        $nomor_meja = empty($nomor_meja) ? NULL : $nomor_meja; // Set menjadi NULL jika kosong
    }

    // Membuat ID pesanan unik
    $id_pesanan = strtoupper('OR' . uniqid());
    $id_pembayaran = strtoupper('ST' . uniqid());

    // Mengambil tanggal saat ini
    $tanggal = date('Y-m-d H:i:s');

    // Simpan data pesanan ke tabel pesanan dan tabel pembayaran
    $sql_pesanan = "INSERT INTO pesanan (id_pesanan, id_user, tanggal, tipe_pesanan, nomor_meja, total_pembayaran, status)
                    VALUES ('$id_pesanan', '$id_user', '$tanggal', '$tipe_pesanan', '$nomor_meja', 0, 'Pending')";
    $query_pesanan = mysqli_query($koneksi, $sql_pesanan);


    if ($query_pesanan) {
        $total_pembayaran = 0;

        // Mengambil data keranjang yang belum dipesan untuk pengguna yang sedang login
        $sql_keranjang = "SELECT * FROM keranjang 
                          INNER JOIN menu ON keranjang.id_menu = menu.id_menu
                          WHERE keranjang.id_user='$id_user' 
                          AND keranjang.status='Belum Dipesan'";
        $query_keranjang = mysqli_query($koneksi, $sql_keranjang);

        // Loop untuk memproses setiap item di keranjang
        while ($data_keranjang = mysqli_fetch_array($query_keranjang)) {
            $id_menu = $data_keranjang['id_menu'];
            $harga = $data_keranjang['harga'];
            $jumlah = $_POST['jumlah'][$id_menu]; // Mengambil jumlah dari input form

            // Menghitung subtotal
            $sub_total = $harga * $jumlah;
            $total_pembayaran += $sub_total;

            // Update keranjang untuk memasukkan jumlah terbaru dan status
            $sql_update_keranjang = "UPDATE keranjang 
                                     SET id_pesanan='$id_pesanan', jumlah='$jumlah', status='Sudah Dipesan'
                                     WHERE id_keranjang='{$data_keranjang['id_keranjang']}'";
            $query_update_keranjang = mysqli_query($koneksi, $sql_update_keranjang);
        }

        // Update data pesanan ke tabel pesanan
        $sql_update_pesanan = "UPDATE pesanan SET total_pembayaran='$total_pembayaran' 
                               WHERE id_pesanan='$id_pesanan' AND id_user='$id_user'";
        $query_update_pesanan = mysqli_query($koneksi, $sql_update_pesanan);

        // Cek apakah data diupdate
        if ($query_update_keranjang && $query_update_pesanan) {
            // Insert data ke tabel pembayaran
            $sql_pembayaran = "INSERT INTO pembayaran (id_pembayaran, id_pesanan, id_user, tanggal, metode_pembayaran, jumlah_pembayaran, status)
                                VALUES ('$id_pembayaran', '$id_pesanan', '$id_user', '$tanggal', '', 0, 'Unpaid')";
            $query_pembayaran = mysqli_query($koneksi, $sql_pembayaran);

            if ($query_pembayaran) {
                $_SESSION['toast-success'] = "Pesanan telah dikirim, silakan melakukan pembayaran.";
                echo "<script>window.location.href = '" . base_url('user/order-detail.php?order-id=' . $id_pesanan) . "';</script>";
            } else {
                $_SESSION['toast-error'] = "Terjadi kesalahan.";
                echo "<script>window.location.href = '" . base_url('user/cart.php') . "';</script>";
            }
        } else {
            $_SESSION['toast-error'] = "Terjadi kesalahan.";
            echo "<script>window.location.href = '" . base_url('user/cart.php') . "';</script>";
        }
        exit();
    } else {
        $_SESSION['toast-error'] = "Terjadi kesalahan.";
        echo "<script>window.location.href = '" . base_url('user/cart.php') . "';</script>";
        exit();
    }
}

// Hapus item dari pesanan yang telah dibuat
if (isset($_GET['delete-order-item']) && $_GET['order-id']) {
    $id_keranjang = $_GET['delete-order-item'];
    $id_pesanan = $_GET['order-id'];

    $sql_keranjang = "DELETE FROM keranjang WHERE id_keranjang='$id_keranjang'";
    $query_keranjang = mysqli_query($koneksi, $sql_keranjang);

    if ($query_keranjang) {
        // Cek apakah masih ada item di keranjang
        $cek_keranjang = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM keranjang WHERE id_pesanan='$id_pesanan'");
        $jumlah_item_pesanan = mysqli_fetch_array($cek_keranjang);

        if ($jumlah_item_pesanan['total'] > 0) {
            $_SESSION['toast-success'] = "Menu dihapus dari pesanan.";
            echo "<script>window.location.href = '" . base_url('user/order-edit.php?order-id=' . $id_pesanan) . "';</script>";
        } else {
            // Hapus pesanan jika tidak ada item lagi
            $hapus_pesanan = mysqli_query($koneksi, "DELETE FROM pesanan WHERE id_pesanan='$id_pesanan'");

            if ($hapus_pesanan) {
                $_SESSION['toast-success'] = "Pesanan telah dihapus karena tidak ada menu lagi.";
            } else {
                $_SESSION['toast-error'] = "Terjadi kesalahan.";
            }
            echo "<script>window.location.href = '" . base_url('user/order.php') . "';</script>";
        }
    } else {
        $_SESSION['toast-error'] = "Menu gagal dihapus dari pesanan.";
        echo "<script>window.location.href = '" . base_url('user/order-edit.php?order-id=' . $id_pesanan) . "';</script>";
    }
    exit();
}

// Batalkan pesanan
if (isset($_GET['cancel-order'])) {
    $id_pesanan = $_GET['cancel-order'];

    $cekStatusPesanan = mysqli_query($koneksi, "SELECT status FROM pesanan WHERE id_pesanan='$id_pesanan'");
    $statusPesanan = mysqli_fetch_array($cekStatusPesanan);

    if ($statusPesanan['status'] == 'Pending' || $statusPesanan['status'] == 'Cancelled') {
        $query = mysqli_query($koneksi, "UPDATE pesanan SET status='Cancelled' WHERE id_pesanan='$id_pesanan'");

        if ($query) {
            $_SESSION['toast-success'] = "Pesanan berhasil dibatalkan.";
        } else {
            $_SESSION['toast-error'] = "Status pesanan gagal diperbarui.";
        }
    } else {
        $_SESSION['toast-error'] = "Status pesanan gagal diperbarui.";
    }

    echo "<script>window.location.href = '" . base_url('user/order-detail.php?order-id=' . $id_pesanan) . "';</script>";

    exit();
}

// Edit pesanan
if (isset($_POST['edit_pesanan'])) {
    $id_user = $_SESSION['id_user'];

    $id_pesanan = $_POST['id_pesanan'];
    $tipe_pesanan = $_POST['tipe'];
    $nomor_meja = $_POST['nomor_meja'];

    if ($tipe_pesanan === 'Takeaway') {
        $nomor_meja = NULL; // Set menjadi NULL untuk Takeaway
    } else {
        $nomor_meja = empty($nomor_meja) ? NULL : $nomor_meja; // Set menjadi NULL jika kosong
    }

    $total_pembayaran = 0;
    $sql_keranjang = "SELECT * FROM keranjang 
                      INNER JOIN menu ON keranjang.id_menu = menu.id_menu
                      WHERE keranjang.id_pesanan='$id_pesanan'
                      AND keranjang.id_user='$id_user'";
    $query_keranjang = mysqli_query($koneksi, $sql_keranjang);

    if ($query_keranjang && mysqli_num_rows($query_keranjang) > 0) {
        $query_update_keranjang = true;

        while ($data_keranjang = mysqli_fetch_array($query_keranjang)) {
            $id_menu = $data_keranjang['id_menu'];
            $harga = $data_keranjang['harga'];
            $jumlah = $_POST['jumlah'][$id_menu];
            $sub_total = $harga * $jumlah;
            $total_pembayaran += $sub_total;

            $sql_update_keranjang = "UPDATE keranjang 
                                     SET jumlah='$jumlah', status='Sudah Dipesan'
                                     WHERE id_keranjang='{$data_keranjang['id_keranjang']}'";
            $query_update_keranjang = $query_update_keranjang && mysqli_query($koneksi, $sql_update_keranjang);
        }

        $sql_update_pesanan = "UPDATE pesanan SET tipe_pesanan='$tipe_pesanan', nomor_meja='$nomor_meja', total_pembayaran='$total_pembayaran' 
                               WHERE id_pesanan='$id_pesanan' AND id_user='$id_user'";
        $query_update_pesanan = mysqli_query($koneksi, $sql_update_pesanan);

        if ($query_update_keranjang && $query_update_pesanan) {
            $_SESSION['toast-success'] = "Pesanan telah diedit, silakan melakukan pembayaran.";
            echo "<script>window.location.href = '" . base_url('user/order-detail.php?order-id=' . $id_pesanan) . "';</script>";
        } else {
            $_SESSION['toast-error'] = "Terjadi kesalahan saat memperbarui data.";
            echo "<script>window.location.href = '" . base_url('user/order-edit.php?order-id=' . $id_pesanan) . "';</script>";
        }
    } else {
        $_SESSION['toast-error'] = "Keranjang kosong atau tidak dapat diakses.";
        echo "<script>window.location.href = '" . base_url('user/order-edit.php?order-id=' . $id_pesanan) . "';</script>";
    }
    exit();
}
