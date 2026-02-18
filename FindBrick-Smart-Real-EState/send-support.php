<?php
include("session-check.php");
// ini_set('session.cache_limiter', 'public');
// session_cache_limiter(false);
// session_start();
// include("config.php");
// if (!isset($_SESSION['uemail'])) {
//     header("location:login.php");
//     exit;
// }

// Simple form handler with email sending logic
$success = false;
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $message = trim($_POST["message"] ?? "");

    if ($name && $email && $message && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Prepare email
        $to = "findbrick26@gmail.com"; // Replace with your support email
        $subject = "Support Request from $name";
        $body = "Name: $name\nEmail: $email\n\nMessage:\n$message";
        $headers = "From: FindBrick <$email>\r\nReply-To: FindBrick26@gmail.com\r\n";

        // Send email (returns true on success)
        if (mail($to, $subject, $body, $headers)) {
            $success = true;
        } else {
            $error = "Sorry, there was an error sending your message. Please try again later.";
        }
    } else {
        $error = "Please fill all fields correctly.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Profile - FindBrick</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="images/fb-logo.png">
    <link rel="stylesheet" href="package/bootstrap/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="css/all.min.css">
    <!-- Google Fonts: Poppins & Playfair Display -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png">

<style>
        /* Custom CSS for modern support feedback */
        .support-feedback-card {
            border-radius: 1rem;
            box-shadow: 0 4px 24px rgba(44, 62, 80, 0.08);
            background: #fff;
            padding: 2rem;
            margin: 2rem auto;
            max-width: 500px;
            text-align: center;
        }

        .support-feedback-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 1rem;
        }

        .support-feedback-icon.error {
            color: #dc3545;
        }
    </style>

</head>

<body>
    <div id="page-wrapper">
        <?php include("include/header.php"); ?>
        <br><br><br><br><br>
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <?php if ($success): ?>
                        <div class="support-feedback-card animate__animated animate__fadeInDown">
                            <div class="support-feedback-icon"><i class="fas fa-check-circle"></i></div>
                            <h2 class="font-weight-bold mb-3">Thank You!</h2>
                            <p>Your message has been received. Our support team will contact you soon.</p>
                            <a href="index.php" class="btn btn-search mt-4 px-4">Back to Home</a>
                        </div>
                    <?php else: ?>
                        <?php if ($error): ?>
                            <div class="support-feedback-card animate__animated animate__shakeX">
                                <div class="support-feedback-icon error"><i class="fas fa-exclamation-circle"></i></div>
                                <h4 class="text-danger mb-2">Error</h4>
                                <p><?php echo $error; ?></p>
                            </div>
                        <?php endif; ?>
                        <div class="card shadow-lg border-0 p-4">
                            <h2 class="mb-3 font-weight-bold text-center">Contact Support</h2>
                            <form id="supportForm" method="post" action="send-support.php" novalidate>
                                <div class="form-group">
                                    <label for="supportName">Name</label>
                                    <input type="text" class="form-control" id="supportName" name="name" required>
                                    <div class="invalid-feedback">Please enter your name.</div>
                                </div>
                                <div class="form-group">
                                    <label for="supportEmail">Email</label>
                                    <input type="email" class="form-control" id="supportEmail" name="email" required>
                                    <div class="invalid-feedback">Please enter a valid email.</div>
                                </div>
                                <div class="form-group">
                                    <label for="supportMessage">Message</label>
                                    <textarea class="form-control" id="supportMessage" name="message" rows="5" required></textarea>
                                    <div class="invalid-feedback">Please enter your message.</div>
                                </div>
                                <button type="submit" class="btn btn-search px-4 w-100">Send Message</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php include("include/footer.php"); ?>
        <!-- <a href="#" class="bg-secondary text-white hover-text-secondary" id="scroll"><i class="fas fa-angle-up"></i></a> -->
    </div>
    <script src="package/Jquery/dist/jquery.slim.min.js"></script>
    <script src="package/popper/dist/popper.min.js"></script>
    <script src="package/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="package/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/all.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        // Custom JS for Bootstrap 4 form validation
        (function() {
            'use strict';
            var form = document.getElementById('supportForm');
            if (form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            }
        })();
    </script>
</body>

</html>