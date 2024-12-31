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
    <!-- content -->
    <?= $content; ?>

    <!-- Vendor JS Files -->
    <script src="<?= base_url('assets/dashboard/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
</body>

</html>