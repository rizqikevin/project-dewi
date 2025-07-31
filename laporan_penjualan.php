<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

function formatRupiah($angka){
    return 'Rp ' . number_format($angka, 2, ',', '.');
}

// Get filter parameters
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');
$filter_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$filter_barang = isset($_GET['barang']) ? $_GET['barang'] : '';

// Query for monthly sales report
$sql_monthly = "SELECT 
    nama_barang,
    SUM(jumlah_terjual) as total_terjual,
    SUM(total_harga) as total_pendapatan,
    COUNT(*) as jumlah_transaksi
    FROM catatan_penjualan 
    WHERE DATE_FORMAT(tanggal_penjualan, '%Y-%m') = ?
    GROUP BY nama_barang
    ORDER BY total_pendapatan DESC";

$stmt_monthly = $conn->prepare($sql_monthly);
$stmt_monthly->bind_param("s", $filter_bulan);
$stmt_monthly->execute();
$result_monthly = $stmt_monthly->get_result();

// Query for yearly sales report
$sql_yearly = "SELECT 
    MONTH(tanggal_penjualan) as bulan,
    SUM(jumlah_terjual) as total_terjual,
    SUM(total_harga) as total_pendapatan,
    COUNT(*) as jumlah_transaksi
    FROM catatan_penjualan 
    WHERE YEAR(tanggal_penjualan) = ?
    GROUP BY MONTH(tanggal_penjualan)
    ORDER BY bulan";

$stmt_yearly = $conn->prepare($sql_yearly);
$stmt_yearly->bind_param("s", $filter_tahun);
$stmt_yearly->execute();
$result_yearly = $stmt_yearly->get_result();

// Query for top selling products
$sql_top = "SELECT 
    nama_barang,
    SUM(jumlah_terjual) as total_terjual,
    SUM(total_harga) as total_pendapatan
    FROM catatan_penjualan 
    GROUP BY nama_barang
    ORDER BY total_terjual DESC
    LIMIT 10";

$result_top = $conn->query($sql_top);

// Query for stock status
$sql_stock = "SELECT 
    sb.nama_barang,
    sb.jumlah_barang as stock_tersedia,
    COALESCE(SUM(cp.jumlah_terjual), 0) as total_terjual,
    sb.harga_barang
    FROM stok_barang sb
    LEFT JOIN catatan_penjualan cp ON sb.nama_barang = cp.nama_barang
    GROUP BY sb.nama_barang, sb.jumlah_barang, sb.harga_barang
    ORDER BY sb.jumlah_barang ASC";

$result_stock = $conn->query($sql_stock);

