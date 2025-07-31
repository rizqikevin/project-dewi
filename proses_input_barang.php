<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form values
    $nama_barang = htmlspecialchars($_POST['nama_barang']);
    $asal_barang = htmlspecialchars($_POST['asal_barang']);
    $jumlah_barang = filter_var($_POST['jumlah_barang'], FILTER_SANITIZE_NUMBER_INT);
    $tanggal_masuk = htmlspecialchars($_POST['tanggal_masuk']);
    $harga_barang = filter_var($_POST['harga_barang'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    // Database connection
    include 'koneksi.php';

    // Check connection
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    // Prepared statement to insert data
    $stmt = $conn->prepare("INSERT INTO stok_barang (nama_barang, asal_barang, jumlah_barang, tanggal_masuk, harga_barang) VALUES (?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("ssiss", $nama_barang, $asal_barang, $jumlah_barang, $tanggal_masuk, $harga_barang);

    // Execute query
    if ($stmt->execute()) {
        echo "<script>
                alert('Data barang baru berhasil disimpan.');
                window.location.href = 'dashboard.php';
              </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>
