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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['insert'])) {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $content = trim($_POST['content']);
    $uid = $_SESSION['uid'];

    if (!empty($name) && !empty($phone) && !empty($content)) {
        $stmt = $conn->prepare("INSERT INTO feedback (uid, fdescription, status) VALUES (?, ?, 0)");
        if ($stmt) {
            $stmt->bind_param("is", $uid, $content);
            if ($stmt->execute()) {
                $msg = "<div class='alert alert-success'>Feedback sent successfully.</div>";
            } else {
                $error = "<div class='alert alert-warning'>Could not send feedback. Please try again.</div>";
            }
            $stmt->close();
        } else {
            $error = "<div class='alert alert-warning'>Database error: " . htmlspecialchars($con->error) . "</div>";
        }
    } else {
        $error = "<div class='alert alert-warning'>Please fill all the fields.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Profile - Homex</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="images/fb-logo.png">
    <link rel="stylesheet" href="package/bootstrap/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="css/all.min.css">
    <!-- Google Fonts: Poppins & Playfair Display -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div id="page-wrapper">
        <?php include("include/header.php"); ?>
        <br><br><br><br><br>
        
        <div class="container my-5 text-center">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <img src="images/404-real-estate.png" class="img-fluid mb-4" alt="404 Not Found" style="max-width:300px;">
                    <h1 class="display-4">404 - Page Not Found</h1>
                    <p class="lead">Sorry, the page you are looking for does not exist. You may have followed a broken link or entered a URL that doesn't exist on our site.</p>
                    <a href="index.php" class="btn btn-search px-5 mt-3">Go to Home</a>
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
        if (typeof window.history.pushState == 'function') {
            window.history.pushState({}, "Hide", '<?php echo $_SERVER['PHP_SELF']; ?>');
        }
    </script>
</body>

</html>