$bulan_names = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Laporan Penjualan - PROSTOCK</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand" href="dashboard.php">PROSTOCK</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>
        
        <!-- Navbar-->
        <ul class="navbar-nav ml-auto ml-md-0">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="#">Settings</a>
                    <a class="dropdown-item" href="#">Activity Log</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="logout.php">Logout</a>
                </div>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Core</div>
                        <a class="nav-link" href="dashboard.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        <div class="sb-sidenav-menu-heading">Menu</div>
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePenjualan" aria-expanded="false" aria-controls="collapsePenjualan">
                            <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>
                            Penjualan
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapsePenjualan" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="catatan_penjualan.php">Catatan Penjualan</a>
                                <a class="nav-link" href="laporan_penjualan.php">Laporan Penjualan</a>
                            </nav>
                        </div>
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                            <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                            Opsi Admin
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#pagesCollapseAuth" aria-expanded="false" aria-controls="pagesCollapseAuth">
                                    Kelola Akun
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="pagesCollapseAuth" aria-labelledby="headingOne" data-parent="#sidenavAccordionPages">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link" href="login.php">Login</a>
                                        <?php
                                        if ($_SESSION['role'] == 'Administrator') {
                                            echo '<a class="nav-link" href="register.php">Register</a>';
                                        }
                                        ?>
                                        <a class="nav-link" href="ganti_password.php">Ganti Password</a>
                                        <?php
                                        if ($_SESSION['role'] == 'Administrator') {
                                            echo '<a class="nav-link" href="hapus_user.php">Hapus User</a>';
                                        }
                                        ?>
                                    </nav>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    <?php echo htmlspecialchars($_SESSION['nama']); ?>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <h1 class="mt-4">Laporan Penjualan</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Laporan Penjualan</li>
                    </ol>

                    <!-- Filter Section -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-filter mr-1"></i>
                            Filter Laporan
                        </div>
                        <div class="card-body">
                            <form method="GET" action="laporan_penjualan.php">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="bulan">Bulan</label>
                                            <input type="month" class="form-control" id="bulan" name="bulan" value="<?php echo $filter_bulan; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="tahun">Tahun</label>
                                            <select class="form-control" id="tahun" name="tahun">
                                                <?php
                                                for ($i = 2020; $i <= date('Y') + 1; $i++) {
                                                    $selected = ($i == $filter_tahun) ? 'selected' : '';
                                                    echo "<option value='$i' $selected>$i</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>&nbsp;</label><br>
                                            <button type="submit" class="btn btn-primary">Filter</button>
                                            <a href="laporan_penjualan.php" class="btn btn-secondary">Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row">
                        <?php
                        // Calculate summary data
                        $sql_summary = "SELECT 
                            SUM(total_harga) as total_pendapatan,
                            SUM(jumlah_terjual) as total_barang_terjual,
                            COUNT(*) as total_transaksi
                            FROM catatan_penjualan 
                            WHERE DATE_FORMAT(tanggal_penjualan, '%Y-%m') = ?";
                        $stmt_summary = $conn->prepare($sql_summary);
                        $stmt_summary->bind_param("s", $filter_bulan);
                        $stmt_summary->execute();
                        $result_summary = $stmt_summary->get_result();
                        $summary = $result_summary->fetch_assoc();
                        ?>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-primary text-white mb-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="small">Total Pendapatan</div>
                                            <div class="h5"><?php echo formatRupiah($summary['total_pendapatan'] ?? 0); ?></div>
                                        </div>
                                        <div><i class="fas fa-money-bill-wave fa-2x"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-success text-white mb-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="small">Total Barang Terjual</div>
                                            <div class="h5"><?php echo number_format($summary['total_barang_terjual'] ?? 0); ?> unit</div>
                                        </div>
                                        <div><i class="fas fa-boxes fa-2x"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-warning text-white mb-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="small">Total Transaksi</div>
                                            <div class="h5"><?php echo number_format($summary['total_transaksi'] ?? 0); ?></div>
                                        </div>
                                        <div><i class="fas fa-shopping-cart fa-2x"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-info text-white mb-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="small">Rata-rata per Transaksi</div>
                                            <div class="h5"><?php echo formatRupiah(($summary['total_transaksi'] > 0) ? $summary['total_pendapatan'] / $summary['total_transaksi'] : 0); ?></div>
                                        </div>
                                        <div><i class="fas fa-chart-line fa-2x"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Sales Report -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Laporan Penjualan Bulanan - <?php echo date('F Y', strtotime($filter_bulan . '-01')); ?>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="monthlyTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Nama Barang</th>
                                            <th>Total Terjual</th>
                                            <th>Total Pendapatan</th>
                                            <th>Jumlah Transaksi</th>
                                            <th>Rata-rata per Transaksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($result_monthly->num_rows > 0) {
                                            while ($row = $result_monthly->fetch_assoc()) {
                                                $avg_per_transaction = $row['total_pendapatan'] / $row['jumlah_transaksi'];
                                                echo "<tr>
                                                    <td>" . htmlspecialchars($row['nama_barang']) . "</td>
                                                    <td>" . number_format($row['total_terjual']) . " unit</td>
                                                    <td>" . formatRupiah($row['total_pendapatan']) . "</td>
                                                    <td>" . number_format($row['jumlah_transaksi']) . "</td>
                                                    <td>" . formatRupiah($avg_per_transaction) . "</td>
                                                </tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='5' class='text-center'>Tidak ada data untuk bulan ini</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Yearly Sales Trend -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-chart-line mr-1"></i>
                            Tren Penjualan Tahunan - <?php echo $filter_tahun; ?>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Bulan</th>
                                            <th>Total Terjual</th>
                                            <th>Total Pendapatan</th>
                                            <th>Jumlah Transaksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($result_yearly->num_rows > 0) {
                                            while ($row = $result_yearly->fetch_assoc()) {
                                                echo "<tr>
                                                    <td>" . $bulan_names[$row['bulan']] . "</td>
                                                    <td>" . number_format($row['total_terjual']) . " unit</td>
                                                    <td>" . formatRupiah($row['total_pendapatan']) . "</td>
                                                    <td>" . number_format($row['jumlah_transaksi']) . "</td>
                                                </tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='4' class='text-center'>Tidak ada data untuk tahun ini</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Top Selling Products -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-trophy mr-1"></i>
                            Top 10 Produk Terlaris (All Time)
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Ranking</th>
                                            <th>Nama Barang</th>
                                            <th>Total Terjual</th>
                                            <th>Total Pendapatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($result_top->num_rows > 0) {
                                            $rank = 1;
                                            while ($row = $result_top->fetch_assoc()) {
                                                $badge_class = '';
                                                if ($rank == 1) $badge_class = 'badge-warning';
                                                elseif ($rank == 2) $badge_class = 'badge-secondary';
                                                elseif ($rank == 3) $badge_class = 'badge-dark';
                                                else $badge_class = 'badge-light';
                                                
                                                echo "<tr>
                                                    <td><span class='badge $badge_class'>#$rank</span></td>
                                                    <td>" . htmlspecialchars($row['nama_barang']) . "</td>
                                                    <td>" . number_format($row['total_terjual']) . " unit</td>
                                                    <td>" . formatRupiah($row['total_pendapatan']) . "</td>
                                                </tr>";
                                                $rank++;
                                            }
                                        } else {
                                            echo "<tr><td colspan='4' class='text-center'>Tidak ada data</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Status Report -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-warehouse mr-1"></i>
                            Status Stock & Penjualan
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="stockTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Nama Barang</th>
                                            <th>Stock Tersedia</th>
                                            <th>Total Terjual</th>
                                            <th>Harga Satuan</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($result_stock->num_rows > 0) {
                                            while ($row = $result_stock->fetch_assoc()) {
                                                $status = '';
                                                $status_class = '';
                                                
                                                if ($row['stock_tersedia'] == 0) {
                                                    $status = 'Habis';
                                                    $status_class = 'badge-danger';
                                                } elseif ($row['stock_tersedia'] <= 10) {
                                                    $status = 'Stock Rendah';
                                                    $status_class = 'badge-warning';
                                                } elseif ($row['stock_tersedia'] <= 50) {
                                                    $status = 'Stock Sedang';
                                                    $status_class = 'badge-info';
                                                } else {
                                                    $status = 'Stock Aman';
                                                    $status_class = 'badge-success';
                                                }
                                                
                                                echo "<tr>
                                                    <td>" . htmlspecialchars($row['nama_barang']) . "</td>
                                                    <td>" . number_format($row['stock_tersedia']) . " unit</td>
                                                    <td>" . number_format($row['total_terjual']) . " unit</td>
                                                    <td>" . formatRupiah($row['harga_barang']) . "</td>
                                                    <td><span class='badge $status_class'>$status</span></td>
                                                </tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='5' class='text-center'>Tidak ada data stock</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; GilangFR 2024</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
    
    <script>
    $(document).ready(function() {
        $('#monthlyTable').DataTable({
            "order": [[ 2, "desc" ]],
            "pageLength": 25
        });
        
        $('#stockTable').DataTable({
            "order": [[ 1, "asc" ]],
            "pageLength": 25
        });
    });
    </script>
</body>
</html>

<?php
$conn->close();
?>