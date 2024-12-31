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

// Create user
if (isset($_POST['create-user'])) {
    $errors = [];
    $form_data = [];

    $email = $_POST['email'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $level = $_POST['level'];
    $id_user = uniqid();

    // Validasi email
    if (empty($email)) {
        $errors['email'] = 'Email wajib diisi.';
    } else {
        // Cek apakah email sudah terdaftar
        $cek_email = mysqli_query($koneksi, "SELECT email FROM users WHERE email = '$email'");
        if (mysqli_num_rows($cek_email) > 0) {
            $errors['email'] = 'Email sudah terdaftar.';
        } else {
            $form_data['email'] = $email;
        }
    }

    // Validasi nama lengkap
    if (empty($nama_lengkap)) {
        $errors['nama_lengkap'] = 'Nama lengkap wajib diisi.';
    } else {
        $form_data['nama_lengkap'] = $nama_lengkap;
    }

    // Validasi role
    if (empty($level)) {
        $errors['level'] = 'Role wajib dipilih.';
    } else {
        $form_data['level'] = $level;
    }

    // Jika tidak ada error
    if (empty($errors)) {
        // Gunakan password default bedasarkan role
        if ($level === 'Admin') {
            $password = 'admin';
        } elseif ($level === 'Cashier') {
            $password = 'kasir';
        }

        // Enkripsi password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Query insert pengguna baru
        $sql = "INSERT INTO users (id_user, email, nama_lengkap, password, level) 
                VALUES ('$id_user', '$email', '$nama_lengkap', '$hashed_password', '$level')";

        $query = mysqli_query($koneksi, $sql);

        if ($query) {
            $_SESSION['success'] = "Pengguna berhasil ditambahkan dengan email '$email' dan kata sandi '$password'.";
        } else {
            $_SESSION['error'] = "Pengguna gagal ditambahkan.";
        }

        // Redirect pengguna berdasarkan role
        echo "<script>window.location.href = '" . base_url('dashboard/manage-user/role.php?role=' . urlencode($level)) . "';</script>";
        exit();
    } else {
        // Simpan error dan form_data dalam session
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $form_data;

        echo "<script>window.location.href = '" . base_url('dashboard/manage-user/create.php') . "';</script>";
        exit();
    }
}

// Update user
if (isset($_GET['update-user'])) {
    $errors = [];

    $id_user = $_GET['update-user'];
    $email = $_POST['email'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $level = $_POST['level'];

    // Validasi email
    if (empty($email)) {
        $errors['email'] = 'Email wajib diisi.';
    } else {
        // Cek email duplikat selain pengguna yang sedang di-update
        $cek_email = mysqli_query($koneksi, "SELECT email FROM users WHERE email = '$email' AND id_user != '$id_user'");
        if (mysqli_num_rows($cek_email) > 0) {
            $errors['email'] = 'Email sudah terdaftar.';
        }
    }

    // Validasi nama lengkap
    if (empty($nama_lengkap)) {
        $errors['nama_lengkap'] = 'Nama lengkap wajib diisi.';
    }

    // Validasi role
    if (empty($level)) {
        $errors['level'] = 'Role wajib dipilih.';
    }

    // Jika tidak ada error, lakukan update
    if (empty($errors)) {
        // Update pengguna
        $update_sql = "UPDATE users SET email='$email', nama_lengkap='$nama_lengkap', level='$level' WHERE id_user='$id_user'";

        $update_query = mysqli_query($koneksi, $update_sql);

        if ($update_query) {
            if ($id_user === $_SESSION['id_user']) {
                // Perbarui session
                $_SESSION['email'] = $email;
                $_SESSION['nama_lengkap'] = $nama_lengkap;
                $_SESSION['level'] = $level;
            }

            $_SESSION['success'] = "Pengguna berhasil diperbarui.";
        } else {
            $_SESSION['error'] = "Pengguna gagal diperbarui.";
        }

        // Redirect pengguna berdasarkan role
        echo "<script>window.location.href = '" . base_url('dashboard/manage-user/role.php?role=' . urlencode($level)) . "';</script>";
        exit();
    } else {
        $_SESSION['errors'] = $errors;

        echo "<script>window.location.href = '" . base_url('dashboard/manage-user/edit.php?user-id=' . $id_user) . "';</script>";
        exit();
    }
}

// Delete user
if (isset($_GET['delete-user']) && isset($_GET['current-role'])) {
    $id_user = $_GET['delete-user'];
    $role_saat_ini = $_GET['current-role'];

    $query = mysqli_query($koneksi, "DELETE FROM users WHERE id_user='$id_user'");

    if ($query) {
        if ($id_user === $_SESSION['id_user']) {
            // Logout
            echo "<script>window.location.href = '" . base_url('auth/logout.php') . "';</script>";
            exit();
        } else {
            $_SESSION['success'] = "Pengguna berhasil dihapus.";
            echo "<script>window.location.href = '" . base_url('dashboard/manage-user/role.php?role=' . $role_saat_ini) . "';</script>";
            exit();
        }
    } else {
        $_SESSION['error'] = "Pengguna gagal dihapus.";
        echo "<script>window.location.href = '" . base_url('dashboard/manage-user/role.php?role=' . $role_saat_ini) . "';</script>";
        exit();
    }
}
