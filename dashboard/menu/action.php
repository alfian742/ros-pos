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
} elseif ($_SESSION['level'] !== 'Admin') {
    echo "<script>window.location.href = '" . base_url('error/403.php') . "';</script>";
    exit();
}

// Add menu
if (isset($_POST['add-menu'])) {
    // Inisialisasi variabel
    $errors = [];
    $form_data = [];

    $nama_menu = $_POST['nama_menu'] ?? '';
    $harga = $_POST['harga'] ?? '';
    $kategori = $_POST['kategori'] ?? '';
    $status = $_POST['status'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';

    // Validasi Nama Menu (tidak boleh kosong dan harus unik)
    if (empty($nama_menu)) {
        $errors['nama_menu'] = 'Nama menu wajib diisi.';
    } else {
        // Cek nama menu
        $cek_nama_menu = mysqli_query($koneksi, "SELECT nama_menu FROM menu WHERE nama_menu = '$nama_menu'");

        if (mysqli_num_rows($cek_nama_menu) > 0) {
            $errors['nama_menu'] = 'Nama menu sudah ada, silakan gunakan nama lain.';
        } else {
            $form_data['nama_menu'] = $nama_menu;
        }
    }

    // Validasi Harga (harus angka dan tidak boleh kosong)
    if (empty($harga)) {
        $errors['harga'] = 'Harga wajib diisi.';
    } elseif (!is_numeric($harga)) {
        $errors['harga'] = 'Harga harus berupa angka.';
    } else {
        $form_data['harga'] = $harga;
    }

    // Validasi Kategori (wajib dipilih)
    if (empty($kategori)) {
        $errors['kategori'] = 'Kategori wajib dipilih.';
    } else {
        $form_data['kategori'] = $kategori;
    }

    // Validasi Status (wajib dipilih)
    if (empty($status)) {
        $errors['status'] = 'Status wajib dipilih.';
    } else {
        $form_data['status'] = $status;
    }

    // Validasi Deskripsi (wajib diisi)
    if (empty($deskripsi)) {
        $errors['deskripsi'] = 'Deskripsi singkat wajib diisi.';
    } else {
        $form_data['deskripsi'] = $deskripsi;
    }

    // Validasi Gambar (wajib diunggah dan validasi tipe serta ukuran file)
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $file = $_FILES['gambar'];
        $file_type = $file['type'];
        $file_size = $file['size'];
        $valid_extensions = ['image/jpg', 'image/jpeg', 'image/png', 'image/webp'];

        if (!in_array($file_type, $valid_extensions)) {
            $errors['gambar'] = 'Format gambar tidak valid. Hanya JPG, JPEG, PNG, dan WEBP yang diperbolehkan.';
        }

        if ($file_size > 1 * 1024 * 1024) {
            $errors['gambar'] = 'Ukuran file maksimal 1 MB.';
        }
    } else {
        $errors['gambar'] = 'Gambar wajib diunggah.';
    }

    // Jika tidak ada error, simpan data ke database
    if (empty($errors)) {
        // Upload gambar dengan nama yang dihasilkan oleh uniqid()
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION); // mendapatkan ekstensi file
        $gambar = uniqid() . '.' . $ext; // menghasilkan nama unik dengan ekstensi yang sama

        $url_gambar = base_url('assets/uploads/menu/' . $gambar);

        // Path absolut untuk pengecekan file di server
        $path_gambar = $_SERVER['DOCUMENT_ROOT'] . parse_url($url_gambar, PHP_URL_PATH);

        $upload = move_uploaded_file($_FILES['gambar']['tmp_name'], $path_gambar);

        if ($upload) {
            $tanggal = date('Y-m-d H:i:s');
            // Insert data ke database
            $sql = "INSERT INTO menu (id_menu, tanggal_buat, nama_menu, kategori, harga, deskripsi, status, gambar) 
                      VALUES (NULL, '$tanggal', '$nama_menu', '$kategori', '$harga', '$deskripsi', '$status', '$gambar')";
            $query = mysqli_query($koneksi, $sql);

            if ($query) {
                $_SESSION['success'] = 'Menu berhasil disimpan.';
            } else {
                $_SESSION['error'] = 'Menu gagal disimpan.';
            }
            echo "<script>window.location.href = '" . base_url('dashboard/menu/show.php') . "';</script>";
            exit();
        } else {
            $_SESSION['error'] = 'Menu gagal disimpan.';
            echo "<script>window.location.href = '" . base_url('dashboard/menu/show.php') . "';</script>";
            exit();
        }
    } else {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $form_data;

        echo "<script>window.location.href = '" . base_url('dashboard/menu/create.php') . "';</script>";
        exit();
    }
}

