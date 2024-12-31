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
} elseif ($_SESSION['level'] !== 'Admin' && $_SESSION['level'] !== 'Cashier') {
    echo "<script>window.location.href = '" . base_url('error/403.php') . "';</script>";
    exit();
}

// Judul Halaman
$title = 'POS';

ob_start(); // Start output buffering 
?>

<div class="pagetitle">
    <div class="d-flex justify-content-between align-items-center gap-2 mb-4">
        <h1 class="mb-0 fw-bold"><?= $title ?></h1>
    </div>
</div><!-- End Page Title -->

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body pt-4">
                    <h3 class="mb-4 fw-bold">Menu</h3>

                    <!-- Table with stripped rows -->
                    <table class="table table-hover datatable" style="white-space: nowrap !important;">
                        <thead class="table-light">
                            <tr>
                                <th>Gambar</th>
                                <th>Nama Menu</th>
                                <th>Harga</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $id_user = $_SESSION['id_user'];

                            $no = 1;

                            $sql = "SELECT * FROM menu ORDER BY nama_menu ASC";

                            $query = mysqli_query($koneksi, $sql);

                            while ($data = mysqli_fetch_array($query)) :
                            ?>
                                <tr>
                                    <td>
                                        <img src="<?= get_image_url(base_url('assets/uploads/menu/' . $data['gambar'])); ?>" class="rounded-circle" style="width: 80px; height: 80px;" alt="<?= $data['nama_menu']; ?>" loading="lazy">
                                    </td>
                                    <td><?= $data['nama_menu'] ?></td>
                                    <td><?= 'Rp ' . number_format($data['harga'], 0, ',', '.') ?></td>
                                    <td><?= $data['kategori']; ?></td>
                                    <td>
                                        <span class="<?= $data['status'] === 'Tersedia' ? 'text-success' : 'text-danger'; ?>"><?= $data['status']; ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($data['status'] === 'Tersedia'): ?>
                                            <?php
                                            $sql_tambah_keranjang = "SELECT * FROM keranjang 
                                                                    WHERE id_user='$id_user' 
                                                                    AND id_menu='{$data['id_menu']}' 
                                                                    AND status='Belum Dipesan'";
                                            $query_tambah_keranjang = mysqli_query($koneksi, $sql_tambah_keranjang);

                                            if (mysqli_num_rows($query_tambah_keranjang) == 0):
                                            ?>
                                                <a href="<?= base_url('dashboard/pos/action.php?add-to-cart=' . $data['id_menu']); ?>" class="btn btn-success">
                                                    <i class="bi bi-bi bi-cart-plus"></i>
                                                </a>
                                            <?php else: ?>
                                                <h4 class="text-info mb-0"><i class="bi bi-cart-check-fill"></i></h4>
                                            <?php endif; ?>
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

        <div class="col-lg-12">
            <div class="card">
                <div class="card-body pt-4">
                    <h3 class="mb-4 fw-bold">Keranjang</h3>

                    <?php
                    $sql_keranjang = "SELECT * FROM keranjang INNER JOIN menu 
                                        ON keranjang.id_menu=menu.id_menu
                                        WHERE keranjang.id_user='$id_user' 
                                        AND keranjang.status='Belum Dipesan' 
                                        ORDER BY keranjang.id_keranjang ASC";
                    $query_keranjang = mysqli_query($koneksi, $sql_keranjang);

                    if (mysqli_num_rows($query_keranjang) > 0):
                    ?>
                        <form method="post" action="<?= base_url('dashboard/pos/action.php'); ?>">
                            <table class="table table-hover" style="white-space: nowrap !important;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Gambar</th>
                                        <th>Nama Menu</th>
                                        <th>Harga</th>
                                        <th>Jumlah</th>
                                        <th>Sub Total</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($data_keranjang = mysqli_fetch_array($query_keranjang)): ?>
                                        <tr>
                                            <th scope="row">
                                                <div class="d-flex align-items-center">
                                                    <img src="<?= get_image_url(base_url('assets/uploads/menu/' . $data_keranjang['gambar'])); ?>" class="rounded-circle" style="width: 80px; height: 80px;" alt="<?= $data_keranjang['nama_menu']; ?>" loading="lazy">
                                                </div>
                                            </th>
                                            <td>
                                                <p class="mb-0 mt-4"><?= $data_keranjang['nama_menu']; ?></p>
                                            </td>
                                            <td>
                                                <p class="mb-0 mt-4"><?= 'Rp ' . number_format($data_keranjang['harga'], 0, ',', '.'); ?></p>
                                            </td>
                                            <td>
                                                <div class="input-group quantity mt-4" style="width: 125px;">
                                                    <!-- Tombol minus untuk mengurangi jumlah produk -->
                                                    <div class="input-group-btn">
                                                        <button type="button" class="btn btn-sm btn-minus btn-outline-secondary" data-id_menu="<?= $data_keranjang['id_menu']; ?>">
                                                            <i class="bi bi-dash"></i>
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
                                                        <button type="button" class="btn btn-sm btn-plus btn-outline-secondary" data-id_menu="<?= $data_keranjang['id_menu']; ?>">
                                                            <i class="bi bi-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="mb-0 mt-4 subtotal" id="subtotal_<?= $data_keranjang['id_menu']; ?>"></p>
                                            </td>
                                            <td>
                                                <!-- Button trigger delete modal -->
                                                <button type="button" class="btn btn-md btn-danger mt-4" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $data_keranjang['id_menu']; ?>">
                                                    <i class="bi bi-cart-x"></i>
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
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                                                                <a href="<?= base_url('dashboard/pos/action.php?delete-cart=' . $data_keranjang['id_keranjang']); ?>" class="btn btn-primary text-white px-4">Ya</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile ?>
                                </tbody>
                            </table>

                            <!-- Bagian untuk total belanja -->
                            <div class="row g-4 justify-content-end mt-5">
                                <div class="col-lg-4">
                                    <div class="bg-light rounded">
                                        <div class="p-4">
                                            <div class="d-flex flex-column border-bottom pb-4 mb-4">
                                                <!-- Menampilkan nama kasir -->
                                                <h6 class="mb-3">Nama Admin/Kasir:</h6>
                                                <h5 class="mb-0"><?= $_SESSION['nama_lengkap'] ?></h5>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <!-- Menampilkan metode pembayaran -->
                                                <h6 class="mb-0 me-4">Tanggal</h6>
                                                <p class="mb-0"><?= date('d-m-Y H:i'); ?></p>
                                            </div>

                                            <div class="mb-4">
                                                <label class="form-label mb-3" for="tipe">Tipe Pesanan:</label>
                                                <select class="form-select form-select-lg" id="tipe" name="tipe" required>
                                                    <option value="" disabled selected>-- Pilih Tipe Pesanan --</option>
                                                    <option value="Dine In">Dine In</option>
                                                    <option value="Takeaway">Takeaway</option>
                                                </select>
                                            </div>

                                            <div class="mb-4 d-none" id="nomorMejaContainer">
                                                <label class="form-label mb-3" for="nomor_meja">Nomor Meja:</label>
                                                <select class="form-select form-select-lg" id="nomor_meja" name="nomor_meja">
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

                                            <div>
                                                <label class="form-label mb-3" for="tipe">Metode Pembayaran:</label>
                                                <select class="form-select form-select-lg" id="metode_pembayaran" name="metode_pembayaran" required>
                                                    <option value="" disabled selected>-- Pilih Metode Pembayaran --</option>
                                                    <option value="Cash">Cash</option>
                                                    <option value="Debit Card">Debit Card</option>
                                                    <option value="QRIS">QRIS</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class="card shadow-none border">
                                        <div class="card-body pt-4">
                                            <div class="row g-4 mb-4">
                                                <div class="col-md-6">
                                                    <h5>Total Belanja</h5>
                                                    <h5 id="total_belanja">Rp 0</h5>
                                                </div>
                                                <div class="col-md-6">
                                                    <h5>Kembali</h5>
                                                    <h5 id="sisa">Rp 0</h5>
                                                </div>
                                            </div>

                                            <div class="border-bottom pb-4 mb-4">
                                                <div class="input-group input-group-lg">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="number" class="form-control" name="jumlah_pembayaran" id="jumlah_pembayaran" required>
                                                    <button class="btn btn-lg btn-primary" type="submit" name="proses_sekarang" id="btn-process"><i class="bi bi bi-arrow-repeat me-2"></i> Proses</button>
                                                </div>
                                                <small><span class="text-danger">*</span> Masukkan angka valid seperti contoh berikut: 10000</small><br>
                                                <small id="error_jumlah_pembayaran" class="text-danger"></small>
                                            </div>

                                            <p><span class="text-danger">*</span> Pastikan pesanan Anda sudah benar.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                function updateSubtotalAndTotal() {
                                    let totalBelanja = 0;
                                    let allItemsValid = true; // Untuk memeriksa apakah semua item memiliki jumlah yang valid

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

                                        // Memeriksa apakah semua jumlah item valid (lebih dari 0)
                                        if (jumlah <= 0 || isNaN(jumlah)) {
                                            allItemsValid = false;
                                        }
                                    });

                                    // Update total belanja di tampilan
                                    document.getElementById('total_belanja').innerText = 'Rp ' + totalBelanja.toLocaleString('id-ID');

                                    return {
                                        totalBelanja,
                                        allItemsValid
                                    };
                                }

                                function validateForm() {
                                    const jumlahPembayaranInput = document.getElementById('jumlah_pembayaran');
                                    const totalBelanjaInfo = updateSubtotalAndTotal(); // Total belanja dan status item valid
                                    const jumlahPembayaran = parseFloat(jumlahPembayaranInput.value) || 0;
                                    const submitButton = document.getElementById('btn-process');
                                    const errorDisplay = document.getElementById('error_jumlah_pembayaran');
                                    const sisaDisplay = document.getElementById('sisa');

                                    let isFormValid = true;

                                    // Reset pesan error
                                    errorDisplay.textContent = '';

                                    // Validasi input pembayaran
                                    if (jumlahPembayaran <= 0 || isNaN(jumlahPembayaran)) {
                                        errorDisplay.textContent = 'Harus berupa angka yang valid.';
                                        sisaDisplay.textContent = 'Rp 0';
                                        jumlahPembayaranInput.classList.add('is-invalid'); // Tambahkan kelas is-invalid
                                        jumlahPembayaranInput.classList.remove('is-valid'); // Hapus kelas is-valid jika ada
                                        isFormValid = false;
                                    } else {
                                        jumlahPembayaranInput.classList.remove('is-invalid'); // Hapus kelas is-invalid jika valid
                                        jumlahPembayaranInput.classList.add('is-valid'); // Tambahkan kelas is-valid jika valid
                                    }

                                    // Hitung sisa pembayaran
                                    const sisa = isFormValid ? jumlahPembayaran - totalBelanjaInfo.totalBelanja : 0;
                                    sisaDisplay.textContent = 'Rp ' + sisa.toLocaleString('id-ID');

                                    // Validasi apakah pembayaran cukup
                                    if (sisa < 0) {
                                        isFormValid = false;
                                    }

                                    // Form valid jika semua item valid dan pembayaran valid
                                    submitButton.disabled = !(totalBelanjaInfo.allItemsValid && isFormValid);
                                }

                                // Event listener untuk tombol minus
                                document.querySelectorAll('.btn-minus').forEach(button => {
                                    button.addEventListener('click', function() {
                                        const input = this.closest('.input-group').querySelector('.jumlah-item');
                                        let jumlah = Math.max(0, parseInt(input.value) - 1); // Pastikan jumlah tidak kurang dari 0
                                        input.value = jumlah;
                                        validateForm(); // Validasi ulang form setelah perubahan jumlah item
                                    });
                                });

                                // Event listener untuk tombol plus
                                document.querySelectorAll('.btn-plus').forEach(button => {
                                    button.addEventListener('click', function() {
                                        const input = this.closest('.input-group').querySelector('.jumlah-item');
                                        let jumlah = parseInt(input.value) + 1;
                                        input.value = jumlah;
                                        validateForm(); // Validasi ulang form setelah perubahan jumlah item
                                    });
                                });

                                // Event listener untuk perubahan jumlah item secara manual
                                document.querySelectorAll('.jumlah-item').forEach(input => {
                                    input.addEventListener('change', function() {
                                        validateForm(); // Validasi ulang form setelah perubahan jumlah item manual
                                    });
                                });

                                // Event listener untuk tipe pesanan
                                document.getElementById('tipe').addEventListener('change', function() {
                                    const nomorMejaContainer = document.getElementById('nomorMejaContainer');
                                    const nomorMejaSelect = document.getElementById('nomor_meja');

                                    if (this.value === 'Dine In') {
                                        nomorMejaContainer.classList.remove('d-none');
                                        nomorMejaSelect.required = true;
                                    } else {
                                        nomorMejaContainer.classList.add('d-none');
                                        nomorMejaSelect.required = false;
                                    }
                                });

                                // Event listener untuk input jumlah pembayaran
                                document.getElementById('jumlah_pembayaran').addEventListener('input', function() {
                                    validateForm(); // Validasi ulang form saat jumlah pembayaran diinputkan
                                });

                                // Inisialisasi validasi form saat halaman dimuat
                                validateForm();
                            });
                        </script>
                    <?php else: ?>
                        <div class="row">
                            <div class="col-lg-12">
                                <img src="<?= base_url('assets/dashboard/img/add-to-cart.svg'); ?>" alt="Search not Found" height="250" width="250" class="d-block mx-auto">
                                <h5 class="text-center">Tidak Ada Data Di Keranjang</h5>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Toast -->
<?php include('../components/toasts.php') ?>

<?php
$content = ob_get_clean(); // Get content and clean buffer

// Menyertakan layout
include('../layout.php');
?>