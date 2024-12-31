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

// Judul Halaman
$title = 'Kelola Pengguna';

ob_start(); // Start output buffering 
?>

<div class="pagetitle">
    <div class="d-flex justify-content-between align-items-center gap-2 mb-4">
        <h1 class="mb-0 fw-bold"><?= $title ?></h1>

        <div class="d-flex gap-2">
            <a href="<?= base_url('dashboard/manage-user/show.php'); ?>" class="btn btn-sm px-lg-3 btn-dark">Semua</a>

            <?php
            $role = $_GET['role'] ?? '';
            $sql_role_user = "SELECT DISTINCT level FROM users ORDER BY level ASC";
            $query_role_user = mysqli_query($koneksi, $sql_role_user);

            while ($data_role_user = mysqli_fetch_array($query_role_user)):
            ?>
                <a href="<?= base_url('dashboard/manage-user/role.php?role=' . urlencode($data_role_user['level'])); ?>" class="btn btn-sm px-lg-3 btn-light border-secondary">
                    <?php
                    if ($data_role_user['level'] === 'User') {
                        echo 'Pelanggan';
                    } elseif ($data_role_user['level'] === 'Cashier') {
                        echo 'Kasir';
                    } else {
                        echo 'Admin';
                    }
                    ?>
                </a>
            <?php endwhile; ?>
        </div>
    </div>
</div><!-- End Page Title -->

<section class="section">
    <div class="row">
        <div class="col-lg-12">

            <div class="card">
                <div class="card-body pt-4">
                    <!-- Alerts -->
                    <?php include('../components/alerts.php') ?>

                    <!-- Table with stripped rows -->
                    <table class="table table-hover datatable">
                        <thead class="table-light">
                            <tr>
                                <th>No.</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;

                            $sql = "SELECT * FROM users ORDER BY nama_lengkap ASC";

                            $query = mysqli_query($koneksi, $sql);

                            while ($data = mysqli_fetch_array($query)) :
                                if ($data['level'] === 'User') {
                                    $role = 'Pelanggan';
                                } elseif ($data['level'] === 'Cashier') {
                                    $role = 'Kasir';
                                } else {
                                    $role = 'Admin';
                                }
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $data['nama_lengkap'] ?></td>
                                    <td><?= $data['email'] ?></td>
                                    <td>
                                        <?php if ($data['level'] === 'User'): ?>
                                            <span class="badge bg-info"><?= $role ?></span>
                                        <?php elseif ($data['level'] === 'Cashier'): ?>
                                            <span class="badge bg-secondary"><?= $role; ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-primary"><?= $role; ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile ?>
                        </tbody>
                    </table>
                    <!-- End Table with stripped rows -->

                </div>
            </div>

        </div>
    </div>
</section>

<?php
$content = ob_get_clean(); // Get content and clean buffer

// Menyertakan layout
include('../layout.php');
?>