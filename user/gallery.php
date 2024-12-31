<?php
// Menyertakan konfigurasi
include('../config/config.php');

// Memulai sesi
session_start();

// Judul Halaman
$title = 'Galeri';

ob_start(); // Start output buffering 
?>

<!-- Single Page Header start -->
<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6">Galeri</h1>
</div>
<!-- Single Page Header End -->

<!-- Gallery Start-->
<div class="container">
    <div class="row g-4 mt-4">
        <div class="col-lg-12">
            <?php
            $limit = 6;
            $offset = 0;
            $sql = "SELECT * FROM galeri ORDER BY tanggal_buat DESC LIMIT $limit OFFSET $offset";
            $query = mysqli_query($koneksi, $sql);

            if (mysqli_num_rows($query) > 0):
            ?>
                <div class="row g-4 justify-content-center" id="gallery-container">
                    <?php while ($data = mysqli_fetch_array($query)): ?>
                        <div class="col-md-6 col-lg-4 gallery-item">
                            <div class="rounded position-relative fruite-item border border-secondary d-flex flex-column justify-content-between h-100">
                                <div class="fruite-img">
                                    <img src="<?= get_image_url(base_url('assets/uploads/gallery/' . $data['gambar'])); ?>" loading="lazy" class="w-100 rounded-top" height="350" style="object-fit: cover;" alt="<?= $data['keterangan']; ?>">
                                </div>
                                <div class="p-4 rounded-bottom">
                                    <h5 class="text-center"><?= $data['keterangan']; ?></h5>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-center align-items-center mt-5">
                            <button id="load-more" class="btn btn-lg btn-secondary text-white">Lihat Lainnya</button>
                        </div>
                    </div>
                </div>
            <?php
            else:
                include('components/data-not-found.php');
            endif
            ?>
        </div>
    </div>
</div>
<!-- Gallery End-->

<script src="<?= base_url('assets/user/lib/jquery/jquery.min.js'); ?>"></script>
<script>
    $(document).ready(function() {
        let offset = 6; // Sudah ada 6 data awal

        $("#load-more").click(function() {
            $.ajax({
                url: '<?= base_url('user/gallery-load-more.php'); ?>',
                method: 'POST',
                data: {
                    offset: offset
                },
                success: function(response) {
                    if (response.trim() === 'done') {
                        // Jika respons adalah 'done', sembunyikan tombol
                        $("#load-more").hide();
                        // Tampilkan pesan "Semua Telah Ditampilkan"
                        $("#gallery-container").append('<h5 class="fw-semibold text-center mt-5 mb-0">Semua Data Telah Ditampilkan</h5>');
                    } else {
                        // Tambahkan data baru ke container
                        $("#gallery-container").append(response);
                        // Tambah nilai offset untuk request berikutnya
                        offset += 6;
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error loading more data: ", error);
                }
            });
        });
    });
</script>

<?php
$content = ob_get_clean(); // Get content and clean buffer

// Menyertakan layout
include('layout.php');
?>