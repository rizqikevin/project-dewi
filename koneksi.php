<?php
$servername = "db";
$username = "root";
$password = "rootpassword";
$dbname = "dbprostock_sql";

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Atau menggunakan PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>
