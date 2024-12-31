<?php
// Menyertakan konfigurasi
include('../config/config.php');

// Memulai sesi
session_start();

// Cek auth
if (!isset($_SESSION['email']) && !isset($_SESSION['level'])) {
    $_SESSION['error'] = "Maaf, Anda harus masuk terlebih dahulu";
    echo "<script>window.location.href = '" . base_url('auth/login.php') . "';</script>";
    exit();
} elseif ($_SESSION['level'] === 'Admin' || $_SESSION['level'] === 'Cashier') {
    echo "<script>window.location.href = '" . base_url('dashboard/pos/show.php') . "';</script>";
    exit();
}

$id_user = $_SESSION['id_user'];
$id_pesanan = $_GET['order-id'];

if ($_SESSION['level'] === 'Admin' || $_SESSION['level'] === 'Cashier') {
    echo "<script>window.location.href = '" . base_url('dashboard/order-cashier/detail.php?order-id=' . $id_pesanan) . "';</script>";
    exit();
}

$sql_keranjang = "SELECT * FROM keranjang INNER JOIN menu 
                  ON keranjang.id_menu=menu.id_menu
                  WHERE keranjang.id_pesanan='$id_pesanan'
                  AND keranjang.id_user='$id_user' 
                  ORDER BY keranjang.id_keranjang ASC";
$query_keranjang = mysqli_query($koneksi, $sql_keranjang);

// Judul Halaman
$title = 'Edit Pesanan';

ob_start(); // Start output buffering 
?>

<!-- Single Page Header start -->
<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6"><?= $title; ?></h1>
</div>
<!-- Single Page Header End -->

