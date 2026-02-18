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
    <link rel="shortcut icon" href="images/fb-logo.png">
    <link rel="stylesheet" href="package/bootstrap/dist/css/bootstrap.min.css">

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
            <h2 class="section-title text-center mb-4">Privacy Policy</h2>
            <div class="card p-4">
                <h4>Your Privacy Matters</h4>
                <p>FindBrick is committed to protecting your privacy. This policy explains how we use your data:</p>
                <ul>
                    <li>We collect personal information only when you register, list a property, or contact us.</li>
                    <li>Your information is used to provide services and communicate with you regarding your account or property listings.</li>
                    <li>We do not share your personal data with third parties except as required by law or to provide our services.</li>
                    <li>Cookies may be used to enhance your browsing experience.</li>
                    <li>You can request to delete your data by contacting our support team.</li>
                </ul>
                <p>If you have any questions, please visit our <a href="support.php">Support page</a>.</p>
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