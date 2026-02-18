<?php
session_start();
$error = '';
$success = '';
$validationError = '';

require_once('config.php');

function generateOTP($length = 6)
{
    return str_pad(random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
}

function agentornot($user_type, $conn,$image_name)
{
    // return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'Agent';
    if ($user_type == 'Agent' || $user_type == 'agent' || $user_type == 'Builder' || $user_type == 'builder') {
        $stmt = $conn->prepare("select uid from user where uemail=? limit 1");
        $stmt->execute([$_SESSION['pending_email']]);
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $id = $row['uid'];
        $db_name = $_POST['name'];
        $user_type = $_POST['user_type'];
        $agent_phone = $_POST['phone'];
        $db_email = $_POST['email'];
        $imagename = $image_name;
        $stmt = $conn->prepare("INSERT INTO agent (user_id, name, phone, utype, email, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $id, $db_name, $agent_phone, $user_type, $db_email, $imagename);
        $stmt->execute();
    }
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $user_type = $_POST['user_type'] ?? '';
    $phone = preg_replace('/\D/', '', $_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $image_name = '';

    // Validation
    if (empty($name) || empty($email) || empty($user_type) || empty($phone) || empty($password) || empty($confirm_password)) {
        $validationError = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $validationError = "Please enter a valid email address.";
    } elseif ($password !== $confirm_password) {
        $validationError = "Passwords do not match.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        $validationError = "Password must be at least 8 characters, include uppercase, lowercase, number, and special character.";
    } elseif (!preg_match('/^\d{10}$/', $phone)) {
        $validationError = "Please enter a valid phone number.";
    } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $validationError = "Please upload a profile image.";
    } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
        $validationError = "Image size should not exceed 2MB.";
    } else {
        $allowed_types = ['image/jpeg', 'image/png'];
        if (in_array($_FILES['image']['type'], $allowed_types)) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_name = uniqid('uploads/users/user_', true) . '.' . $ext;
            $upload_dir = 'admin/';
            if (!is_dir($upload_dir)) {
                if (!mkdir($upload_dir, 0777, true)) {
                    $validationError = "Failed to create upload directory.";
                }
            }
            $upload_path = $upload_dir . $image_name;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $validationError = "Failed to upload image. Please try again.";
            }
        } else {
            $validationError = "Please upload a valid image file (JPG, PNG).";
        }
    }

    if (empty($validationError)) {
        // Check if email exists and is verified
        $stmt = $conn->prepare("SELECT uid, email_verified FROM user WHERE uemail = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "This email is already registered and verified. Please login or use another email.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $otp = generateOTP(6);
            $otp_expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

            $stmt = $conn->prepare("INSERT INTO user (uname, uemail, utype, uphone, upassword, uimage, email_verified, status, verify_otp, otp_expiry) VALUES (?, ?, ?, ?, ?, ?, 0, 'pending', ?, ?)");
            $stmt->bind_param("ssssssss", $name, $email, $user_type, $phone, $hashed_password, $image_name, $otp, $otp_expiry);
            if ($stmt->execute()) {
                sendOTPEmail($email, $otp);
                $_SESSION['pending_email'] = $email;
                agentornot($user_type, $conn, $image_name);
                header("Location: verify_email.php");
                exit();
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register - FindBrick</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="package/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/fb-logo.png">

</head>

<body class="login-body bg-url" style="background-image: url('images/login2.jpg');">
    <div class="register-container shadow hover-zoomer">
        <h3 class="login-title text-center">Create Your FindBrick Account</h3>
        <?php if ($validationError): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($validationError) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" id="registerForm" autocomplete="off" novalidate enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Full Name*</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Your name" maxlength="30" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                <span class="invalid-feedback" id="nameError"></span>
            </div>
            <div class="form-group">
                <label for="user_type">Account Type*</label>
                <select class="form-control" id="user_type" name="user_type" required>
                    <option value="">-- Select Type --</option>
                    <option value="user" <?= ($_POST['user_type'] ?? '') === 'user' ? 'selected' : '' ?>>User</option>
                    <option value="agent" <?= ($_POST['user_type'] ?? '') === 'agent' ? 'selected' : '' ?>>Agent</option>
                    <option value="builder" <?= ($_POST['user_type'] ?? '') === 'builder' ? 'selected' : '' ?>>Builder</option>
                </select>
                <span class="invalid-feedback" id="userTypeError"></span>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number*</label>
                <input type="tel" class="form-control" id="phone" name="phone" placeholder="e.g. +919999999999" required minlength="10" maxlength="10" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                <span class="invalid-feedback" id="phoneError"></span>
            </div>
            <div class="form-group">
                <label for="email">Email address*</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                <span class="invalid-feedback" id="emailError"></span>
            </div>
            <div class="form-group mb-2">
                <label for="password">Password*</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required minlength="8" maxlength="20" value="<?= htmlspecialchars($_POST['password'] ?? '') ?>">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary btn-light" type="button" id="togglePassword" tabindex="-1">
                            <span class="fa-solid fa-eye"></span>
                        </button>
                    </div>
                </div>
                <span class="invalid-feedback" id="passwordError"></span>
            </div>
            <div class="form-group mb-2">
                <label for="confirm_password">Confirm Password*</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Re-enter password" required minlength="8" maxlength="20" value="<?= htmlspecialchars($_POST['confirm_password'] ?? '') ?>">
                <span class="invalid-feedback" id="confirmPasswordError"></span>
            </div>
            <div class="form-group">
                <label for="image">Profile Image* (JPG, PNG)</label>
                <input type="file" class="form-control-file" id="image" name="image" accept="image/jpeg,image/png" required value="<?= htmlspecialchars($_FILES['image']['name'] ?? '') ?>">
                <span class="invalid-feedback" id="imageError"></span>
            </div>
            <button type="submit" class="btn btn-login btn-block mt-3">Register</button>
            <div class="text-center mt-3">
                <span>Already have an account?</span>
                <a href="verify_email.php" class="form-link">Verify Email</a>
                <span> And </span>
                <a href="login.php" class="form-link">Login</a>
            </div>
        </form>
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