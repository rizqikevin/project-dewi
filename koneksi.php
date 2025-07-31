<?php
$servername = "db";
$username = "prostock_user";
$password = "prostock_pass";
$dbname = "dbprostock";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>