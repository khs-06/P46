<?php
session_start();
require_once('config.php');
$error = '';
$success = '';
$validationError = '';

function generateOTP($length = 6)
{
    return str_pad(random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
}

function sendOTPEmail($email, $otp)
{
    $subject = "FindBrick Password Reset OTP";
    $message = "Hi,<br><br>Your OTP for resetting your password on FindBrick is: <b>$otp</b><br><br>This OTP is valid for 10 minutes.<br><br>If you did not request a reset, please ignore this email.";
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: FindBrick <noreply@findbrick.com>\r\n";
    mail($email, $subject, $message, $headers);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    if (empty($email)) {
        $validationError = "Please enter your email address.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $validationError = "Please enter a valid email address.";
    } else {
        // Check user exists and is active
        $stmt = $conn->prepare("SELECT uid FROM user WHERE uemail = ? AND email_verified = 1 AND status = 'active' LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            // Generate OTP and set expiry
            $otp = generateOTP(6);
            $otp_expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));
            $stmt->bind_result($id);
            $stmt->fetch();
            // Store OTP in DB
            $update = $conn->prepare("UPDATE user SET reset_password_otp = ?, reset_password_otp_expiry = ? WHERE uid = ?");
            $update->bind_param("ssi", $otp, $otp_expiry, $id);
            if ($update->execute()) {
                sendOTPEmail($email, $otp);
                $_SESSION['reset_email'] = $email;
                header("Location: password_reset.php?email=" . urlencode($email));
                exit();
            } else {
                $error = "Failed to start password reset. Please try again.";
            }
            $update->close();
        } else {
            $error = "No account found with this email.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Forgot Password - FindBrick</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="package/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/fb-logo.png">
</head>

<body class="login-body bg-url" style="background-image: url('images/login2.jpg');">
    <br><br><br><br><br>
    <div class="login-container shadow hover-zoomer">
        <h3 class="login-title text-center">Forgot Password</h3>
        <?php if ($validationError): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($validationError) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" id="forgotPassForm" novalidate>
            <div class="form-group">
                <label for="email">Registered Email Address</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required autofocus value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-login btn-block mt-3">Send OTP</button>
            <div class="text-center mt-3">
                <a href="login.php" class="form-link">Back to Login</a> |
                <a href="register.php" class="form-link">Create Account</a>
            </div>
        </form>
    </div>
    <!-- Bootstrap JS + dependencies  -->
    <script src="package/Jquery/dist/jquery.slim.min.js"></script>
    <!-- <script src="package/popper/dist/popper.min.js"></script>      -->
    <script src="package/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="js/all.min.js"></script>
</body>

</html>