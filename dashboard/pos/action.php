<?php
// Menyertakan konfigurasi
include('../../config/config.php');

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
        echo "<script>window.location.href = '" . base_url('dashboard/pos/show.php') . "';</script>";
    } else {
        $_SESSION['toast-error'] = "Menu gagal ditambahkan ke keranjang.";
        echo "<script>window.location.href = '" . base_url('dashboard/pos/show.php') . "';</script>";
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
        echo "<script>window.location.href = '" . base_url('dashboard/pos/show.php') . "';</script>";
    } else {
        $_SESSION['toast-error'] = "Menu gagal dihapus dari keranjang.";
        echo "<script>window.location.href = '" . base_url('dashboard/pos/show.php') . "';</script>";
    }
    exit();
}

// Melakukan Pesanan
if (isset($_POST['proses_sekarang'])) {
    $id_user = $_SESSION['id_user'];

    $tipe_pesanan = $_POST['tipe'];
    $nomor_meja = $_POST['nomor_meja'];
    $jumlah_pembayaran = $_POST['jumlah_pembayaran'];
    $metode_pembayaran = $_POST['metode_pembayaran'];

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
                                VALUES ('$id_pembayaran', '$id_pesanan', '$id_user', '$tanggal', '$metode_pembayaran', '$jumlah_pembayaran', 'Paid')";
            $query_pembayaran = mysqli_query($koneksi, $sql_pembayaran);

            if ($query_pembayaran) {
                $_SESSION['toast-success'] = "Transaksi berhasil disimpan.";
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
                                    window.location.href = '" . base_url('dashboard/pos/show.php') . "';
                                }, 1000);
                            };
                        };
                    </script>";
                exit();
            } else {
                $_SESSION['toast-error'] = "Terjadi kesalahan.";
                echo "<script>window.location.href = '" . base_url('dashboard/pos/show.php') . "';</script>";
            }
        } else {
            $_SESSION['toast-error'] = "Terjadi kesalahan.";
            echo "<script>window.location.href = '" . base_url('dashboard/pos/show.php') . "';</script>";
        }
        exit();
    } else {
        $_SESSION['toast-error'] = "Terjadi kesalahan.";
        echo "<script>window.location.href = '" . base_url('dashboard/pos/show.php') . "';</script>";
        exit();
    }
}
