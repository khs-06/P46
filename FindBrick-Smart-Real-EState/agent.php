<?php
// include("config.php");
include("session-check.php");

// Pagination
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$limit = 9;
$offset = ($page - 1) * $limit;

// Get agent count for pagination
$count_result = $conn->query("SELECT COUNT(*) as total FROM agent");
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// Get agents for this page
$stmt = $conn->prepare("SELECT * FROM agent ORDER BY id DESC LIMIT ?, ?");
$stmt->bind_param("ii", $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();
$agents = [];
while ($row = $result->fetch_assoc()) $agents[] = $row;
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Agents - FindBrick</title>
    <link rel="shortcut icon" href="images/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Muli:400,400i,500,600,700&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Comfortaa:400,700" rel="stylesheet">
    <link rel="stylesheet" href="package/bootstrap/dist/css/bootstrap.min.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/fb-logo.png">

    <link rel="stylesheet" href="css/all.min.css">
    <!-- Google Fonts: Poppins & Playfair Display -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">

    <style>
        .agent-card {
            transition: box-shadow .3s;
            border-radius: 15px;
            overflow: hidden;
            background: #fff;
            border: none;
        }

        .agent-card:hover {
            box-shadow: 0 8px 24px rgba(44, 62, 80, .14);
            transform: translateY(-6px) scale(1.03);
            transition: transform .3s;
        }

        .agent-avatar {
            width: 100%;
            height: 260px;
            object-fit: cover;
            background: #f1f1f1;
        }

        .agent-social a {
            color: #6777ef;
            margin: 0 6px;
            font-size: 1.15rem;
            transition: color .2s;
        }

        .agent-social a:hover {
            color: #495057;
        }

        @media (max-width: 575px) {
            .agent-avatar {
                height: 170px;
            }
        }
    </style>
</head>

<body>
    <div id="page-wrapper">
        <?php include("include/header.php"); ?>
        <br><br><br><br>
        <div class="banner-full-row page-banner" style="background-image:url('images/breadcromb.jpg'); min-height: 250px;">
            <div class="container">
                <div class="row align-items-center py-5">
                    <div class="col-md-6">
                        <h2 class="page-name text-white text-uppercase mb-0"><b>Agents</b></h2>
                    </div>
                    <div class="col-md-6">
                        <nav aria-label="breadcrumb" class="float-md-right">
                            <ol class="breadcrumb bg-transparent m-0 p-0">
                                <li class="breadcrumb-item text-white"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active">Agents</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <div class="full-row py-5" style="background: #f7f8fa;">
            <div class="container">
                <!-- Agents -->
                <div class="row" id="agent-list">
                    <?php
                    if ($agents) {
                        foreach ($agents as $row) {
                            $img = htmlspecialchars($row['image'] ?? 'images/team/default.jpg');
                            $name = htmlspecialchars($row['name']);
                            $specialty = htmlspecialchars($row['specialty'] ?? '');
                            $city = htmlspecialchars($row['city'] ?? '');
                            $agent_id = (int)$row['id'];
                            $desc = htmlspecialchars($row['bio'] ?? '');
                            echo '
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card agent-card shadow-sm h-100">
                            <a href="agentdetail.php?id=' . $agent_id . '">
                                <img src="admin/' . $img . '" class="agent-avatar" alt="' . $name . '">
                            </a>
                            <div class="card-body text-center p-3">
                                <h5 class="mb-1"><a class="text-secondary hover-text-primary" href="agentdetail.php?id=' . $agent_id . '">' . $name . '</a></h5>
                                <div class="small text-muted mb-1">' . ($specialty ?: 'Real Estate Agent') . '</div>
                                <div class="small text-primary mb-2">' . ($city ?: '') . '</div>
                                <div class="agent-social mb-2">';
                            if (!empty($row['facebook'])) echo '<a href="' . htmlspecialchars($row['facebook']) . '" target="_blank" title="Facebook"><i class="fab fa-facebook-f"></i></a>';
                            if (!empty($row['twitter'])) echo '<a href="' . htmlspecialchars($row['twitter']) . '" target="_blank" title="Twitter"><i class="fab fa-twitter"></i></a>';
                            if (!empty($row['linkedin'])) echo '<a href="' . htmlspecialchars($row['linkedin']) . '" target="_blank" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>';
                            echo '
                                </div>
                                <div class="small text-muted">' . substr($desc, 0, 80) . (strlen($desc) > 80 ? '...' : '') . '</div>
                            </div>
                            <div class="card-footer bg-transparent text-center border-0 pb-3">
                                <a href="agentdetail.php?id=' . $agent_id . '" class="btn btn-outline-primary btn-sm px-4">View Profile</a>
                            </div>
                        </div>
                    </div>
                    ';
                        }
                    } else {
                        echo '<div class="col-12 text-center text-muted py-5">No agents found.</div>';
                    }
                    ?>
                </div>
                <!-- Pagination -->
                <div class="row">
                    <div class="col-12">
                        <nav>
                            <ul class="pagination justify-content-center mt-4" id="agent-pagination">
                                <?php
                                if ($total_pages > 1) {
                                    $get_copy = $_GET;
                                    unset($get_copy['page']);
                                    $url = '?' . http_build_query($get_copy);
                                    $url = $url == '?' ? '?' : $url . '&';
                                    echo '<li class="page-item' . ($page == 1 ? ' disabled' : '') . '"><a class="page-link" href="' . $url . 'page=' . ($page - 1) . '">Previous</a></li>';
                                    for ($i = 1; $i <= $total_pages; $i++) {
                                        if ($i == $page) {
                                            echo '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
                                        } else {
                                            echo '<li class="page-item"><a class="page-link" href="' . $url . 'page=' . $i . '">' . $i . '</a></li>';
                                        }
                                    }
                                    echo '<li class="page-item' . ($page == $total_pages ? ' disabled' : '') . '"><a class="page-link" href="' . $url . 'page=' . ($page + 1) . '">Next</a></li>';
                                }
                                ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <?php include("include/footer.php"); ?>
    </div>
    <script src="package/Jquery/dist/jquery.slim.min.js"></script>
    <script src="package/Jquery/dist/jquery.min.js"></script>
    <script src="package/popper/dist/popper.min.js"></script>
    <script src="package/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="package/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/all.min.js"></script>
    <script src="js/script.js"></script>
</body>

</html>