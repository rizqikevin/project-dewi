<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitasi dan validasi input
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $nama = htmlspecialchars($_POST['nama']);
    $role = 'User'; // Set default role to 'User'

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Membuat koneksi ke database
    include 'koneksi.php';

    // Memeriksa koneksi
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    // Menyiapkan pernyataan SQL untuk memeriksa apakah email sudah digunakan
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "Email sudah digunakan. Silakan gunakan email lain.";
    } else {
        // Menyiapkan pernyataan SQL untuk memasukkan data pengguna baru
        $stmt = $conn->prepare("INSERT INTO users (email, password, nama, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $email, $hashed_password, $nama, $role);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Akun berhasil dibuat. Silakan login.";
            // Arahkan ke halaman yang sama setelah notifikasi
            header("Location: register.php#success");
            exit;
        } else {
            $error = "Pendaftaran gagal. Silakan coba lagi.";
        }
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROSTOCK - Register</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
    <style>
        body {
            background: url('assets/img/bg-masthead.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .card {
            background-color: rgba(255, 255, 255, 0.85);
        }
    </style>
    <script>
    // Menampilkan notifikasi pop-up jika ada pesan sukses dari PHP session
    <?php if (isset($_SESSION['success'])): ?>
        window.onload = function() {
            alert("<?php echo $_SESSION['success']; ?>");
            <?php unset($_SESSION['success']); ?>
        };
    <?php endif; ?>
    </script>
</head>
<body>
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4">Create Account</h3>
                                </div>
                                <div class="card-body">
                                    <?php
                                    if (isset($error)) {
                                        echo '<div class="alert alert-danger">' . htmlspecialchars($error) . '</div>';
                                    }
                                    ?>
                                    <form action="register.php" method="post">
                                        <div class="form-group">
                                            <label class="small mb-1" for="inputFirstName">Full Name</label>
                                            <input class="form-control py-4" id="inputFirstName" name="nama" type="text" placeholder="Enter full name" required />
                                        </div>
                                        <div class="form-group">
                                            <label class="small mb-1" for="inputEmailAddress">Email</label>
                                            <input class="form-control py-4" id="inputEmailAddress" name="email" type="email" aria-describedby="emailHelp" placeholder="Enter email address" required />
                                        </div>
                                        <div class="form-group">
                                            <label class="small mb-1" for="inputPassword">Password</label>
                                            <input class="form-control py-4" id="inputPassword" name="password" type="password" placeholder="Enter password" required />
                                        </div>
                                        <div class="form-group mt-4 mb-0">
                                            <button class="btn btn-primary btn-block" type="submit">Create Account</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center">
                                    <div class="small"><a href="login.php">Have an account? Go to login</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer">
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Gilang FR 2024</div>
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
</body>
</html>
