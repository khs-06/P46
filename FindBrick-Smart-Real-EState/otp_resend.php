<?php
session_start();
require_once('config.php');
$success = '';
$error = '';
$validationError = '';

function generateOTP($length = 6)
{
    return str_pad(random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
}

function sendOTPEmail($email, $otp)
{
    $subject = "Your FindBricks Email Verification OTP";
    $message = "Hi,<br><br>Your OTP for verifying your email address on FindBricks is: <b>$otp</b><br><br>This OTP is valid for 10 minutes.<br><br>If you did not register, just ignore this email.";
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: FindBrick <noreply@findbrick.com>\r\n";
    mail($email, $subject, $message, $headers);
}

$email = $_GET['email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    if (empty($email)) {
        $validationError = "Please enter your email address.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $validationError = "Please enter a valid email address.";
    } else {
        // Check if user exists, is pending and not yet verified
        $stmt = $conn->prepare("SELECT uid FROM user WHERE uemail = ? AND email_verified = 0 AND status = 'pending' LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            // Generate new OTP and expiry
            $otp = generateOTP(6);
            $otp_expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

            // Update OTP and expiry in DB
            $stmt->bind_result($id);
            $stmt->fetch();
            $update = $conn->prepare("UPDATE user SET verify_otp = ?, otp_expiry = ? WHERE uid = ?");
            $update->bind_param("ssi", $otp, $otp_expiry, $id);
            if ($update->execute()) {
                sendOTPEmail($email, $otp);
                $_SESSION['pending_email'] = $email;
                // Redirect back to verify_email.php with email parameter
                header("Location: verify_email.php?email=" . urlencode($email));
                exit();
            } else {
                $error = "Failed to resend OTP. Please try again or contact support.";
            }
            $update->close();
        } else {
            $error = "Account not found or already verified.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Resend OTP - FindBrick</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="package/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/fb-logo.png">

</head>

<body class="login-body bg-url" style="background-image: url('images/login2.jpg');">
    <br><br><br><br><br>
    <div class="login-container shadow hover-zoomer">
        <h3 class="login-title text-center">Resend OTP</h3>
        <?php if ($validationError): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($validationError) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" id="resendotpForm" novalidate>
            <div class="form-group">
                <label for="email">Registered Email Address</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter email to resend OTP" required value="<?= htmlspecialchars($email) ?>">
            </div>
            <button type="submit" class="btn btn-login btn-block mt-3">Resend OTP</button>
        </form>
        <div class="text-center mt-3">
            <a href="verify_email.php<?= !empty($email) ? '?email=' . urlencode($email) : '' ?>" class="form-link">Back to Email Verification</a>
            <span> And </span>
            <a href="login.php" class="form-link">Login</a>
        </div>
    </div>
    <!-- Bootstrap JS + dependencies  -->
    <script src="package/Jquery/dist/jquery.slim.min.js"></script>
    <!-- <script src="package/popper/dist/popper.min.js"></script>      -->
    <script src="package/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="js/all.min.js"></script>
    <script src="js/login.js"></script>
    <script src="js/script.js"></script>
</body>

</html>