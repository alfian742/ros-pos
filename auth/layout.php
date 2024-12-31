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

    <!-- Template Main CSS File -->
    <link href="<?= base_url('assets/dashboard/css/style.css'); ?>" rel="stylesheet">
</head>

<body>

    <main>
        <div class="container">

            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

                            <div class="d-flex justify-content-center py-4">
                                <a href="index.html" class="logo d-flex align-items-center w-auto">
                                    <img src="<?= base_url('assets/uploads/static/logo.jpg'); ?>" alt="Logo" class="brand-img d-block mx-auto">
                                    <!-- <span class="d-none d-lg-block">Pan & Co.</span> -->
                                </a>
                            </div><!-- End Logo -->

                            <!-- content -->
                            <?= $content; ?>

                        </div>
                    </div>
                </div>

            </section>

        </div>
    </main><!-- End #main -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="<?= base_url('assets/dashboard/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>

    <!-- Template Main JS File -->
    <script src="<?= base_url('assets/dashboard/js/main.js'); ?>"></script>
</body>

</html>