// Update menu
if (isset($_GET['update-menu'])) {
    // Inisialisasi variabel
    $errors = [];
    $form_data = [];

    $id_menu = $_GET['update-menu']; // Ambil ID menu yang sedang diupdate
    $nama_menu = $_POST['nama_menu'] ?? '';
    $harga = $_POST['harga'] ?? '';
    $kategori = $_POST['kategori'] ?? '';
    $status = $_POST['status'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';

    // Validasi Nama Menu (tidak boleh kosong dan harus unik)
    if (empty($nama_menu)) {
        $errors['nama_menu'] = 'Nama menu wajib diisi.';
    } else {
        // Cek nama menu
        $cek_nama_menu = mysqli_query($koneksi, "SELECT nama_menu FROM menu WHERE nama_menu = '$nama_menu' AND id_menu != '$id_menu'");

        if (mysqli_num_rows($cek_nama_menu) > 0) {
            $errors['nama_menu'] = 'Nama menu sudah ada, silakan gunakan nama lain.';
        } else {
            $form_data['nama_menu'] = $nama_menu;
        }
    }

    // Validasi Harga (harus angka dan tidak boleh kosong)
    if (empty($harga)) {
        $errors['harga'] = 'Harga wajib diisi.';
    } elseif (!is_numeric($harga)) {
        $errors['harga'] = 'Harga harus berupa angka.';
    } else {
        $form_data['harga'] = $harga;
    }

    // Validasi Kategori (wajib dipilih)
    if (empty($kategori)) {
        $errors['kategori'] = 'Kategori wajib dipilih.';
    } else {
        $form_data['kategori'] = $kategori;
    }

    // Validasi Status (wajib dipilih)
    if (empty($status)) {
        $errors['status'] = 'Status wajib dipilih.';
    } else {
        $form_data['status'] = $status;
    }

    // Validasi Deskripsi (wajib diisi)
    if (empty($deskripsi)) {
        $errors['deskripsi'] = 'Deskripsi singkat wajib diisi.';
    } else {
        $form_data['deskripsi'] = $deskripsi;
    }

    // Ambil data gambar lama
    $sql_gambar_lama = "SELECT gambar FROM menu WHERE id_menu = '$id_menu'";
    $query_gambar_lama = mysqli_query($koneksi, $sql_gambar_lama);
    $data_menu = mysqli_fetch_array($query_gambar_lama);
    $gambar_lama = $data_menu['gambar'];

    // Validasi Gambar
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $file = $_FILES['gambar'];
        $file_type = $file['type'];
        $file_size = $file['size'];
        $valid_extensions = ['image/jpg', 'image/jpeg', 'image/png', 'image/webp'];

        if (!in_array($file_type, $valid_extensions)) {
            $errors['gambar'] = 'Format gambar tidak valid. Hanya JPG, JPEG, PNG, dan WEBP yang diperbolehkan.';
        }

        if ($file_size > 1 * 1024 * 1024) {
            $errors['gambar'] = 'Ukuran file maksimal 1 MB.';
        }
    }

    // Jika tidak ada error, simpan data ke database
    if (empty($errors)) {
        $update_query = "UPDATE menu SET 
                         nama_menu = '$nama_menu', 
                         kategori = '$kategori', 
                         harga = '$harga', 
                         deskripsi = '$deskripsi', 
                         status = '$status'";

        // Jika gambar baru diunggah, lakukan upload dan hapus gambar lama
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
            $gambar_baru = uniqid() . '.' . $ext;
            $url_gambar_baru = base_url('assets/uploads/menu/' . $gambar_baru);
            $path_gambar_baru = $_SERVER['DOCUMENT_ROOT'] . parse_url($url_gambar_baru, PHP_URL_PATH);

            $upload = move_uploaded_file($_FILES['gambar']['tmp_name'], $path_gambar_baru);

            if ($upload) {
                // Hapus gambar lama jika ada
                if (!empty($gambar_lama)) {
                    $url_gambar_lama = base_url('assets/uploads/menu/' . $gambar_lama);
                    $path_gambar_lama = $_SERVER['DOCUMENT_ROOT'] . parse_url($url_gambar_lama, PHP_URL_PATH);
                    if (file_exists($path_gambar_lama)) {
                        unlink($path_gambar_lama);
                    }
                }
                // Simpan gambar baru ke database
                $update_query .= ", gambar = '$gambar_baru'";
            } else {
                $_SESSION['error'] = 'Gagal mengunggah gambar baru.';
                echo "<script>window.location.href = '" . base_url('dashboard/menu/edit.php?menu-id=' . $id_menu) . "';</script>";
                exit();
            }
        }

        $update_query .= " WHERE id_menu = '$id_menu'";
        $query_update = mysqli_query($koneksi, $update_query);

        if ($query_update) {
            $_SESSION['success'] = 'Menu berhasil diperbarui.';
        } else {
            $_SESSION['error'] = 'Menu gagal diperbarui.' . mysqli_error($koneksi);
        }

        echo "<script>window.location.href = '" . base_url('dashboard/menu/show.php') . "';</script>";
        exit();
    } else {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $form_data;

        echo "<script>window.location.href = '" . base_url('dashboard/menu/edit.php?menu-id=' . $id_menu) . "';</script>";
        exit();
    }
}

