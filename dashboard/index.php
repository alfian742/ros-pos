<?php
// Menyertakan konfigurasi
include('../config/config.php');

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
$title = 'Dashboard';

ob_start(); // Start output buffering 
?>

<div class="pagetitle">
  <div class="d-flex justify-content-between align-items-center gap-2 mb-4">
    <h1 class="mb-0 fw-bold"><?= $title ?></h1>

    <div style="width: 28rem;">
      <!-- Alert -->
      <?php include('components/alerts.php') ?>
    </div>
  </div>
</div><!-- End Page Title -->

<section class="section dashboard">
  <div class="row">
    <?php
    // Hitung jumlah pesanan hari ini
    $sql_today = "SELECT COUNT(*) as count 
                  FROM pesanan 
                  INNER JOIN pembayaran ON pembayaran.id_pesanan=pesanan.id_pesanan
                  WHERE DATE(pembayaran.tanggal) = CURDATE()";
    $query_today = mysqli_query($koneksi, $sql_today);
    $count_today = mysqli_fetch_array($query_today)['count'];

    // Hitung jumlah pesanan bulan ini
    $sql_this_month = "SELECT COUNT(*) as count 
                       FROM pesanan 
                       INNER JOIN pembayaran ON pembayaran.id_pesanan=pesanan.id_pesanan
                       WHERE MONTH(pembayaran.tanggal) = MONTH(CURDATE()) AND YEAR(pembayaran.tanggal) = YEAR(CURDATE()) 
                       AND pesanan.status='Completed' AND pembayaran.status='Paid'";
    $query_this_month = mysqli_query($koneksi, $sql_this_month);
    $count_this_month = mysqli_fetch_array($query_this_month)['count'];
    ?>

    <!-- Sales Card untuk Pesanan Hari Ini -->
    <div class="col-lg-3 col-md-6">
      <div class="card info-card sales-card">
        <div class="card-body">
          <h5 class="card-title">
            Pesanan Harian
            <br>
            <span class="this-day"></span>
          </h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-cart"></i>
            </div>
            <div class="ps-3">
              <p class="mb-0 fs-5 fw-semibold"><?= $count_today; ?></p>
            </div>
          </div>
        </div>
      </div>
    </div><!-- End Sales Card -->

    <!-- Sales Card untuk Pesanan Bulan Ini -->
    <div class="col-lg-3 col-md-6">
      <div class="card info-card sales-card">
        <div class="card-body">
          <h5 class="card-title">
            Pesanan Bulanan
            <br>
            <span class="this-month"></span>
          </h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-cart"></i>
            </div>
            <div class="ps-3">
              <p class="mb-0 fs-5 fw-semibold"><?= $count_this_month; ?></p>
            </div>
          </div>
        </div>
      </div>
    </div><!-- End Sales Card -->

    <?php
    // Hitung pendapatan hari ini
    $sql_revenue_today = "SELECT SUM(pesanan.total_pembayaran) as revenue 
                          FROM pembayaran 
                          INNER JOIN pesanan ON pembayaran.id_pesanan = pesanan.id_pesanan
                          WHERE DATE(pembayaran.tanggal) = CURDATE() AND pesanan.status='Completed' AND pembayaran.status='Paid'";
    $query_revenue_today = mysqli_query($koneksi, $sql_revenue_today);
    $revenue_today = mysqli_fetch_array($query_revenue_today)['revenue'] ?? 0;

    // Hitung pendapatan bulan ini
    $sql_revenue_this_month = "SELECT SUM(pesanan.total_pembayaran) as revenue 
                               FROM pembayaran 
                               INNER JOIN pesanan ON pembayaran.id_pesanan = pesanan.id_pesanan
                               WHERE MONTH(pembayaran.tanggal) = MONTH(CURDATE()) AND YEAR(pembayaran.tanggal) = YEAR(CURDATE()) 
                               AND pesanan.status='Completed' AND pembayaran.status='Paid'";
    $query_revenue_this_month = mysqli_query($koneksi, $sql_revenue_this_month);
    $revenue_this_month = mysqli_fetch_array($query_revenue_this_month)['revenue'] ?? 0;
    ?>

    <!-- Revenue Card untuk Pendapatan Hari Ini -->
    <div class="col-lg-3 col-md-6">
      <div class="card info-card revenue-card">
        <div class="card-body">
          <h5 class="card-title">
            Pendapatan Harian
            <br>
            <span class="this-day"></span>
          </h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="ps-3">
              <p class="mb-0 fs-5 fw-semibold">Rp <?= number_format($revenue_today, 0, ',', '.'); ?></p>
            </div>
          </div>
        </div>
      </div>
    </div><!-- End Revenue Card -->

    <!-- Revenue Card untuk Pendapatan Bulan Ini -->
    <div class="col-lg-3 col-md-6">
      <div class="card info-card revenue-card">
        <div class="card-body">
          <h5 class="card-title">
            Pendapatan Bulanan
            <br>
            <span class="this-month"></span>
          </h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
              <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="ps-3">
              <p class="mb-0 fs-5 fw-semibold">Rp <?= number_format($revenue_this_month, 0, ',', '.'); ?></p>
            </div>
          </div>
        </div>
      </div>
    </div><!-- End Revenue Card -->

    <div class="col-12">
      <div class="row">
        <?php if ($_SESSION['level'] === 'Admin'): ?>
          <div class="col-lg-12">
            <div class="row">
              <?php
              // Hitung jumlah menu
              $sql_count_menu = "SELECT COUNT(*) as count FROM menu";
              $query_count_menu = mysqli_query($koneksi, $sql_count_menu);
              $count_menu = mysqli_fetch_array($query_count_menu)['count'] ?? 0;
              ?>

              <!-- Menu Card -->
              <div class="col-lg-3 col-md-6">
                <div class="card info-card customers-card">
                  <div class="card-body">
                    <h5 class="card-title">Menu</h5>
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-book"></i>
                      </div>
                      <div class="ps-3">
                        <p class="mb-0 fs-5 fw-semibold"><?= $count_menu; ?></p>
                      </div>
                    </div>
                  </div>
                </div>
              </div><!-- End Menu Card -->

              <?php
              // Query to count the number of users based on their level
              $sql_count_admin = "SELECT COUNT(*) as count FROM users WHERE level = 'Admin'";
              $sql_count_kasir = "SELECT COUNT(*) as count FROM users WHERE level = 'Cashier'";
              $sql_count_user = "SELECT COUNT(*) as count FROM users WHERE level = 'User'";

              $query_count_admin = mysqli_query($koneksi, $sql_count_admin);
              $query_count_kasir = mysqli_query($koneksi, $sql_count_kasir);
              $query_count_user = mysqli_query($koneksi, $sql_count_user);

              $count_admin = mysqli_fetch_array($query_count_admin)['count'];
              $count_kasir = mysqli_fetch_array($query_count_kasir)['count'];
              $count_user = mysqli_fetch_array($query_count_user)['count'];
              ?>

              <!-- Users Card for User Count -->
              <div class="col-lg-3 col-md-6">
                <div class="card info-card customers-card">
                  <div class="card-body">
                    <h5 class="card-title">Pelanggan</h5>
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-people"></i>
                      </div>
                      <div class="ps-3">
                        <p class="mb-0 fs-5 fw-semibold"><?= $count_user; ?></p>
                      </div>
                    </div>
                  </div>
                </div>
              </div><!-- End Users Card -->

              <!-- Users Card for Cashier Count -->
              <div class="col-lg-3 col-md-6">
                <div class="card info-card customers-card">
                  <div class="card-body">
                    <h5 class="card-title">Kasir</h5>
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-people"></i>
                      </div>
                      <div class="ps-3">
                        <p class="mb-0 fs-5 fw-semibold"><?= $count_kasir; ?></p>
                      </div>
                    </div>
                  </div>
                </div>
              </div><!-- End Cashier Card -->

              <!-- Users Card for Admin Count -->
              <div class="col-lg-3 col-md-6">
                <div class="card info-card customers-card">
                  <div class="card-body">
                    <h5 class="card-title">Admin</h5>
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-people"></i>
                      </div>
                      <div class="ps-3">
                        <p class="mb-0 fs-5 fw-semibold"><?= $count_admin; ?></p>
                      </div>
                    </div>
                  </div>
                </div>
              </div><!-- End Admin Card -->
            </div>
          </div>
        <?php endif ?>


        <!-- Reports -->
        <div class="col-lg-12">
          <?php
          // Ambil bulan dan tahun saat ini
          $current_month = date('m');
          $current_year = date('Y');

          // Dapatkan jumlah hari dalam bulan saat ini
          $days_in_month = cal_days_in_month(CAL_GREGORIAN, $current_month, $current_year);

          // Inisialisasi array untuk jumlah pesanan dan pendapatan sesuai jumlah hari di bulan tersebut
          $daily_orders = array_fill(0, $days_in_month, 0); // Array untuk jumlah hari
          $daily_revenue = array_fill(0, $days_in_month, 0); // Array untuk jumlah hari

          // Query untuk jumlah pesanan dan pendapatan per hari
          $sql_daily_data = "SELECT DAY(pesanan.tanggal) as day, COUNT(*) as orders, SUM(pesanan.total_pembayaran) as revenue 
                              FROM pembayaran 
                              INNER JOIN pesanan ON pembayaran.id_pesanan = pesanan.id_pesanan
                              WHERE MONTH(pembayaran.tanggal) = '$current_month' AND YEAR(pembayaran.tanggal) = '$current_year' 
                              AND pesanan.status='Completed' AND pembayaran.status='Paid' 
                              GROUP BY day";

          $query_daily_data = mysqli_query($koneksi, $sql_daily_data);
          while ($row = mysqli_fetch_assoc($query_daily_data)) {
            $daily_orders[$row['day'] - 1] = (int)$row['orders']; // Menggunakan index 0
            $daily_revenue[$row['day'] - 1] = (int)$row['revenue']; // Menggunakan index 0
          }
          ?>

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Grafik Penjualan
                <br>
                <span class="this-month"></span>
              </h5>

              <!-- Line Chart -->
              <div id="reportsChart"></div>

              <script>
                document.addEventListener("DOMContentLoaded", () => {
                  new ApexCharts(document.querySelector("#reportsChart"), {
                    series: [{
                      name: 'Pesanan',
                      data: <?= json_encode($daily_orders); ?>, // Data jumlah pesanan
                    }, {
                      name: 'Pendapatan',
                      data: <?= json_encode($daily_revenue); ?> // Data pendapatan
                    }],
                    chart: {
                      height: 350,
                      type: 'area',
                      toolbar: {
                        show: false
                      },
                    },
                    markers: {
                      size: 4
                    },
                    colors: ['#4154f1', '#2eca6a'],
                    fill: {
                      type: "gradient",
                      gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.3,
                        opacityTo: 0.4,
                        stops: [0, 90, 100]
                      }
                    },
                    dataLabels: {
                      enabled: false
                    },
                    stroke: {
                      curve: 'smooth',
                      width: 2
                    },
                    xaxis: {
                      categories: [
                        <?php
                        // Mengenerate nama hari dalam bulan
                        $month_names = [
                          1 => 'Januari',
                          2 => 'Februari',
                          3 => 'Maret',
                          4 => 'April',
                          5 => 'Mei',
                          6 => 'Juni',
                          7 => 'Juli',
                          8 => 'Agustus',
                          9 => 'September',
                          10 => 'Oktober',
                          11 => 'November',
                          12 => 'Desember'
                        ];

                        for ($day = 1; $day <= 31; $day++) {
                          if ($day <= date('t')) { // Pastikan tidak melebihi jumlah hari dalam bulan
                            echo '"' . $day . ' ' . $month_names[intval($current_month)] . '",';
                          }
                        }
                        ?>
                      ],
                    },
                    yaxis: {
                      title: {
                        text: 'Pendapatan (Rp)',
                      },
                      labels: {
                        formatter: (value) => 'Rp ' + value.toLocaleString()
                      }
                    },
                    tooltip: {
                      x: {
                        format: 'dd/MM'
                      },
                      y: {
                        formatter: (value, {
                          seriesIndex
                        }) => {
                          return seriesIndex === 0 ? value : 'Rp ' + value.toLocaleString();
                        }
                      }
                    },
                  }).render();
                });
              </script>
              <!-- End Line Chart -->
            </div>
          </div>
        </div><!-- End Reports -->
      </div>
    </div>
  </div>
</section>

<script>
  function displayDate() {
    const today = new Date();

    // Format untuk tanggal hari ini
    const optionsDay = {
      day: 'numeric',
      month: 'long',
      year: 'numeric'
    };
    const formattedDay = today.toLocaleDateString('id-ID', optionsDay);

    // Format untuk bulan ini
    const optionsMonth = {
      month: 'long',
      year: 'numeric'
    };
    const formattedMonth = today.toLocaleDateString('id-ID', optionsMonth);

    // Menampilkan hasil ke elemen dengan class
    const dayElements = document.querySelectorAll('.this-day');
    const monthElements = document.querySelectorAll('.this-month');

    dayElements.forEach(el => el.textContent = formattedDay);
    monthElements.forEach(el => el.textContent = formattedMonth);
  }

  // Panggil fungsi untuk menampilkan tanggal
  displayDate();
</script>

<?php
$content = ob_get_clean(); // Get content and clean buffer

// Menyertakan layout
include('layout.php');
?>