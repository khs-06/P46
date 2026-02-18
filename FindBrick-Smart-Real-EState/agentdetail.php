<?php
include("session-check.php");

// Get agent id from URL
// $agent_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
// if ($agent_id <= 0) {
// header("Location: agent.php");
// exit;
// }
$agent_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
// Fetch agent info
$stmt = $conn->prepare("SELECT a.*, u.uname, u.utype FROM agent a LEFT JOIN user u ON a.user_id=u.uid WHERE a.id=?");
$stmt->bind_param("i", $agent_id);
$stmt->execute();
$result = $stmt->get_result();
$agent = $result->fetch_assoc();

if (!$agent) {
    echo '<div class="container py-5 text-center text-danger">Agent not found.</div>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Agent Details - FindBrick</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <link rel="shortcut icon" href="images/favicon.ico">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/style.css"> -->
    <link rel="stylesheet" href="package/bootstrap/dist/css/bootstrap.min.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/fb-logo.png">

    <link rel="stylesheet" href="css/all.min.css">
    <!-- Google Fonts: Poppins & Playfair Display -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">

    <style>
        .agent-profile-img {
            /* left: 10px; */
            width: 100%;
            max-width: 280px;
            height: 280px;
            object-fit: cover;
            border-radius: 20px;
        }

        .agent-detail-social a {
            color: #6777ef;
            margin: 0 7px;
            font-size: 1.25rem;
        }

        .agent-detail-social a:hover {
            color: #495057;
        }

        .property-thumb {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
        }
        .card-img {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card-img:hover {
            transform: translateY(-6px) scale(1.03);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>
    <?php include("include/header.php"); ?>
    <br><br><br><br>
    <div class="banner-full-row page-banner" style="background-image:url('images/breadcromb.jpg'); min-height: 220px;">
        <div class="container">
            <div class="row align-items-center py-4">
                <div class="col-md-6">
                    <h2 class="page-name text-white text-uppercase mb-0"><b>Agent Details</b></h2>
                </div>
                <div class="col-md-6">
                    <nav aria-label="breadcrumb" class="float-md-right">
                        <ol class="breadcrumb bg-transparent m-0 p-0">
                            <li class="breadcrumb-item text-white"><a href="index.php">Home</a></li>
                            <li class="breadcrumb-item"><a href="agent.php" class="text-white">Agents</a></li>
                            <li class="breadcrumb-item active">Agent Detail</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="container py-5">
        <div class="row">
            <!-- AGENT INFO -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow p-3 text-center">
                    <img src="admin/<?php echo htmlspecialchars($agent['image'] ?: 'images/team/default.jpg'); ?>"
                        class="agent-profile-img m-3 card-img" alt="<?php echo htmlspecialchars($agent['name']); ?>">
                    <h4 class="mb-1"><?php echo htmlspecialchars($agent['name']); ?></h4>
                    <div class="mb-2 text-muted"><?php echo htmlspecialchars($agent['specialty']); ?></div>
                    <div class="mb-2 text-primary font-weight-bold"><?php echo htmlspecialchars($agent['city']); ?></div>
                    <div class="mb-2"><i class="fa fa-phone mr-1"></i> <?php echo htmlspecialchars($agent['phone']); ?></div>
                    <div class="mb-2"><i class="fa fa-envelope mr-1"></i> <?php echo htmlspecialchars($agent['email']); ?></div>
                    <div class="agent-detail-social mb-3">
                        <?php if ($agent['facebook']): ?><a href="<?php echo htmlspecialchars($agent['facebook']); ?>" target="_blank"><i class="fab fa-facebook-f"></i></a><?php endif; ?>
                        <?php if ($agent['twitter']): ?><a href="<?php echo htmlspecialchars($agent['twitter']); ?>" target="_blank"><i class="fab fa-twitter"></i></a><?php endif; ?>
                        <?php if ($agent['linkedin']): ?><a href="<?php echo htmlspecialchars($agent['linkedin']); ?>" target="_blank"><i class="fab fa-linkedin-in"></i></a><?php endif; ?>
                    </div>
                    <div class="text-secondary small mb-2">Joined: <?php echo date("F Y", strtotime($agent['created_at'])); ?></div>
                    <div class="text-secondary small"><?php echo htmlspecialchars($agent['utype']).' : '.htmlspecialchars($agent['uname']); ?></div>
                </div>
                <!-- FEEDBACK: List feedback for this agent -->
                <div class="card shadow mt-4 p-3">
                    <h5 class="text-secondary mb-2">Customer Feedback</h5>
                    <?php
                    $feedback_res = $conn->query("SELECT f.*, u.uname FROM feedback f LEFT JOIN user u ON f.uid=u.uid WHERE f.fdescription!='' AND f.status=1 ORDER BY f.created_at DESC LIMIT 3");
                    if ($feedback_res && $feedback_res->num_rows > 0) {
                        while ($fb = $feedback_res->fetch_assoc()) {
                            echo '<div class="mb-3"><b class="text-primary">' . htmlspecialchars($fb['uname']) . '</b> <span class="text-muted small">' . date('M d, Y', strtotime($fb['created_at'])) . '</span><div>' . htmlspecialchars($fb['fdescription']) . '</div></div>';
                        }
                    } else {
                        echo '<div class="text-muted">No feedback yet.</div>';
                    }
                    ?>
                </div>
            </div>
            <!-- AGENT BIO and PROPERTIES -->
            <div class="col-lg-8">
                <div class="card shadow p-4 mb-4">
                    <?php if (!empty($agent['bio'])): ?>
                    <h4 class="text-secondary mb-3">About <?php echo htmlspecialchars($agent['name']); ?></h4>
                    <p><?php echo nl2br(htmlspecialchars($agent['bio'])); ?></p>
                    <?php else: ?>
                        <h4 class="text-secondary mb-3">About</h4>
                        <div class="col-12 text-muted">No any detail about this agent.</div>
                    <?php endif; ?>
                </div>
                <div class="card shadow p-4">
                    <h5 class="text-secondary mb-3">Listed Properties</h5>
                    <div class="row">
                        <?php
                        $prop_stmt = $conn->prepare("SELECT * FROM property WHERE uid=? ORDER BY created_at DESC LIMIT 6");
                        $prop_stmt->bind_param("i", $agent['user_id']);
                        $prop_stmt->execute();
                        $prop_res = $prop_stmt->get_result();
                        if ($prop_res->num_rows > 0) {
                            while ($prop = $prop_res->fetch_assoc()) {
                                echo '
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card card-img h-100">
                                <a href="propertydetail.php?pid=' . $prop['pid'] . '">
                                    <img src="admin/' . htmlspecialchars($prop['pimage']) . '" class="property-thumb" alt="' . htmlspecialchars($prop['title']) . '">
                                </a>
                                <div class="card-body p-2">
                                    <h6 class="mb-1"><a href="propertydetail.php?pid=' . $prop['pid'] . '" class="text-dark">' . htmlspecialchars($prop['title']) . '</a></h6>
                                    <div class="small text-muted">' . htmlspecialchars($prop['city']) . ', ' . htmlspecialchars($prop['state']) . '</div>
                                    <div class="text-primary font-weight-bold">₹' . number_format($prop['price']) . ' ' . htmlspecialchars($prop['price_type']) . '</div>
                                </div>
                            </div>
                        </div>
                        ';
                            }
                        } else {
                            echo '<div class="col-12 text-muted">No properties listed by this agent.</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include("include/footer.php"); ?>
    <script src="package/Jquery/dist/jquery.slim.min.js"></script>
    <script src="package/Jquery/dist/jquery.min.js"></script>
    <script src="package/popper/dist/popper.min.js"></script>
    <script src="package/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="package/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

    <script src="js/owl.carousel.min.js"></script>
    <script src="js/all.min.js"></script>
    <script src="js/script.js"></script>
</body>

</html>