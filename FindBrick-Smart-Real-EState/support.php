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

$error = '';
$msg = '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Profile - FindBrick</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="images/favicon.ico">
    <link rel="stylesheet" href="package/bootstrap/dist/css/bootstrap.min.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/fb-logo.png">

    <link rel="stylesheet" href="css/all.min.css">
    <!-- Google Fonts: Poppins & Playfair Display -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">
</head>

<body>
    <div id="page-wrapper">
        <?php include("include/header.php"); ?>
        <br><br><br><br><br>
        <div class="container my-5">
            <h2 class="section-title text-center mb-4">Support & Contact Us</h2>
            <div class="card p-4">
                <h4>We're here to help!</h4>
                <p>If you have any questions, issues, or feedback, please use the form below or contact us directly.</p>
                <form method="post" action="send-support.php">
                    <div class="form-group">
                        <label for="supportName">Name</label>
                        <input type="text" autocomplete="off" class="form-control" id="supportName" name="name" required placeholder="Your Name">
                    </div>
                    <div class="form-group">
                        <label for="supportEmail">Email</label>
                        <input type="email" class="form-control" id="supportEmail" name="email" required placeholder="Your Email">
                    </div>
                    <div class="form-group">
                        <label for="supportMessage">Message</label>
                        <textarea class="form-control" id="supportMessage" name="message" rows="5" required placeholder="Your Message..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-search">Send Message</button>
                </form>
                <div class="mt-4">
                    <h5>Contact Details</h5>
                    <p>Email: support@findbrick.com</p>
                    <!-- <p>Phone: +91-XXXXXXX</p> -->
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
    <!-- <script>
        if (typeof window.history.pushState == 'function') {
            window.history.pushState({}, "Hide", '<?php echo $_SERVER['PHP_SELF']; ?>');
        }
    </script> -->
</body>

</html>