// Delete menu
if (isset($_GET['delete-menu'])) {
    $id_menu = $_GET['delete-menu'];

    // Ambil data gambar lama dari database
    $query_gambar = mysqli_query($koneksi, "SELECT gambar FROM menu WHERE id_menu='$id_menu'");
    $data_gambar = mysqli_fetch_array($query_gambar);
    $gambar = $data_gambar['gambar'] ?? '';

    // Hapus gambar jika ada
    if (!empty($gambar)) {
        $url_gambar = base_url('assets/uploads/menu/' . $gambar);
        $path_gambar = $_SERVER['DOCUMENT_ROOT'] . parse_url($url_gambar, PHP_URL_PATH);
        if (file_exists($path_gambar)) {
            unlink($path_gambar);
        }
    }

    // Hapus data menu dari database
    $query = mysqli_query($koneksi, "DELETE FROM menu WHERE id_menu='$id_menu'");

    if ($query) {
        $_SESSION['success'] = "Menu berhasil dihapus.";
    } else {
        $_SESSION['error'] = "Menu gagal dihapus.";
    }

    // Redirect setelah proses selesai
    echo "<script>window.location.href = '" . base_url('dashboard/menu/show.php') . "';</script>";
    exit();
}

// Update status menu
if (isset($_GET['update-menu-status'])) {
    $id_menu = $_GET['update-menu-status'];
    $status = $_POST['status'];

    $query = mysqli_query($koneksi, "UPDATE menu SET status='$status' WHERE id_menu='$id_menu'");

    if ($query) {
        $_SESSION['success'] = "Status menu berhasil diperbarui.";
    } else {
        $_SESSION['error'] = "Status menu gagal diperbarui.";
    }

    echo "<script>window.location.href = '" . base_url('dashboard/menu/show.php') . "';</script>";
    exit();
}
