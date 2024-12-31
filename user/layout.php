<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Pan & Co. | <?= $title; ?></title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?= base_url('assets/uploads/static/logo-square.jpg'); ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/uploads/static/logo-square.jpg'); ?>">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap"
        rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="<?= base_url('assets/user/lib/lightbox/css/lightbox.min.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('assets/user/lib/owlcarousel/assets/owl.carousel.min.css'); ?>" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="<?= base_url('assets/user/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('assets/user/lib/dataTables/css/dataTables.bootstrap5.css'); ?>" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="<?= base_url('assets/user/css/style.css'); ?>" rel="stylesheet">
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner"
        class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50  d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>
    <!-- Spinner End -->

    <!-- Navbar start -->
    <div class="container-fluid fixed-top">
        <div class="container topbar bg-white d-none d-lg-block border-bottom border-primary">
            <div class="d-flex justify-content-between">
                <div class="top-info">
                    <img src="<?= base_url('assets/uploads/static/logo.jpg'); ?>" alt="Logo" class="logo" height="20rem">
                </div>
                <div class="top-link">
                    <div class="d-flex flex-row gap-4">
                        <a href="https://maps.app.goo.gl/B3zsGhUTDXbu7Q7A6" target="_blank"
                            class="text-secondary small"><i class="fas fa-map-marker-alt me-1 text-secondary"></i>
                            Lombok Epicentrum Mall GF 29-30</a>
                        <a href="https://www.instagram.com/panco.lombok" target="_blank" class="text-secondary small"><i
                                class="fab fa-instagram me-1 text-secondary"></i>
                            @panco.lombok</a>
                        <a href="https://wa.me/6282146816611" target="_blank" class="text-secondary small"><i
                                class="fab fa-whatsapp me-1 text-secondary"></i>
                            +62 821-4681-6611</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="container px-0">
            <nav class="navbar navbar-light bg-white navbar-expand-xl">
                <a href="<?= base_url(); ?>" class="navbar-brand">
                    <div class="d-flex align-items-center gap-2">
                        <img src="<?= base_url('assets/uploads/static/logo.jpg'); ?>" alt="Logo" class="logo d-lg-none"
                            height="38rem">
                        <h1 class="text-secondary display-6 d-none d-lg-block">Pan & Co.</h1>
                    </div>
                </a>
                <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars text-secondary"></span>
                </button>
                <div class="collapse navbar-collapse bg-white" id="navbarCollapse">
                    <div class="navbar-nav mx-auto text-center">
                        <a href="<?= base_url(); ?>" class="nav-item nav-link <?= ($title === 'Beranda') ? 'active' : ''; ?>">Beranda</a>
                        <a href="<?= base_url('user/menu.php'); ?>" class="nav-item nav-link <?= ($title === 'Menu') ? 'active' : ''; ?>">Menu</a>
                        <a href="<?= base_url('user/gallery.php'); ?>" class="nav-item nav-link <?= ($title === 'Galeri') ? 'active' : ''; ?>">Galeri</a>

                        <?php if (isset($_SESSION['email']) && isset($_SESSION['level']) && ($_SESSION['level'] === 'Admin' || $_SESSION['level'] === 'Cashier')): ?>
                            <a href="<?= base_url('dashboard/index.php'); ?>" class="nav-item nav-link">Dashboard</a>
                        <?php endif ?>
                    </div>
                    <div class="d-flex justify-content-center gap-2 m-3 me-xl-0 text-center">
                        <?php if (isset($_SESSION['email']) && isset($_SESSION['level'])): ?>
                            <div class="nav-item dropdown my-auto">
                                <a href="#" class="nav-link dropdown-toggle dropdown-no-caret" data-bs-toggle="dropdown">
                                    <i class="fa fa-bell fa-2x text-secondary"></i>
                                    <span class="position-absolute bg-primary text-white rounded-circle d-flex align-items-center justify-content-center px-1 notifications"
                                        style="top: 0; left: 28px; height: 24px; min-width: 24px;">
                                        0 <!-- Default count, will be updated by Ajax -->
                                    </span>
                                </a>
                                <div class="dropdown-menu m-0 bg-secondary rounded-0 notifications-list">
                                    <!-- Notifikasi akan muncul di sini -->
                                    <a href="#" class="dropdown-item text-dark text-center">
                                        <small>Tidak ada notifikasi</small>
                                    </a>
                                </div>
                            </div>
                            <a href="<?= base_url('user/cart.php'); ?>" class="position-relative my-auto">
                                <i class="fa fa-shopping-bag fa-2x text-secondary"></i>
                                <?php
                                $sql_jumlah_item = "SELECT id_keranjang FROM keranjang WHERE id_user='{$_SESSION['id_user']}' AND status='Belum Dipesan'";
                                $query_jumlah_item = mysqli_query($koneksi, $sql_jumlah_item);

                                $data_item = mysqli_num_rows($query_jumlah_item) ?? 0;
                                ?>
                                <span
                                    class="position-absolute bg-primary text-white rounded-circle d-flex align-items-center justify-content-center px-1"
                                    style="top: -8px; left: 16px; height: 24px; min-width: 24px;">
                                    <?= $data_item ?>
                                </span>
                            </a>
                            <div class="nav-item dropdown my-auto">
                                <a href="#" class="nav-link dropdown-toggle dropdown-no-caret" data-bs-toggle="dropdown">
                                    <div class="avatar"><?= strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)); ?></div>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end m-0 bg-secondary rounded-0">
                                    <a href="<?= base_url('user/profile.php'); ?>" class="dropdown-item text-dark <?= ($title === 'Profil') ? 'bg-secondary' : ''; ?>">
                                        <i class="dropdown-icons fa fa-user me-2"></i> Profil
                                    </a>
                                    <a href="<?= base_url('user/order.php'); ?>" class="dropdown-item text-dark <?= ($title === 'Riwayat Pesanan') ? 'bg-secondary' : ''; ?>">
                                        <i class=" dropdown-icons fa fa-history me-2"></i> Riwayat Pesanan
                                    </a>
                                    <a href="#" class="dropdown-item text-dark" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                        <i class="dropdown-icons fa fa-sign-out-alt me-2"></i> Keluar
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <a href="<?= base_url('auth/login.php'); ?>" class="my-auto btn btn-outline-secondary btn-login">Masuk</a>
                            <a href="<?= base_url('auth/register.php'); ?>" class="my-auto btn btn-secondary text-white btn-login">Daftar</a>
                        <?php endif; ?>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->

    <!-- content -->
    <?= $content; ?>

    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-white-50 footer mt-5">
        <div class="container py-4">
            <div class="row align-items-center g-4">
                <div class="col-lg-3">
                    <div class="d-flex justify-content-center justify-content-lg-start">
                        <a class="btn  btn-outline-secondary me-2 btn-md-square rounded-circle" target="_blank" href="https://maps.app.goo.gl/B3zsGhUTDXbu7Q7A6"><i
                                class="fas fa-map-marker-alt"></i></a>
                        <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" target="_blank" href="https://www.instagram.com/panco.lombok"><i
                                class="fab fa-instagram"></i></a>
                        <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" target="_blank" href="https://wa.me/6282146816611"><i
                                class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <span class="text-light"><i class="fas fa-copyright me-1"></i> <?= date('Y'); ?> Pan & Co. Hak cipta dilindungi.</span>
                </div>
                <div class="col-lg-3 text-center text-lg-end">
                    <span class="text-light">Desain oleh <a class="border-bottom text-secondary" href="https://www.instagram.com/opi___11/">Novi</a></span>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- Toast -->
    <?php include('components/toasts.php') ?>

    <?php if (isset($_SESSION['email']) && isset($_SESSION['level'])): ?>
        <!-- Logout modal -->
        <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="logoutModalLabel">Keluar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?= $_SESSION['nama_lengkap']; ?>, apakah Anda yakin ingin mengakhiri sesi?
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                        <a href="<?= base_url('auth/logout.php'); ?>" class="btn btn-secondary text-white px-4">Ya</a>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Menu modal -->
        <div class="modal fade" id="menuModal" tabindex="-1" aria-labelledby="menuModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="menuModalLabel">Pemberitahuan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Silakan "Masuk" untuk menambahkan menu ke keranjang.
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                        <a href="<?= base_url('auth/login.php'); ?>" class="btn btn-secondary text-white">Masuk</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif ?>

    <!-- Back to Top -->
    <a href="#" class="btn btn-secondary border-3 border-secondary text-white rounded-circle back-to-top"><i
            class="fa fa-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="<?= base_url('assets/user/lib/jquery/jquery.min.js'); ?>"></script>
    <script src="<?= base_url('assets/user/js/bootstrap.bundle.min.js'); ?>"></script>
    <script src="<?= base_url('assets/user/lib/dataTables/js/dataTables.js'); ?>"></script>
    <script src="<?= base_url('assets/user/lib/dataTables/js/dataTables.bootstrap5.js'); ?>"></script>
    <script src="<?= base_url('assets/user/lib/easing/easing.min.js'); ?>"></script>
    <script src="<?= base_url('assets/user/lib/waypoints/waypoints.min.js'); ?>"></script>
    <script src="<?= base_url('assets/user/lib/lightbox/js/lightbox.min.js'); ?>"></script>
    <script src="<?= base_url('assets/user/lib/owlcarousel/owl.carousel.min.js'); ?>"></script>

    <!-- Template Javascript -->
    <script src="<?= base_url('assets/user/js/main.js'); ?>"></script>
    <script>
        $(document).ready(function() {
            // Fungsi untuk memeriksa notifikasi baru setiap 5 detik
            function checkNotifications() {
                $.ajax({
                    url: '<?= base_url('user/get-notifications.php'); ?>', // Ganti dengan URL endpoint PHP yang sesuai
                    method: 'GET',
                    dataType: 'json', // Mengharapkan data dalam format JSON
                    success: function(response) {
                        if (response.error) {
                            console.log(response.error); // Jika ada error (misalnya unauthorized), tampilkan pesan
                            return;
                        }

                        var newNotificationCount = response.jumlah_notifikasi;
                        $('.notifications').text(newNotificationCount); // Update jumlah notifikasi di ikon

                        // Menampilkan dropdown notifikasi jika ada
                        if (newNotificationCount > 0) {
                            $('.notifications').show();
                        } else {
                            $('.notifications').hide();
                        }

                        // Reset dan loop untuk menampilkan notifikasi di dropdown
                        var notificationsList = $('.notifications-list');
                        notificationsList.empty(); // Kosongkan daftar notifikasi sebelumnya

                        if (newNotificationCount > 0) {
                            // Looping melalui setiap notifikasi
                            $.each(response.notifications, function(index, notification) {
                                var statusBadge = '';

                                // Tentukan badge berdasarkan status
                                switch (notification.status) {
                                    case 'Pending':
                                        statusBadge = 'Status: <span class="badge bg-warning">Pending</span>';
                                        break;
                                    case 'Confirmed':
                                        statusBadge = 'Status: <span class="badge bg-success">Confirmed</span>';
                                        break;
                                    case 'In Progress':
                                        statusBadge = 'Status: <span class="badge bg-info">In Progress</span>';
                                        break;
                                    case 'Completed':
                                        statusBadge = 'Status: <span class="badge bg-primary">Completed</span>';
                                        break;
                                    case 'Cancelled':
                                        statusBadge = 'Status: <span class="badge bg-danger">Cancelled</span>';
                                        break;
                                }

                                // Tambahkan notifikasi ke dropdown
                                notificationsList.append(
                                    '<a href="order-detail.php?order-id=' + notification.id_pesanan + '" class="dropdown-item text-dark">' +
                                    '<div class=" mb-2">ID Pesanan: <span class="fw-bold">' + notification.id_pesanan + '</span></div>' +
                                    statusBadge +
                                    '</a><hr>'
                                );
                            });
                        } else {
                            notificationsList.append('<a href="#" class="dropdown-item text-dark text-center"><small>Tidak ada notifikasi</small></a>');
                        }
                    },
                    error: function() {
                        console.log('Error checking notifications');
                    }
                });
            }

            // Panggil fungsi checkNotifications pertama kali dan kemudian setiap 5 detik
            checkNotifications();
            setInterval(checkNotifications, 5000); // Setiap 5 detik
        });
    </script>
</body>

</html>