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
            <h2 class="section-title text-center mb-4">Terms & Conditions</h2>
            <div class="card p-4">
                <h4>Welcome to FindBrick!</h4>
                <p>By using our website, you agree to comply with and be bound by the following terms and conditions:</p>
                <ul>
                    <li>All property information is provided by owners or agents and is for informational purposes only.</li>
                    <li>FindBrick is not responsible for any transactions between users and property owners or agents.</li>
                    <li>Users must not post false, misleading, or fraudulent listings.</li>
                    <li>We reserve the right to remove any content or listings that violate our policies.</li>
                    <li>Personal information provided is subject to our <a href="privacy.php">Privacy Policy</a>.</li>
                    <li>Any disputes arising from use of the site will be governed by applicable local laws.</li>
                </ul>
                <p>For full details, please contact our support team.</p>
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