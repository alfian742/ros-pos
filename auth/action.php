<?php
// Menyertakan konfigurasi
include('../config/config.php');

// Memulai sesi
session_start();

// Login
if (isset($_POST['login'])) {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $errors = [];

        $email = $_POST['email'];
        $password = $_POST['password'];
        $remember = isset($_POST['remember']);

        // Validasi email
        if (empty($email)) {
            $errors['email'] = 'Silakan masukkan email Anda.';
        }

        // Validasi password
        if (empty($password)) {
            $errors['password'] = 'Silakan masukkan kata sandi Anda.';
        }

        if (empty($errors)) {
            // Cek email
            $sql = "SELECT * FROM users WHERE email='$email'";
            $query = mysqli_query($koneksi, $sql);
            $result = mysqli_fetch_array($query);

            if ($result) {
                // Verifikasi password
                if (password_verify($password, $result['password'])) {
                    $_SESSION['id_user'] = $result['id_user'];
                    $_SESSION['nama_lengkap'] = $result['nama_lengkap'];
                    $_SESSION['email'] = $result['email'];
                    $_SESSION['level'] = $result['level'];

                    // Jika Remember Me dicentang, simpan session dalam cookie
                    if ($remember) {
                        setcookie('id_user', $result['id_user'], time() + (86400), "/");
                        setcookie('nama_lengkap', $result['nama_lengkap'], time() + (86400), "/");
                        setcookie('email', $result['email'], time() + (86400), "/");
                        setcookie('level', $result['level'], time() + (86400), "/");
                    }

                    // Redirect berdasarkan level pengguna
                    if ($_SESSION['level'] === 'Admin' || $_SESSION['level'] === 'Cashier') {
                        $_SESSION['success'] = "Selamat Datang " . $_SESSION['nama_lengkap'];
                        echo "<script>window.location.href = '" . base_url('dashboard/index.php') . "';</script>";
                    } else {
                        $_SESSION['toast-success'] = "Selamat Datang " . $_SESSION['nama_lengkap'];
                        echo "<script>window.location.href = '" . base_url() . "';</script>";
                    }
                    exit();
                } else {
                    $_SESSION['error'] = "Email atau Kata Sandi salah.";
                }
            } else {
                $_SESSION['error'] = "Email atau Kata Sandi salah.";
            }
        } else {
            $_SESSION['errors'] = $errors;
        }

        // Jika ada kesalahan, tetap di halaman login
        echo "<script>window.location.href = '" . base_url('auth/login.php') . "';</script>";
        exit();
    }
}

