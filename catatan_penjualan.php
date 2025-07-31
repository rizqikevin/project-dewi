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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add"])) {
        $nama_barang = htmlspecialchars($_POST["nama_barang"]);
        $jumlah_terjual = intval($_POST["jumlah_terjual"]);
        $tanggal_penjualan = htmlspecialchars($_POST["tanggal_penjualan"]);
        $harga_satuan = intval($_POST["harga_satuan"]);
        $total_harga = $jumlah_terjual * $harga_satuan;
        $keterangan = htmlspecialchars($_POST["keterangan"]);

        // Check stock availability
        $check_stock = "SELECT jumlah_barang FROM stok_barang WHERE nama_barang = ?";
        $stmt_check = $conn->prepare($check_stock);
        $stmt_check->bind_param("s", $nama_barang);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows > 0) {
            $row_stock = $result_check->fetch_assoc();
            $current_stock = $row_stock['jumlah_barang'];
            
            if ($current_stock >= $jumlah_terjual) {
                // Insert sale record
                $sql = "INSERT INTO catatan_penjualan (nama_barang, jumlah_terjual, tanggal_penjualan, total_harga, keterangan) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sisis", $nama_barang, $jumlah_terjual, $tanggal_penjualan, $total_harga, $keterangan);
                
                if ($stmt->execute()) {
                    // Update stock
                    $new_stock = $current_stock - $jumlah_terjual;
                    $update_stock = "UPDATE stok_barang SET jumlah_barang = ? WHERE nama_barang = ?";
                    $stmt_update = $conn->prepare($update_stock);
                    $stmt_update->bind_param("is", $new_stock, $nama_barang);
                    $stmt_update->execute();
                    $stmt_update->close();
                    
                    echo "<script>alert('Penjualan berhasil ditambahkan dan stock telah diperbarui'); window.location.href = 'catatan_penjualan.php';</script>";
                } else {
                    echo "<script>alert('Gagal menambahkan penjualan'); window.location.href = 'catatan_penjualan.php';</script>";
                }
                $stmt->close();
            } else {
                echo "<script>alert('Stock tidak mencukupi! Stock tersedia: $current_stock'); window.location.href = 'catatan_penjualan.php';</script>";
            }
        } else {
            echo "<script>alert('Barang tidak ditemukan dalam stock!'); window.location.href = 'catatan_penjualan.php';</script>";
        }
        $stmt_check->close();
    } elseif (isset($_POST["edit"])) {
        $id = intval($_POST["id"]);
        $nama_barang = htmlspecialchars($_POST["nama_barang"]);
        $jumlah_terjual = intval($_POST["jumlah_terjual"]);
        $tanggal_penjualan = htmlspecialchars($_POST["tanggal_penjualan"]);
        $total_harga = intval($_POST["total_harga"]);
        $keterangan = htmlspecialchars($_POST["keterangan"]);

        $sql = "UPDATE catatan_penjualan SET nama_barang = ?, jumlah_terjual = ?, tanggal_penjualan = ?, total_harga = ?, keterangan = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sisisi", $nama_barang, $jumlah_terjual, $tanggal_penjualan, $total_harga, $keterangan, $id);

        if ($stmt->execute()) {
            echo "<script>alert('Penjualan telah diubah'); window.location.href = 'catatan_penjualan.php';</script>";
        } else {
            echo "<script>alert('Gagal mengubah penjualan'); window.location.href = 'catatan_penjualan.php';</script>";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Catatan Penjualan - PROSTOCK</title>
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
                    <h1 class="mt-4">Catatan Penjualan</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active"><?php echo htmlspecialchars($_SESSION['nama']); ?></li>
                    </ol>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table mr-1"></i>
                            Data Penjualan
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Nama Barang</th>
                                            <th>Jumlah Terjual</th>
                                            <th>Tanggal Penjualan</th>
                                            <th>Total Harga</th>
                                            <th>Keterangan</th>
                                            <th>Aksi</th> <!-- Kolom baru untuk aksi -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT id, nama_barang, jumlah_terjual, tanggal_penjualan, total_harga, keterangan FROM catatan_penjualan";
                                        $result = $conn->query($sql);

                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>
                                                    <td>" . htmlspecialchars($row["nama_barang"]) . "</td>
                                                    <td>" . intval($row["jumlah_terjual"]) . "</td>
                                                    <td>" . htmlspecialchars($row["tanggal_penjualan"]) . "</td>
                                                    <td>" . formatRupiah($row["total_harga"]) . "</td>
                                                    <td>" . htmlspecialchars($row["keterangan"]) . "</td>
                                                    <td>
                                                        <button class='btn btn-warning btn-edit' data-id='" . $row["id"] . "' data-nama='" . htmlspecialchars($row["nama_barang"]) . "' data-jumlah='" . intval($row["jumlah_terjual"]) . "' data-tanggal='" . htmlspecialchars($row["tanggal_penjualan"]) . "' data-harga='" . intval($row["total_harga"]) . "' data-keterangan='" . htmlspecialchars($row["keterangan"]) . "'>Edit</button>
                                                    </td>
                                                </tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='6'>No data found</td></tr>";
                                        }
                                        $conn->close();
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-plus mr-1"></i>
                            Tambah Penjualan Baru
                        </div>
                        <div class="card-body">
                            <form action="catatan_penjualan.php" method="POST">
                                <input type="hidden" name="add" value="1">
                                <div class="form-group">
                                    <label for="nama_barang">Nama Barang</label>
                                    <select class="form-control" id="nama_barang" name="nama_barang" required onchange="updateStock()">
                                        <option value="">Pilih Barang</option>
                                        <?php
                                        include 'koneksi.php';
                                        $sql_barang = "SELECT DISTINCT nama_barang, jumlah_barang, harga_barang FROM stok_barang WHERE jumlah_barang > 0";
                                        $result_barang = $conn->query($sql_barang);
                                        if ($result_barang->num_rows > 0) {
                                            while ($row_barang = $result_barang->fetch_assoc()) {
                                                echo '<option value="' . htmlspecialchars($row_barang['nama_barang']) . '" data-stock="' . $row_barang['jumlah_barang'] . '" data-harga="' . $row_barang['harga_barang'] . '">' . htmlspecialchars($row_barang['nama_barang']) . ' (Stock: ' . $row_barang['jumlah_barang'] . ')</option>';
                                            }
                                        }
                                        $conn->close();
                                        ?>
                                    </select>
                                    <small class="form-text text-muted">Stock tersedia: <span id="stock-info">-</span></small>
                                </div>
                                <div class="form-group">
                                    <label for="jumlah_terjual">Jumlah Terjual</label>
                                    <input type="number" class="form-control" id="jumlah_terjual" name="jumlah_terjual" required min="1" onchange="calculateTotal()">
                                </div>
                                <div class="form-group">
                                    <label for="tanggal_penjualan">Tanggal Penjualan</label>
                                    <input type="date" class="form-control" id="tanggal_penjualan" name="tanggal_penjualan" required>
                                </div>
                                <div class="form-group">
                                    <label for="harga_satuan">Harga Satuan</label>
                                    <input type="number" class="form-control" id="harga_satuan" name="harga_satuan" required onchange="calculateTotal()">
                                </div>
                                <div class="form-group">
                                    <label for="total_harga_display">Total Harga</label>
                                    <input type="text" class="form-control" id="total_harga_display" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="keterangan">Keterangan</label>
                                    <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Tambah Penjualan</button>
                            </form>
                        </div>
                    </div>
                    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">Edit Penjualan</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="editForm" action="catatan_penjualan.php" method="POST">
                                        <input type="hidden" name="edit" value="1">
                                        <input type="hidden" id="edit_id" name="id">
                                        <div class="form-group">
                                            <label for="edit_nama_barang">Nama Barang</label>
                                            <input type="text" class="form-control" id="edit_nama_barang" name="nama_barang" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="edit_jumlah_terjual">Jumlah Terjual</label>
                                            <input type="number" class="form-control" id="edit_jumlah_terjual" name="jumlah_terjual" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="edit_tanggal_penjualan">Tanggal Penjualan</label>
                                            <input type="date" class="form-control" id="edit_tanggal_penjualan" name="tanggal_penjualan" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="edit_total_harga">Total Harga</label>
                                            <input type="number" class="form-control" id="edit_total_harga" name="total_harga" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="edit_keterangan">Keterangan</label>
                                            <textarea class="form-control" id="edit_keterangan" name="keterangan" rows="3"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Gilang FR 2024</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms & Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/datatables-demo.js"></script>
    
    <script>
    function updateStock() {
        var select = document.getElementById('nama_barang');
        var selectedOption = select.options[select.selectedIndex];
        var stockInfo = document.getElementById('stock-info');
        var hargaSatuan = document.getElementById('harga_satuan');
        var jumlahTerjual = document.getElementById('jumlah_terjual');
        
        if (selectedOption.value) {
            var stock = selectedOption.getAttribute('data-stock');
            var harga = selectedOption.getAttribute('data-harga');
            stockInfo.textContent = stock + ' unit';
            hargaSatuan.value = harga;
            jumlahTerjual.max = stock;
            calculateTotal();
        } else {
            stockInfo.textContent = '-';
            hargaSatuan.value = '';
            jumlahTerjual.max = '';
            document.getElementById('total_harga_display').value = '';
        }
    }
    
    function calculateTotal() {
        var jumlah = document.getElementById('jumlah_terjual').value;
        var harga = document.getElementById('harga_satuan').value;
        var totalDisplay = document.getElementById('total_harga_display');
        
        if (jumlah && harga) {
            var total = parseInt(jumlah) * parseInt(harga);
            totalDisplay.value = 'Rp ' + total.toLocaleString('id-ID');
        } else {
            totalDisplay.value = '';
        }
    }
    
    // Edit modal functionality
    $('.btn-edit').click(function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        var jumlah = $(this).data('jumlah');
        var tanggal = $(this).data('tanggal');
        var harga = $(this).data('harga');
        var keterangan = $(this).data('keterangan');
        
        $('#edit_id').val(id);
        $('#edit_nama_barang').val(nama);
        $('#edit_jumlah_terjual').val(jumlah);
        $('#edit_tanggal_penjualan').val(tanggal);
        $('#edit_total_harga').val(harga);
        $('#edit_keterangan').val(keterangan);
        
        $('#editModal').modal('show');
    });
    </script>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();

            $('.btn-edit').on('click', function() {
                var id = $(this).data('id');
                var nama = $(this).data('nama');
                var jumlah = $(this).data('jumlah');
                var tanggal = $(this).data('tanggal');
                var harga = $(this).data('harga');
                var keterangan = $(this).data('keterangan');

                $('#edit_id').val(id);
                $('#edit_nama_barang').val(nama);
                $('#edit_jumlah_terjual').val(jumlah);
                $('#edit_tanggal_penjualan').val(tanggal);
                $('#edit_total_harga').val(harga);
                $('#edit_keterangan').val(keterangan);

                $('#editModal').modal('show');
            });
        });
    </script>
</body>
</html>
