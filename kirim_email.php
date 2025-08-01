<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);

    $to = 'gilangfikrir@gmail.com';
    $subject = 'Request for Account Registration';
    $body = "From: $user_email\n\nMessage:\n$message";
    $headers = "From: $user_email";

    if (mail($to, $subject, $body, $headers)) {
        $success = "Email berhasil dikirim. Kami akan segera menghubungi Anda.";
    } else {
        $error = "Email gagal dikirim. Silakan coba lagi nanti.";
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
    <title>PROSTOCK - Kirim Email</title>
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
</head>
<body>
<div id="layoutAuthentication">
    <div id="layoutAuthentication_content">
        <main>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-5">
                        <div class="card shadow-lg border-0 rounded-lg mt-5">
                            <div class="card-header">
                                <h3 class="text-center font-weight-light my-4">Kirim Email untuk Registrasi Akun</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                if (isset($success)) {
                                    echo '<div class="alert alert-success">' . htmlspecialchars($success) . '</div>';
                                }
                                if (isset($error)) {
                                    echo '<div class="alert alert-danger">' . htmlspecialchars($error) . '</div>';
                                }
                                ?>
                                <form action="kirim_email.php" method="post">
                                    <div class="form-group">
                                        <label class="small mb-1" for="inputEmailAddress">Email Anda</label>
                                        <input class="form-control py-4" id="inputEmailAddress" name="email" type="email" placeholder="Enter your email address" required />
                                    </div>
                                    <div class="form-group">
                                        <label class="small mb-1" for="inputMessage">Pesan</label>
                                        <textarea class="form-control py-4" id="inputMessage" name="message" rows="4" placeholder="Enter your message" required></textarea>
                                    </div>
                                    <div class="form-group d-flex align-items-center justify-content-between mt-4 mb-0">
                                        <button class="btn btn-primary" type="submit">Kirim Email</button>
                                    </div>
                                </form>
                            </div>
                            <div class="card-footer text-center">
                                <div class="small"><a href="login.php">Kembali ke Halaman Login</a></div>
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
</body>
</html>
