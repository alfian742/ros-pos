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

// Judul Halaman
$title = 'Keranjang';

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
        <?php
        $id_user = $_SESSION['id_user'];

        $sql_keranjang = "SELECT * FROM keranjang INNER JOIN menu 
                          ON keranjang.id_menu=menu.id_menu
                          WHERE keranjang.id_user='$id_user' 
                          AND keranjang.status='Belum Dipesan' 
                          ORDER BY keranjang.id_keranjang ASC";
        $query_keranjang = mysqli_query($koneksi, $sql_keranjang);

        if (mysqli_num_rows($query_keranjang) > 0):
        ?>
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
                                                        Hapus <?= $data_keranjang['nama_menu']; ?> dari keranjang?
                                                    </div>
                                                    <div class="modal-footer border-0">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tidak</button>
                                                        <a href="<?= base_url('user/action.php?delete-cart=' . $data_keranjang['id_keranjang']); ?>" class="btn btn-secondary text-white px-4">Ya</a>
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
                                <div class="d-flex justify-content-between border-bottom pb-4 mb-4">
                                    <!-- Menampilkan total belanja -->
                                    <h5 class="mb-0 me-4">Total Belanja:</h5>
                                    <p class="mb-0" id="total_belanja"></p>
                                </div>

                                <div class="mb-4">
                                    <label class="h5 mb-3" for="tipe">Tipe Pesanan:</label>
                                    <select class="form-select" id="tipe" name="tipe" required>
                                        <option value="" disabled selected>-- Pilih Tipe Pesanan --</option>
                                        <option value="Dine In">Dine In</option>
                                        <option value="Takeaway">Takeaway</option>
                                    </select>
                                </div>

                                <div class="mb-4 d-none" id="nomorMejaContainer">
                                    <label class="h5 mb-3" for="nomor_meja">Nomor Meja:</label>
                                    <select class="form-select" id="nomor_meja" name="nomor_meja">
                                        <option value="" selected>-- Pilih Nomor Meja --</option>
                                        <option value="1">Meja 1</option>
                                        <option value="2">Meja 2</option>
                                        <option value="3">Meja 3</option>
                                        <option value="4">Meja 4</option>
                                        <option value="5">Meja 5</option>
                                        <option value="6">Meja 6</option>
                                        <option value="7">Meja 7</option>
                                        <option value="8">Meja 8</option>
                                        <option value="9">Meja 9</option>
                                        <option value="10">Meja 10</option>
                                        <option value="11">Meja 11</option>
                                        <option value="12">Meja 12</option>
                                        <option value="13">Meja 13</option>
                                        <option value="14">Meja 14</option>
                                        <option value="15">Meja 15</option>
                                    </select>
                                </div>

                                <p class="border-top pt-4 mt-4"><span class="text-danger">*</span> Pastikan pesanan Anda sudah benar.</p>
                            </div>
                            <!-- Tombol untuk memproses pesanan -->
                            <button type="submit" name="pesan_sekarang" class="btn border-secondary rounded-pill px-4 py-3 text-primary text-uppercase mb-4 ms-4" disabled>
                                Pesan Sekarang
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
            document.querySelector('button[name="pesan_sekarang"]').disabled = disablePesanButton;
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


        // Event listener untuk tipe pesanan
        document.getElementById('tipe').addEventListener('change', function() {
            const nomorMejaContainer = document.getElementById('nomorMejaContainer');
            const nomorMejaSelect = document.getElementById('nomor_meja');

            if (this.value === 'Dine In') {
                nomorMejaContainer.classList.remove('d-none');
                nomorMejaSelect.required = true; // Menambahkan required
            } else {
                nomorMejaContainer.classList.add('d-none');
                nomorMejaSelect.required = false; // Menghapus required
            }
        });
    });
</script>

<?php
$content = ob_get_clean(); // Get content and clean buffer

// Menyertakan layout
include('layout.php');
?>