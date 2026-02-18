<?php
session_start();
require_once('config.php');
$success = '';
$error = '';
$validationError = '';

// Get user email from session or GET
$email = $_SESSION['pending_email'] ?? ($_GET['email'] ?? '');

if (empty($email)) {
    echo "<script>alert('Session expired. Please verify your email again and request a new OTP.'); window.location.href='otp_resend.php';</script>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp'] ?? '');

    if (empty($otp)) {
        $validationError = "Please enter the OTP.";
    } else {
        // Check OTP and expiry
        $stmt = $conn->prepare("SELECT uid, otp_expiry FROM user WHERE uemail = ? AND verify_otp = ? AND email_verified = 0 AND status = 'pending' LIMIT 1");
        $stmt->bind_param("ss", $email, $otp);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $otp_expiry);
            $stmt->fetch();

            if (strtotime($otp_expiry) >= time()) {
                // Verify email and activate user
                $update = $conn->prepare("UPDATE user SET email_verified = 1, status = 'active', verify_otp = NULL, otp_expiry = NULL WHERE uid = ?");
                $update->bind_param("i", $id);
                if ($update->execute()) {
                    unset($_SESSION['pending_email']);
                    header("Location: login.php?verified=1");
                    exit();
                } else {
                    $error = "Verification failed. Please try again or contact support.";
                }
                $update->close();
            } else {
                $error = "OTP expired. Please resend OTP and try again.";
            }
        } else {
            $error = "Invalid OTP";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Email Verification - FindBrick</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
     <link rel="shortcut icon" type="image/x-icon" href="images/fb-logo.png">
   
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="package/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="login-body bg-url" style="background-image: url('images/login2.jpg');">
    <div class="login-container shadow hover-zoomer">
        <h3 class="login-title text-center">Verify Your Email</h3>
        <?php if ($validationError): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($validationError) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <form method="POST" id="otpForm" autocomplete="off" novalidate>
            <div class="form-group">
                <label for="otp">OTP*</label>
                <input type="tel" class="form-control" id="otp" name="otp" placeholder="Enter OTP" required minlength="6" maxlength="6" pattern="\d{6}">
            </div>
            <button type="submit" class="btn btn-login btn-block mt-3">Verify OTP</button>
            <div class="text-center mt-3">
                <span>Already verified?</span>
                <a href="login.php" class="form-link">Login</a>
                <span> and </span>
                <a href="register.php" class="form-link">Register</a>
            </div>
        </form>
        <div class="text-center mt-3">
            <span>Didn't receive an OTP or OTP expired?</span>
            <a href="otp_resend.php?email=<?= urlencode($email) ?>" class="form-link">Resend OTP</a>
        </div>
    </div>
    <!-- Bootstrap JS + dependencies  -->
    <script src="package/Jquery/dist/jquery.slim.min.js"></script>
    <!-- <script src="package/popper/dist/popper.min.js"></script>      -->
    <script src="package/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="js/all.min.js"></script>
    <script src="js/login.js"></script>
    <script src="js/script.js"></script>
    <script>
        $('#otp').on('keypress', function(e) {
            // Allow: backspace, delete, arrows, tab
            if ($.inArray(e.keyCode, [8, 9, 37, 39, 46]) !== -1) return;
            // Only digits (0-9)
            if (e.which < 48 || e.which > 57) e.preventDefault();
        });
    </script>
</body>

</html>