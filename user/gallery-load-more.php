<?php
include('../config/config.php');

$limit = 6;
$offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;

$sql = "SELECT * FROM galeri ORDER BY tanggal_buat DESC LIMIT $limit OFFSET $offset";
$query = mysqli_query($koneksi, $sql);

if (mysqli_num_rows($query) > 0) :
?>
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
<?php
else :
    echo 'done'; // Kirimkan string 'done' jika tidak ada lagi data yang tersisa
endif
?>