<?php
session_start();
require_once('config.php');
$error = '';
$success = '';
$validationError = '';

$email = $_SESSION['reset_email'] ?? ($_GET['email'] ?? '');

if (empty($email)) {
    $error = "Session expired or invalid. Please restart the password reset process.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($email)) {
        $error = "Session expired or invalid. Please restart the password reset process.";
    } elseif (empty($otp) || empty($new_password) || empty($confirm_password)) {
        $validationError = "Please fill in all fields.";
    } elseif ($new_password !== $confirm_password) {
        $validationError = "Passwords do not match.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $new_password)) {
        $validationError = "Password must be at least 8 characters, including uppercase, lowercase, number, and special character.";
    } elseif (!preg_match('/^\d{6}$/', $otp)) {
        $validationError = "Please enter a valid 6-digit OTP.";
    } else {
        // Check OTP and expiry
        $stmt = $conn->prepare("SELECT uid, reset_password_otp_expiry FROM user WHERE uemail = ? AND reset_password_otp = ? LIMIT 1");
        $stmt->bind_param("ss", $email, $otp);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $reset_otp_expiry);
            $stmt->fetch();
            if (strtotime($reset_otp_expiry) >= time()) {
                // Update password, clear otp
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update = $conn->prepare("UPDATE user SET upassword = ?, reset_password_otp = NULL, reset_password_otp_expiry = NULL WHERE uid = ?");
                $update->bind_param("si", $hashed_password, $id);
                if ($update->execute()) {
                    unset($_SESSION['reset_email']);
                    // Show success and redirect to login
                    $success = "Password reset successful! Redirecting to login...";
                    echo "<script>alert('Password reset successful!');window.location.href='login.php';</script>";
                    exit();
                } else {
                    $error = "Failed to reset password. Please try again.";
                }
                $update->close();
            } else {
                $error = "OTP expired. Please restart the password reset process.";
            }
        } else {
            $error = "Invalid OTP or email.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - FindBrick</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 4 -->
     <link rel="stylesheet" href="package/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/fb-logo.png">
</head>
<body class="login-body bg-url" style="background-image: url('images/login2.jpg');">
    <div class="login-container shadow hover-zoomer">
        <h3 class="login-title text-center">Reset Password</h3>
        <?php if ($validationError): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($validationError) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <form method="POST" id="resetPassForm" autocomplete="off" novalidate>
            <div class="form-group">
                <label for="otp">OTP (sent to your email)</label>
                <input type="tel" class="form-control" id="otp" name="otp" placeholder="Enter OTP" required minlength="6" maxlength="6" pattern="\d{6}">
            </div>
            <div class="form-group mb-2">
                <label for="new_password">New Password</label>
                <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Enter new password" required minlength="8">
            </div>
            <div class="form-group mb-2">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Re-enter new password" required minlength="8">
            </div>
            <button type="submit" class="btn btn-login btn-block mt-3">Reset Password</button>
            <div class="text-center mt-3">
                <a href="login.php" class="form-link">Back to Login</a>
            </div>
        </form>
    </div>
    <!-- Bootstrap JS + dependencies  -->
    <script src="package/Jquery/dist/jquery.slim.min.js"></script>
    <!-- <script src="package/popper/dist/popper.min.js"></script>      -->
    <script src="package/bootstrap/dist/js/bootstrap.min.js"></script>   
    <script src="js/all.min.js"></script>
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