<!-- Cart Page Start -->
<div class="container-fluid py-5">
    <div class="container">
        <div class="d-flex justify-content-between gap-2 mb-4">
            <a href="<?= base_url('user/order-detail.php?order-id=' . $id_pesanan); ?>" class="btn btn-outline-secondary rounded-pill"><i class="fas fa-arrow-left me-2"></i> Detail Pesanan</a>
        </div>

        <?php if (mysqli_num_rows($query_keranjang) > 0): ?>
            <form method="post" action="<?= base_url('user/action.php'); ?>">
                <div class="table-responsive">
                    <table class="table table-hover" style="white-space: nowrap !important;">
                        <thead>
                            <tr>
                                <th scope="col">Produk</th>
                                <th scope="col">Nama Menu</th>
                                <th scope="col">Harga</th>
                                <th scope="col">Jumlah</th>
                                <th scope="col">Sub Total</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($data_keranjang = mysqli_fetch_array($query_keranjang)): ?>
                                <tr>
                                    <th scope="row">
                                        <div class="d-flex align-items-center">
                                            <img src="<?= get_image_url(base_url('assets/uploads/menu/' . $data_keranjang['gambar'])); ?>" class="rounded-circle" style="width: 80px; height: 80px;" alt="<?= $data_keranjang['nama_menu']; ?>">
                                        </div>
                                    </th>
                                    <td>
                                        <p class="mb-0 mt-4"><?= $data_keranjang['nama_menu']; ?></p>
                                    </td>
                                    <td>
                                        <p class="mb-0 mt-4"><?= 'Rp ' . number_format($data_keranjang['harga'], 0, ',', '.'); ?></p>
                                    </td>
                                    <td>
                                        <div class="input-group quantity mt-4" style="width: 100px;">
                                            <!-- Tombol minus untuk mengurangi jumlah produk -->
                                            <div class="input-group-btn">
                                                <button type="button" class="btn btn-sm btn-minus rounded-circle bg-light border" data-id_menu="<?= $data_keranjang['id_menu']; ?>">
                                                    <i class="fa fa-minus"></i>
                                                </button>
                                            </div>

                                            <!-- Input jumlah produk -->
                                            <input type="text" class="form-control form-control-sm text-center border-0 bg-transparent jumlah-item"
                                                name="jumlah[<?= $data_keranjang['id_menu']; ?>]"
                                                value="<?= $data_keranjang['jumlah']; ?>"
                                                data-id_menu="<?= $data_keranjang['id_menu']; ?>"
                                                data-harga="<?= $data_keranjang['harga']; ?>"
                                                data-subtotal="<?= $data_keranjang['harga'] * $data_keranjang['jumlah']; ?>"
                                                readonly>

                                            <!-- Tombol plus untuk menambah jumlah produk -->
                                            <div class="input-group-btn">
                                                <button type="button" class="btn btn-sm btn-plus rounded-circle bg-light border" data-id_menu="<?= $data_keranjang['id_menu']; ?>">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="mb-0 mt-4 subtotal" id="subtotal_<?= $data_keranjang['id_menu']; ?>"></p>
                                    </td>
                                    <td>
                                        <!-- Button trigger delete modal -->
                                        <button type="button" class="btn btn-md rounded-circle bg-light border mt-4" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $data_keranjang['id_menu']; ?>">
                                            <i class="fa fa-times text-danger"></i>
                                        </button>

                                        <!-- Delete modal -->
                                        <div class="modal fade" id="deleteModal<?= $data_keranjang['id_menu']; ?>" tabindex="-1" aria-labelledby="deleteModal<?= $data_keranjang['id_menu']; ?>Label" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0">
                                                    <div class="modal-header border-0">
                                                        <h5 class="modal-title" id="deleteModal<?= $data_keranjang['id_menu']; ?>Label">Hapus Menu</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Hapus <?= $data_keranjang['nama_menu']; ?> dari pesanan?
                                                    </div>
                                                    <div class="modal-footer border-0">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tidak</button>
                                                        <a href="<?= base_url('user/action.php?delete-order-item=' . $data_keranjang['id_keranjang'] . '&&order-id=' . $id_pesanan); ?>" class="btn btn-secondary text-white px-4">Ya</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile ?>
                        </tbody>
                    </table>
                </div>

                <!-- Bagian untuk total belanja -->
                <div class="row g-4 justify-content-end mt-5">
                    <div class="col-8"></div>
                    <div class="col-sm-8 col-md-7 col-lg-6 col-xl-4">
                        <div class="bg-light rounded">
                            <div class="p-4">
                                <h1 class="display-6 mb-4"><span class="fw-normal">Belanja</span></h1>

                                <?php
                                $sql_pesanan = "SELECT * FROM pesanan WHERE id_pesanan = '$id_pesanan' AND id_user = '$id_user'";
                                $query_pesanan = mysqli_query($koneksi, $sql_pesanan);

                                $data_pesanan = mysqli_fetch_array($query_pesanan);
                                ?>

                                <div class="d-flex justify-content-between border-bottom pb-4 mb-4">
                                    <!-- Menampilkan total belanja -->
                                    <h5 class="mb-0 me-4">Total Belanja:</h5>
                                    <p class="mb-0" id="total_belanja"></p>
                                </div>

                                <input type="hidden" name="id_pesanan" value="<?= $data_pesanan['id_pesanan']; ?>">
                                <input type="hidden" name="total_pembayaran" value="<?= $data_pesanan['total_pembayaran']; ?>">

                                <div class="mb-4">
                                    <label class="h5 mb-3" for="tipe">Tipe Pesanan:</label>
                                    <select class="form-select" id="tipe" name="tipe" required>
                                        <option value="" disabled>-- Pilih Tipe Pesanan --</option>
                                        <option value="Dine In" <?= ($data_pesanan['tipe_pesanan'] === 'Dine In') ? 'selected' : ''; ?>>Dine In</option>
                                        <option value="Takeaway" <?= ($data_pesanan['tipe_pesanan'] === 'Takeaway') ? 'selected' : ''; ?>>Takeaway</option>
                                    </select>
                                </div>

                                <div class="mb-4 d-none" id="nomorMejaContainer">
                                    <label class="h5 mb-3" for="nomor_meja">Nomor Meja:</label>
                                    <select class="form-select" id="nomor_meja" name="nomor_meja">
                                        <option value="" disabled>-- Pilih Nomor Meja --</option>
                                        <option value="1" <?= ($data_pesanan['nomor_meja'] === 1) ? 'selected' : ''; ?>>Meja 1</option>
                                        <option value="2" <?= ($data_pesanan['nomor_meja'] === 2) ? 'selected' : ''; ?>>Meja 2</option>
                                        <option value="3" <?= ($data_pesanan['nomor_meja'] === 3) ? 'selected' : ''; ?>>Meja 3</option>
                                        <option value="4" <?= ($data_pesanan['nomor_meja'] === 4) ? 'selected' : ''; ?>>Meja 4</option>
                                        <option value="5" <?= ($data_pesanan['nomor_meja'] === 5) ? 'selected' : ''; ?>>Meja 5</option>
                                        <option value="6" <?= ($data_pesanan['nomor_meja'] === 6) ? 'selected' : ''; ?>>Meja 6</option>
                                        <option value="7" <?= ($data_pesanan['nomor_meja'] === 7) ? 'selected' : ''; ?>>Meja 7</option>
                                        <option value="8" <?= ($data_pesanan['nomor_meja'] === 8) ? 'selected' : ''; ?>>Meja 8</option>
                                        <option value="9" <?= ($data_pesanan['nomor_meja'] === 9) ? 'selected' : ''; ?>>Meja 9</option>
                                        <option value="10" <?= ($data_pesanan['nomor_meja'] === 10) ? 'selected' : ''; ?>>Meja 10</option>
                                        <option value="11" <?= ($data_pesanan['nomor_meja'] === 11) ? 'selected' : ''; ?>>Meja 11</option>
                                        <option value="12" <?= ($data_pesanan['nomor_meja'] === 12) ? 'selected' : ''; ?>>Meja 12</option>
                                        <option value="13" <?= ($data_pesanan['nomor_meja'] === 13) ? 'selected' : ''; ?>>Meja 13</option>
                                        <option value="14" <?= ($data_pesanan['nomor_meja'] === 14) ? 'selected' : ''; ?>>Meja 14</option>
                                        <option value="15" <?= ($data_pesanan['nomor_meja'] === 15) ? 'selected' : ''; ?>>Meja 15</option>
                                    </select>
                                </div>

                                <p class="border-top pt-4 mt-4"><span class="text-danger">*</span> Pastikan pesanan Anda sudah benar.</p>
                            </div>
                            <!-- Tombol untuk memproses pesanan -->
                            <button type="submit" name="edit_pesanan" class="btn border-secondary rounded-pill px-4 py-3 text-primary text-uppercase mb-4 ms-4" disabled>
                                Edit Pesanan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        <?php
        else:
            include('components/empty-cart.php');
        endif;
        ?>
    </div>
