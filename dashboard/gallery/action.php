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

// Add gallery
if (isset($_POST['add-gallery'])) {
    // Inisialisasi variabel
    $errors = [];
    $form_data = [];

    $keterangan = $_POST['keterangan'] ?? '';

    // Validasi keterangan (wajib diisi)
    if (empty($keterangan)) {
        $errors['keterangan'] = 'Keterangan singkat wajib diisi.';
    } else {
        $form_data['keterangan'] = $keterangan;
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

        $url_gambar = base_url('assets/uploads/gallery/' . $gambar);

        // Path absolut untuk pengecekan file di server
        $path_gambar = $_SERVER['DOCUMENT_ROOT'] . parse_url($url_gambar, PHP_URL_PATH);

        $upload = move_uploaded_file($_FILES['gambar']['tmp_name'], $path_gambar);

        if ($upload) {
            $tanggal = date('Y-m-d H:i:s');
            // Insert data ke database
            $sql = "INSERT INTO galeri (id_galeri, tanggal_buat, keterangan, gambar) 
                    VALUES (NULL, '$tanggal', '$keterangan', '$gambar')";
            $query = mysqli_query($koneksi, $sql);

            if ($query) {
                $_SESSION['success'] = 'Item berhasil disimpan.';
            } else {
                $_SESSION['error'] = 'Item gagal disimpan.';
            }
            echo "<script>window.location.href = '" . base_url('dashboard/gallery/show.php') . "';</script>";
            exit();
        } else {
            $_SESSION['error'] = 'Item gagal disimpan.';
            echo "<script>window.location.href = '" . base_url('dashboard/gallery/show.php') . "';</script>";
            exit();
        }
    } else {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $form_data;

        echo "<script>window.location.href = '" . base_url('dashboard/gallery/create.php') . "';</script>";
        exit();
    }
}

// Update galeri
if (isset($_GET['update-gallery'])) {
    // Inisialisasi variabel
    $errors = [];
    $form_data = [];

    $id_galeri = $_GET['update-gallery']; // Ambil ID galeri yang sedang diupdate
    $keterangan = $_POST['keterangan'] ?? '';

    // Validasi keterangan (wajib diisi)
    if (empty($keterangan)) {
        $errors['keterangan'] = 'Keterangan singkat wajib diisi.';
    } else {
        $form_data['keterangan'] = $keterangan;
    }

    // Ambil data gambar lama
    $sql_gambar_lama = "SELECT gambar FROM galeri WHERE id_galeri = '$id_galeri'";
    $query_gambar_lama = mysqli_query($koneksi, $sql_gambar_lama);
    $data_galeri = mysqli_fetch_array($query_gambar_lama);
    $gambar_lama = $data_galeri['gambar'];

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
        $update_query = "UPDATE galeri SET keterangan = '$keterangan'";

        // Jika gambar baru diunggah, lakukan upload dan hapus gambar lama
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
            $gambar_baru = uniqid() . '.' . $ext;
            $url_gambar_baru = base_url('assets/uploads/gallery/' . $gambar_baru);
            $path_gambar_baru = $_SERVER['DOCUMENT_ROOT'] . parse_url($url_gambar_baru, PHP_URL_PATH);

            $upload = move_uploaded_file($_FILES['gambar']['tmp_name'], $path_gambar_baru);

            if ($upload) {
                // Hapus gambar lama jika ada
                if (!empty($gambar_lama)) {
                    $url_gambar_lama = base_url('assets/uploads/gallery/' . $gambar_lama);
                    $path_gambar_lama = $_SERVER['DOCUMENT_ROOT'] . parse_url($url_gambar_lama, PHP_URL_PATH);
                    if (file_exists($path_gambar_lama)) {
                        unlink($path_gambar_lama);
                    }
                }
                // Simpan gambar baru ke database
                $update_query .= ", gambar = '$gambar_baru'";
            } else {
                $_SESSION['error'] = 'Gagal mengunggah gambar baru.';
                echo "<script>window.location.href = '" . base_url('dashboard/gallery/edit.php?gallery-id=' . $id_galeri) . "';</script>";
                exit();
            }
        }

        $update_query .= " WHERE id_galeri = '$id_galeri'";
        $query_update = mysqli_query($koneksi, $update_query);

        if ($query_update) {
            $_SESSION['success'] = 'Item berhasil diperbarui.';
        } else {
            $_SESSION['error'] = 'Item gagal diperbarui.' . mysqli_error($koneksi);
        }

        echo "<script>window.location.href = '" . base_url('dashboard/gallery/show.php') . "';</script>";
        exit();
    } else {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $form_data;

        echo "<script>window.location.href = '" . base_url('dashboard/gallery/edit.php?gallery-id=' . $id_galeri) . "';</script>";
        exit();
    }
}

// Delete galeri
if (isset($_GET['delete-gallery'])) {
    $id_galeri = $_GET['delete-gallery'];

    // Ambil data gambar lama dari database
    $query_gambar = mysqli_query($koneksi, "SELECT gambar FROM galeri WHERE id_galeri='$id_galeri'");
    $data_gambar = mysqli_fetch_array($query_gambar);
    $gambar = $data_gambar['gambar'] ?? '';

    // Hapus gambar jika ada
    if (!empty($gambar)) {
        $url_gambar = base_url('assets/uploads/gallery/' . $gambar);
        $path_gambar = $_SERVER['DOCUMENT_ROOT'] . parse_url($url_gambar, PHP_URL_PATH);
        if (file_exists($path_gambar)) {
            unlink($path_gambar);
        }
    }

    // Hapus data galeri dari database
    $query = mysqli_query($koneksi, "DELETE FROM galeri WHERE id_galeri='$id_galeri'");

    if ($query) {
        $_SESSION['success'] = "Item berhasil dihapus.";
    } else {
        $_SESSION['error'] = "Item gagal dihapus.";
    }

    // Redirect setelah proses selesai
    echo "<script>window.location.href = '" . base_url('dashboard/gallery/show.php') . "';</script>";
    exit();
}