// Register
if (isset($_POST['register'])) {
    $errors = [];
    $form_data = [];

    // Mengambil data dari form
    $email = $_POST['email'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $id_user = uniqid();
    $level = 'User';

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

    // Validasi password
    if (empty($password)) {
        $errors['password'] = 'Kata sandi wajib diisi.';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'Kata sandi minimal 8 karakter.';
    } else {
        // Validasi konfirmasi kata sandi
        if ($password !== $password_confirm) {
            $errors['password_confirm'] = 'Konfirmasi kata sandi tidak cocok.';
        }
    }

    // Jika tidak ada error, lakukan pendaftaran
    if (empty($errors)) {
        // Enkripsi password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Query insert pengguna baru
        $insert_query = "INSERT INTO users (id_user, email, nama_lengkap, password, level) 
                         VALUES ('$id_user', '$email', '$nama_lengkap', '$hashed_password', '$level')";

        if (mysqli_query($koneksi, $insert_query)) {
            $_SESSION['success'] = "Pendaftaran berhasil, silakan masuk.";
            echo "<script>window.location.href = '" . base_url('auth/login.php') . "';</script>";
        } else {
            $_SESSION['error'] = 'Terjadi kesalahan saat mendaftar, silakan coba lagi.';
            echo "<script>window.location.href = '" . base_url('auth/register.php') . "';</script>";
        }
        exit();
    } else {
        // Simpan error dan form_data dalam session
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $form_data;

        // Redirect ke halaman register dengan mengirim error
        echo "<script>window.location.href = '" . base_url('auth/register.php') . "';</script>";
        exit();
    }
}

// Create admin account
// Hanya muncul jika tidak ada admin di tabel user
if (isset($_POST['create-admin-account'])) {
    $id_user            = uniqid();
    $nama_lengkap       = "Administrator";
    $email              = "admin@gmail.com";
    $password           = "admin";
    $hashed_password    = password_hash($password, PASSWORD_DEFAULT);
    $level              = "Admin";

    $buat_akun = mysqli_query($koneksi, "INSERT INTO users VALUES('$id_user', '$nama_lengkap', '$email', '$hashed_password', '$level')");

    if ($buat_akun) {
        $_SESSION['success'] = "<small>Akun berhasil dibuat, silakan masuk dengan email '$email' dan kata sandi '$password'.</small>";
    } else {
        $_SESSION['error'] = "Akun gagal dibuat, silakan coba kembali!";
    }

    echo "<script>window.location.href = '" . base_url('auth/login.php') . "';</script>";
    exit();
}

// Cek auth
if (isset($_SESSION['email']) && isset($_SESSION['level'])) {
    // Update profile
    if (isset($_GET['update-profile'])) {
        $errors = [];

        $id_user = $_GET['update-profile'];
        $email = $_POST['email'];
        $nama_lengkap = $_POST['nama_lengkap'];
        $password = $_POST['password']; // Password lama
        $new_password = $_POST['new_password']; // Kata sandi baru
        $new_password_confirm = $_POST['new_password_confirm']; // Konfirmasi kata sandi baru

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

        // Validasi kata sandi baru jika kata sandi lama diisi
        if (!empty($password)) {
            // Cek apakah kata sandi lama benar
            $sql = "SELECT password FROM users WHERE id_user = '$id_user'";
            $query = mysqli_query($koneksi, $sql);
            $result = mysqli_fetch_assoc($query);

            if (!$result || !password_verify($password, $result['password'])) {
                $errors['password'] = 'Kata sandi lama salah.';
            } else {
                // Validasi kata sandi baru
                if (empty($new_password)) {
                    $errors['new_password'] = 'Kata sandi baru wajib diisi.';
                } elseif (strlen($new_password) < 8) {
                    $errors['new_password'] = 'Kata sandi baru minimal 8 karakter.';
                } elseif ($new_password !== $new_password_confirm) {
                    $errors['new_password_confirm'] = 'Konfirmasi kata sandi baru tidak cocok.';
                }
            }
        }

        // Jika tidak ada error, lakukan update
        if (empty($errors)) {
            // Update profil pengguna
            $update_query = "UPDATE users SET email = '$email', nama_lengkap = '$nama_lengkap'";

            // Jika kata sandi baru diisi, update kata sandi
            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_query .= ", password = '$hashed_password'";
            }

            $update_query .= " WHERE id_user = '$id_user'";
            mysqli_query($koneksi, $update_query);

            // Perbarui session
            $_SESSION['email'] = $email;
            $_SESSION['nama_lengkap'] = $nama_lengkap;

            // Redirect berdasarkan level pengguna
            if ($_SESSION['level'] === 'Admin' && $_SESSION['level'] === 'Cashier') {
                $_SESSION['success'] = "Profil berhasil diperbarui";
                echo "<script>window.location.href = '" . base_url('dashboard/manage-user/profile.php') . "';</script>";
            } else {
                $_SESSION['toast-success'] = "Profil berhasil diperbarui";
                echo "<script>window.location.href = '" . base_url('user/profile.php') . "';</script>";
            }
            exit();
        } else {
            $_SESSION['errors'] = $errors;

            // Redirect berdasarkan level pengguna
            if ($_SESSION['level'] === 'Admin' && $_SESSION['level'] === 'Cashier') {
                echo "<script>window.location.href = '" . base_url('dashboard/manage-user/profile.php') . "';</script>";
            } else {
                echo "<script>window.location.href = '" . base_url('user/profile.php') . "';</script>";
            }
            exit();
        }
    }
}