</div>
<!-- Cart Page End -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function updateSubtotalAndTotal() {
            let totalBelanja = 0;
            let disablePesanButton = false;

            // Menghitung subtotal dan total belanja
            document.querySelectorAll('.jumlah-item').forEach(input => {
                const harga = parseFloat(input.dataset.harga);
                const jumlah = parseInt(input.value);
                const id_menu = input.dataset.id_menu;
                const subtotal = harga * jumlah;

                // Update subtotal di tampilan
                document.getElementById('subtotal_' + id_menu).innerText = 'Rp ' + subtotal.toLocaleString('id-ID');

                // Tambahkan subtotal ke total belanja
                totalBelanja += subtotal;

                // Jika jumlah 0, disable tombol
                if (jumlah === 0) {
                    disablePesanButton = true;
                }
            });

            // Update total belanja di tampilan
            document.getElementById('total_belanja').innerText = 'Rp ' + totalBelanja.toLocaleString('id-ID');

            // Disable atau enable tombol Pesan Sekarang
            document.querySelector('button[name="edit_pesanan"]').disabled = disablePesanButton;
        }

        // Event listener untuk tombol minus
        document.querySelectorAll('.btn-minus').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.closest('.input-group').querySelector('.jumlah-item');
                let jumlah = Math.max(0, parseInt(input.value) - 1); // Pastikan jumlah tidak kurang dari 0
                input.value = jumlah;
                updateSubtotalAndTotal();
            });
        });

        // Event listener untuk tombol plus
        document.querySelectorAll('.btn-plus').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.closest('.input-group').querySelector('.jumlah-item');
                let jumlah = parseInt(input.value) + 1;
                input.value = jumlah;
                updateSubtotalAndTotal();
            });
        });

        // Inisialisasi subtotal dan total belanja saat halaman dimuat
        updateSubtotalAndTotal();

        // Event listener untuk perubahan jumlah item
        document.querySelectorAll('.jumlah-item').forEach(input => {
            input.addEventListener('change', updateSubtotalAndTotal);
        });


        const tipePesanan = document.getElementById('tipe');
        const nomorMejaContainer = document.getElementById('nomorMejaContainer');
        const nomorMejaSelect = document.getElementById('nomor_meja');

        // Fungsi untuk menangani perubahan tipe pesanan
        function handleTipePesananChange() {
            if (tipePesanan.value === 'Dine In') {
                nomorMejaContainer.classList.remove('d-none');
                nomorMejaSelect.required = true; // Menambahkan required
            } else {
                nomorMejaContainer.classList.add('d-none');
                nomorMejaSelect.required = false; // Menghapus required
            }
        }

        // Panggil fungsi saat halaman dimuat
        handleTipePesananChange();

        // Tambahkan event listener untuk perubahan dropdown
        tipePesanan.addEventListener('change', handleTipePesananChange);
    });
</script>

<?php
$content = ob_get_clean(); // Get content and clean buffer

// Menyertakan layout
include('layout.php');
?>