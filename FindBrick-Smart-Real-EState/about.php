<?php
include("session-check.php");
// ini_set('session.cache_limiter', 'public');
// session_cache_limiter(false);
// session_start();
// include("config.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>About Us - FindBrick</title>
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
        <br><br><br><br>
        <div class="banner-full-row page-banner" style="background-image:url('images/breadcromb.jpg'); min-height: 250px;">
            <div class="container">
                <div class="row align-items-center py-5">
                    <div class="col-md-6">
                        <h2 class="page-name text-white text-uppercase mb-0"><b>About Us</b></h2>
                    </div>
                    <div class="col-md-6">
                        <nav aria-label="breadcrumb" class="float-md-right">
                            <ol class="breadcrumb bg-transparent m-0 p-0">
                                <li class="breadcrumb-item text-white"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active">About Us</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <div class="full-row py-5">
            <div class="container">
                <?php
                $query = $conn->query("SELECT * FROM about");
                if ($query) {
                    while ($row = $query->fetch_assoc()) {
                ?>
                        <div class="row mb-5">
                            <div class="col-lg-7">
                                <h3 class="text-secondary mb-4"><?php echo htmlspecialchars($row['title']); ?></h3>
                                <div class="about-content">
                                    <?php echo nl2br($row['description']); ?>
                                </div>
                            </div>
                            <div class="col-lg-5 text-center">
                                <img class="img-fluid rounded shadow" src="admin/uploads/about/<?php echo htmlspecialchars($row['image']); ?>" alt="about image">
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo "<div class='alert alert-warning'>Could not fetch about information. Please try later.</div>";
                }
                ?>
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
</body>

</html>