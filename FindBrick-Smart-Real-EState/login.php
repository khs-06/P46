<?php
session_start();

// If user is already logged in, redirect
if (isset($_SESSION['uid'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$validationError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once('config.php');

    // Sanitize input
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    // Server-side validation
    if (empty($email) || empty($password)) {
        $validationError = "Please fill in both email and password.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $validationError = "Please enter a valid email address.";
    } elseif (strlen($password) < 8) {
        $validationError = "Password must be at least 8 characters.";
    } else {
        // Check user in DB
        $stmt = $conn->prepare("SELECT uid, uname, uemail, upassword, email_verified, utype, status FROM user WHERE uemail = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $db_name, $db_email, $db_password, $email_verified, $user_type, $status);
            $stmt->fetch();
            if ((int)$email_verified !== 1 || $status !== 'active') {
                $error = "Your email is not verified or your account is not active. Please verify your email.";
            } elseif (password_verify($password, $db_password)) {
                // Regenerate session ID for security
                session_regenerate_id(true);
                $_SESSION['uid'] = $id;
                $_SESSION['uemail'] = $db_email;
                
                header("Location: index.php");
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "Invalid email !.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - FindBrick</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 4 -->
     <link rel="stylesheet" href="package/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/fb-logo.png">

</head>
<body class="login-body bg-url" style="background-image: url('images/login2.jpg');">
    <div class="login-container shadow hover-zoomer">
        <h3 class="login-title text-center">Sign In to FindBrick</h3>
        <?php if ($validationError): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($validationError) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" id="loginForm"  novalidate>
            <div class="form-group">
                <label for="email">Email address*</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required autofocus value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                <span class="invalid-feedback" id="emailError"></span>
            </div>
            <div class="form-group mb-2">
                <label for="password">Password*</label>
                <div class="input-group">
                  <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                  <div class="input-group-append">
                    <button class="btn btn-outline-secondary btn-light" type="button" id="togglePassword" tabindex="-1">
                      <span class="fa-solid fa-eye"></span>
                    </button>
                  </div>
                </div>
                <span class="invalid-feedback" id="passwordError"></span>
            </div>
            <button type="submit" class="btn btn-login btn-block mt-3">Login</button>
            <div class="text-center mt-3">
                <a href="password_forgot.php" class="form-link">Forgot password?</a>
            </div>
            <div class="text-center mt-2">
                <span>Don't have an account?</span>
                <a href="verify_email.php" class="form-link">Verify Email</a>
                <span> and </span>
                <a href="register.php" class="form-link">Register</a>
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