<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Pan & Co. | <?= $title; ?></title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?= base_url('assets/uploads/static/logo-square.jpg'); ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/uploads/static/logo-square.jpg'); ?>">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="<?= base_url('assets/dashboard/vendor/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('assets/dashboard/vendor/bootstrap-icons/bootstrap-icons.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('assets/dashboard/vendor/boxicons/css/boxicons.min.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('assets/dashboard/vendor/quill/quill.snow.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('assets/dashboard/vendor/quill/quill.bubble.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('assets/dashboard/vendor/remixicon/remixicon.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('assets/dashboard/vendor/simple-datatables/style.css'); ?>" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="<?= base_url('assets/dashboard/css/style.css'); ?>" rel="stylesheet">
</head>

<body>
    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">
        <div class="d-flex align-items-center justify-content-between">
            <a href="<?= base_url('dashboard/index.php'); ?>" class="logo d-flex align-items-center">
                <img src="<?= base_url('assets/uploads/static/logo.png'); ?>" alt="Logo" height="40" class="brand-img d-block mx-auto">
                <!-- <span class="d-none d-lg-block">Pan & Co.</span> -->
            </a>
            <i class="bi bi-list toggle-sidebar-btn"></i>
        </div><!-- End Logo -->

        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">
                <li class="nav-item dropdown">
                    <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-bell"></i>
                        <span class="badge bg-primary badge-number">0</span>
                    </a><!-- End Notification Icon -->

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
                        <li class="dropdown-header">Tidak ada notifikasi</li>
                    </ul><!-- End Notification Dropdown Items -->
                </li><!-- End Notification Nav -->

                <li class="nav-item dropdown pe-3">

                    <a class="nav-link nav-profile d-flex align-items-center pe-2" href="#" data-bs-toggle="dropdown">
                        <div class="avatar"><?= strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)); ?></div>
                    </a><!-- End Profile Iamge Icon -->

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                        <li class="dropdown-header">
                            <h6><?= $_SESSION['nama_lengkap']; ?></h6>
                            <span><?= ($_SESSION['level'] === 'Cashier') ? 'Kasir' : 'Admin'; ?></span>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="<?= base_url('dashboard/manage-user/profile.php'); ?>">
                                <i class="bi bi-person"></i>
                                <span>Profil</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Keluar</span>
                            </a>
                        </li>

                    </ul><!-- End Profile Dropdown Items -->
                </li><!-- End Profile Nav -->

            </ul>
        </nav><!-- End Icons Navigation -->
    </header><!-- End Header -->

    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">
        <ul class="sidebar-nav" id="sidebar-nav">

            <li class="nav-item">
                <a class="nav-link <?= ($title === 'Dashboard') ? '' : 'collapsed'; ?>" href="<?= base_url('dashboard/index.php'); ?>">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= ($title === 'POS') ? '' : 'collapsed'; ?>" href="<?= base_url('dashboard/pos/show.php'); ?>">
                    <i class="bi bi-wallet"></i>
                    <span>POS</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= (($title === 'Pesanan Kasir') || ($title === 'Pesanan Pelanggan')) ? '' : 'collapsed'; ?>" data-bs-target="#orders-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-journals"></i><span>Pesanan</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="orders-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="<?= base_url('dashboard/order-cashier/show.php'); ?>" class="<?= ($title === 'Pesanan Kasir') ? 'active' : ''; ?>">
                            <i class="bi bi-circle"></i><span>Kasir</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('dashboard/order-user/show.php'); ?>" class="<?= ($title === 'Pesanan Pelanggan') ? 'active' : ''; ?>">
                            <i class="bi bi-circle"></i><span>Pelanggan</span>
                        </a>
                    </li>
                </ul>
            </li>

            <?php if ($_SESSION['level'] === 'Admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= ($title === 'Menu') ? '' : 'collapsed'; ?>" href="<?= base_url('dashboard/menu/show.php'); ?>">
                        <i class="bi bi-book"></i>
                        <span>Menu</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($title === 'Galeri') ? '' : 'collapsed'; ?>" href="<?= base_url('dashboard/gallery/show.php'); ?>">
                        <i class="bi bi-images"></i>
                        <span>Galeri</span>
                    </a>
                </li>
            <?php endif ?>

            <li class="nav-item">
                <a class="nav-link <?= ($title === 'Laporan') ? '' : 'collapsed'; ?>" href="<?= base_url('dashboard/report/show.php'); ?>">
                    <i class="bi bi-file-earmark"></i>
                    <span>Laporan</span>
                </a>
            </li>

            <?php if ($_SESSION['level'] === 'Admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= ($title === 'Kelola Pengguna') ? '' : 'collapsed'; ?>" href="<?= base_url('dashboard/manage-user/show.php'); ?>">
                        <i class="bi bi-people"></i>
                        <span>Kelola Pengguna</span>
                    </a>
                </li>
            <?php endif ?>
        </ul>
    </aside><!-- End Sidebar-->

    <main id="main" class="main">

        <!-- content -->
        <?= $content; ?>

    </main><!-- End #main -->

    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer">
        <div class="d-flex justify-content-between align-items-center px-5">
            <div class="fw-semibold">
                &copy; <?= date('Y'); ?> Pan & Co. Hak cipta dilindungi.
            </div>
            <div class="fw-semibold me-4">
                Desain oleh <a class="border-bottom text-primary" href="#">Alfian</a>
            </div>
        </div>
    </footer><!-- End Footer -->

    <!-- Logout modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered border-0">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h1 class="modal-title fs-5" id="logoutModalLabel">Keluar</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?= $_SESSION['nama_lengkap']; ?>, apakah Anda yakin ingin mengakhiri sesi?
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a href="<?= base_url('auth/logout.php'); ?>" class="btn btn-primary px-4">Ya</a>
                </div>
            </div>
        </div>
    </div>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="<?= base_url('assets/dashboard/vendor/jquery/jquery.min.js'); ?>"></script>
    <script src="<?= base_url('assets/dashboard/vendor/apexcharts/apexcharts.min.js'); ?>"></script>
    <script src="<?= base_url('assets/dashboard/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
    <script src="<?= base_url('assets/dashboard/vendor/quill/quill.js'); ?>"></script>
    <script src="<?= base_url('assets/dashboard/vendor/simple-datatables/simple-datatables.js'); ?>"></script>

    <!-- Template Main JS File -->
    <script src="<?= base_url('assets/dashboard/js/main.js'); ?>"></script>
    <script>
        $(document).ready(function() {
            // Fungsi untuk mengambil data notifikasi
            function fetchNotifications() {
                $.ajax({
                    url: '<?= base_url('dashboard/get-notifications.php'); ?>',
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data.error) {
                            console.error(data.error);
                            return;
                        }

                        // Update jumlah notifikasi
                        if (data.jumlah_item > 99) {
                            $('.badge-number').text('99+');
                        } else {
                            $('.badge-number').text(data.jumlah_item);
                        }

                        // Update daftar notifikasi
                        let notificationList = '';
                        if (data.jumlah_item > 0) {
                            if (data.jumlah_item > 99) {
                                notificationList += `<li class="dropdown-header">Ada 99+ pesanan baru</li>`;
                            } else {
                                notificationList += `<li class="dropdown-header">Ada ${data.jumlah_item} pesanan baru</li>`;
                            }

                            notificationList += '<li><hr class="dropdown-divider"></li>';
                            data.notifications.slice(0, 4).forEach(item => {
                                notificationList += `
                                    <li class="notification-item">
                                        <i class="bi bi-exclamation-circle text-warning"></i>
                                        <a href="<?= base_url('dashboard/order-user/detail.php?order-id='); ?>${item.id_pesanan}">
                                            <h4>${item.id_pesanan}</h4>
                                            <p>${item.nama_lengkap}</p>
                                            <p>${item.tanggal}</p>
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>`;
                            });
                            notificationList += `<li class="dropdown-footer">
                            <a href="<?= base_url('dashboard/order-user/status.php?status=Pending'); ?>">Lihat Semua Pesanan</a>
                        </li>`;
                        } else {
                            notificationList += `<li class="dropdown-header">Tidak ada notifikasi</li>`;
                        }
                        $('.notifications').html(notificationList);
                    },
                    error: function(error) {
                        console.error('Gagal mengambil data notifikasi', error);
                    }
                });
            }

            // Memanggil fungsi fetchNotifications pertama kali
            fetchNotifications();

            // Memanggil fungsi fetchNotifications setiap 5 detik
            setInterval(fetchNotifications, 5000);
        });
    </script>
</body>

</html>