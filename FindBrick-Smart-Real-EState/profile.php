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
</head>

<body>
    <div id="page-wrapper">
        <?php include("include/header.php"); ?>
        <br><br><br><br><br>
        <div class="banner-full-row page-banner" style="background-image:url('images/breadcromb.jpg'); min-height: 300px; overflow: hidden;">
            <div class="container">
                <div class="row align-items-center py-5">
                    <div class="col-md-6">
                        <h2 class="page-name text-white text-uppercase mb-0"><b>Profile</b></h2>
                        <div class="mt-3 text-white">
                            <?php
                            $uid = $_SESSION['uid'];
                            $stmt = $conn->prepare("SELECT uname, uemail, uphone, utype, uimage FROM user WHERE uid = ?");
                            $stmt->bind_param("i", $uid);
                            $stmt->execute();
                            $stmt->bind_result($name, $email, $phone, $role, $image);
                            if ($stmt->fetch()) {
                            ?>
                                <div class="mt-3">
                                    <img src="admin/<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($name); ?>" class="img-fluid" style="width: 150px; height: 100px;">
                                </div>
                                <div class="mt-3">
                                    <p><strong>Name:</strong> <?php echo htmlspecialchars($name); ?></p>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($phone); ?></p>
                                    <p><strong>Role:</strong> <?php echo htmlspecialchars($role); ?></p>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <nav aria-label="breadcrumb" class="float-md-right">
                            <ol class="breadcrumb bg-transparent m-0 p-0">
                                <li class="breadcrumb-item text-white"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active">Profile</li>
                            </ol>
                        </nav>
                        <?php
                        if ($role === 'builder' || $role === 'agent') {
                            echo '<div class="position-relative mt-3" style="top:20px; left: 400px;"><a href="propertyadd.php"><button class="btn btn-success">Submit Property</button></a></div>';
                        }
                        ?>
                        <div class="position-relative mt-4" style="top:20px; left: 422px;"><a href="profileedit.php"><button class="btn btn-primary">Edit Profile</button></a></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="full-row py-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-7">
                        <div class="card shadow p-4 mb-4">
                            <h5 class="card-title mb-4 text-secondary">Feedback Form</h5>
                            <?php echo $msg; ?><?php echo $error; ?>
                            <form method="post" autocomplete="off">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="Enter Name" maxlength="70" required>
                                </div>
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input type="tel" name="phone" class="form-control" placeholder="Enter Phone" minlength="10" maxlength="10" pattern="^\+?\d{10,15}$" required>
                                </div>
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea class="form-control" name="content" rows="5" placeholder="Enter Description" maxlength="100" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary" name="insert">Send Feedback</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card shadow text-center p-4 bg-transparent">
                            
                                <img src="admin/<?php echo htmlspecialchars($image); ?>" alt="Profile Image" class="img-fluid position-relative mb-3" style=" width:400px;height:300px;">
                                <h5 class="mb-2"><?php echo htmlspecialchars($name); ?></h5>
                                <div class="mb-1"><b>Email:</b> <?php echo htmlspecialchars($email); ?></div>
                                <div class="mb-1"><b>Phone:</b> <?php echo htmlspecialchars($phone); ?></div>
                                <div class="mb-1"><b>Role:</b> <?php echo htmlspecialchars($role); ?></div>
                            <?php
                            
                            $stmt->close();
                            ?>
                        </div>
                    </div>
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