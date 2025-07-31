<?php
session_start();

// Koneksi ke database
include 'koneksi.php';

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Pesan awal untuk notifikasi
$message = '';

// Jika formulir dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil dan sanitasi data dari formulir
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];

    // Validasi current password
    $stmt = $conn->prepare("SELECT password FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stored_password = $row['password'];
        if (password_verify($current_password, $stored_password)) {
            // Hash password baru
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update password di database
            $stmt = $conn->prepare("UPDATE users SET password=? WHERE email=?");
            $stmt->bind_param("ss", $hashed_new_password, $email);
            if ($stmt->execute()) {
                // Tampilkan pesan konfirmasi dengan JavaScript
                echo '<script>
                        alert("Password berhasil diubah. Klik OK untuk melanjutkan ke halaman login.");
                        window.location.href = "login.php";
                      </script>';
            } else {
                $message = 'Gagal mengubah password. Silakan coba lagi.';
            }
        } else {
            $message = 'Password saat ini salah.';
        }
    } else {
        $message = 'Email tidak ditemukan.';
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
</head>
<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header"><h3 class="text-center font-weight-light my-4">Ganti Password</h3></div>
                                <div class="card-body">
                                    <?php if (!empty($message)) echo '<div class="alert alert-info">' . htmlspecialchars($message) . '</div>'; ?>
                                    <form action="ganti_password.php" method="post">
                                        <div class="form-group">
                                            <label class="small mb-1" for="email">Email</label>
                                            <input class="form-control py-4" id="email" name="email" type="email" placeholder="Enter your email" required />
                                        </div>
                                        <div class="form-group">
                                            <label class="small mb-1" for="current_password">Current Password</label>
                                            <input class="form-control py-4" id="current_password" name="current_password" type="password" placeholder="Enter current password" required />
                                        </div>
                                        <div class="form-group">
                                            <label class="small mb-1" for="new_password">New Password</label>
                                            <input class="form-control py-4" id="new_password" name="new_password" type="password" placeholder="Enter new password" required />
                                        </div>
                                        <div class="form-group mt-4 mb-0">
                                            <button class="btn btn-primary btn-block" type="submit">Change Password</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>
</html>
