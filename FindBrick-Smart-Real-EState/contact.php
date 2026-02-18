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

$error = $msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if ($name && $email && $phone && $subject && $message) {
        $stmt = $con->prepare("INSERT INTO contact (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);
            if ($stmt->execute()) {
                $msg = "<div class='alert alert-success'>Message sent successfully.</div>";
            } else {
                $error = "<div class='alert alert-warning'>Could not send your message. Please try again.</div>";
            }
            $stmt->close();
        } else {
            $error = "<div class='alert alert-warning'>Database error: " . htmlspecialchars($con->error) . "</div>";
        }
    } else {
        $error = "<div class='alert alert-warning'>Please fill all fields.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Contact - FindBrick</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="images/favicon.ico">
    <link rel="stylesheet" href="package/bootstrap/dist/css/bootstrap.min.css">
        <link rel="shortcut icon" type="image/x-icon" href="images/fb-logo.png">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="css/all.min.css">
    <!-- Google Fonts: Poppins & Playfair Display -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">

</head>

<body>
    <div id="page-wrapper" class="main-container">
        <?php include("include/header.php"); ?>
        <br><br><br><br><br>
        <div class="banner-full-row page-banner" style="background-image:url('images/breadcromb.jpg'); min-height: 250px;">
            <div class="container">
                <div class="row align-items-center py-5">
                    <div class="col-md-6">
                        <h2 class="page-name text-white text-uppercase mb-0"><b>Contact</b></h2>
                    </div>
                    <div class="col-md-6">
                        <nav aria-label="breadcrumb" class="float-md-right">
                            <ol class="breadcrumb bg-transparent m-0 p-0">
                                <li class="breadcrumb-item text-white"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active">Contact</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <div class="full-row py-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 bg-dark text-white p-4 rounded mb-4">
                        <h3 class="mb-4">Contact Info</h3>
                        <p><i class="fas fa-map-marker-alt mr-2"></i> Address: SPS College,<br> Atkot Ring Road, opp. Bhoomi Ginning, Jasdan, Gujarat 360040</p>
                        <p><i class="fas fa-phone-alt mr-2"></i> Phone: 1010101010</p>
                        <p><i class="fas fa-envelope mr-2"></i> Email: findbrick26@gmail.com</p>
                    </div>
                    <div class="col-lg-1"></div>
                    <div class="col-lg-7">
                        <div class="card shadow p-4">
                            <h4 class="mb-4 text-secondary text-center">Get In Touch</h4>
                            <?php echo $msg; ?><?php echo $error; ?>
                            <form method="post" autocomplete="off">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <input type="text" name="name" class="form-control" placeholder="Your Name*" maxlength="100" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <input type="email" name="email" class="form-control" placeholder="Email Address*" maxlength="150" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <input type="tel" name="phone" class="form-control" placeholder="Phone*" maxlength="15" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <input type="text" name="subject" class="form-control" placeholder="Subject*" maxlength="100" required>
                                    </div>
                                    <div class="form-group col-12">
                                        <textarea name="message" class="form-control" rows="5" placeholder="Type Comments..." maxlength="500" required></textarea>
                                    </div>
                                </div>
                                <button type="submit" name="send" class="btn btn-primary px-5">Send Message</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <br><br><br>
                <div class="">
                    <!-- <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d29921.88989279091!2d72.89392697798161!3d20.373147326844283!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be0d1d69db97345%3A0x8bc4433aecadadfd!2sROFEL%20ARTS%20%26%20COMMERCE%20COLLEGE!5e0!3m2!1sen!2sin!4v1585740130321!5m2!1sen!2sin" width="100%" height="350" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe> -->
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5100.472934062102!2d71.16070467314977!3d22.012084564354364!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395858ed71e3905f%3A0x87e641f330c1ec16!2ssps%20sankul!5e0!3m2!1sen!2sin!4v1756063827169!5m2!1sen!2sin" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" tabindex="0"></iframe>
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
</body>

</html>