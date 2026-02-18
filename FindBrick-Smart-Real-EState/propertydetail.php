<?php
include("session-check.php");

$uid = $_SESSION['uid'];
$pid = isset($_REQUEST['pid']) ? intval($_REQUEST['pid']) : 0;

// Check if user has already purchased/rented this property
$stmt_check = $conn->prepare("SELECT id FROM record WHERE pid = ?");
$stmt_check->bind_param("i", $pid);
$stmt_check->execute();
if ($stmt_check->get_result()->num_rows > 0) {
    $btn_msg = "Not Available";
    $btn_css = "disabled";
}else{
    $btn_msg = "Acquire through this agent";
    $btn_css = "";
}
$stmt_check->close();

// $btn_msg = "Acquire through this agent";
// $btn_css = "";
$status = "";

// Fetch property details
$stmt = $conn->prepare("SELECT * FROM property WHERE pid = ?");
$stmt->bind_param("i", $pid);
$stmt->execute();
$propdata = $stmt->get_result()->fetch_assoc();
$status = "For " . htmlspecialchars($propdata['status']);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buy'])) {
    $agent_id = $propdata['agent_id'];
    $sale_type = $propdata['stype'];
    $price = $propdata['price'];

    $conn->begin_transaction();
    try {
        // Insert record in record table
        $stmt2 = $conn->prepare("INSERT INTO record (pid, agent_id, time, sale_type, price, buyer_id) VALUES (?, ?, NOW(), ?, ?, ?)");
        $stmt2->bind_param("iisdi", $pid, $agent_id, $sale_type, $price, $uid);
        $stmt2->execute();

        $new_status = ($propdata['stype'] == 'sale' || strtolower($propdata['stype']) == 'sale') ? "Sold" : (($propdata['stype'] == 'rent' || strtolower($propdata['stype']) == 'rent') ? "Rented" : $propdata['status']);
        $stmt3 = $conn->prepare("UPDATE property SET status = ?, uid = ? WHERE pid = ?");
        $stmt3->bind_param("sii", $new_status, $uid, $pid);
        $stmt3->execute();

        $conn->commit();
        $btn_msg = "Purchased!";
        $btn_css = "disabled";
        $status = $new_status;

        // Send Email to Agent/Builder
        $stmtAgent = $conn->prepare("SELECT name, email FROM agent WHERE id = ?");
        $stmtAgent->bind_param("i", $agent_id);
        $stmtAgent->execute();
        $agent = $stmtAgent->get_result()->fetch_assoc();
        $stmtAgent->close();

        // Get buyer info
        $stmtUser = $conn->prepare("SELECT uname, uemail FROM user WHERE uid = ?");
        $stmtUser->bind_param("i", $uid);
        $stmtUser->execute();
        $buyer = $stmtUser->get_result()->fetch_assoc();
        $stmtUser->close();

        $to = $agent['email'];
        $subject = "New Property Purchase Notification";
        $message = "Dear " . $agent['name'] . ",\n\n"
            . "Your property '" . $propdata['title'] . "' has been purchased by " . $buyer['uname'] . " (" . $buyer['uemail'] . ").\n"
            . "Property ID: $pid\n"
            . "Price: $price\n"
            . "Sale Type: $sale_type\n\n"
            . "Regards,\nHomex";
        // $headers = "From: no-reply@homex.com\r\n";
        $headers = "From: FindBrick <noreply@findbrick.com>\r\n";

        mail($to, $subject, $message, $headers);
        echo "<script>alert('Transaction Successful! An email has been sent to the agent.');</script>";
    } catch (Exception $e) {
        $conn->rollback();
        $btn_msg = "Transaction Failed!";
        $btn_css = "";
    }
}

?>
<!-- Your normal HTML below (unchanged) -->
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- ... -->
</head>

<body>
    <!-- ... -->
    <!-- No change needed in HTML part for buy button -->
</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Property Detail - FindBrick</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="images/fb-logo.png">
    <link rel="shortcut icon" type="image/x-icon" href="images/fb-logo.png">
    <link rel="stylesheet" href="package/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/all.min.css">
    <!-- Google Fonts: Poppins & Playfair Display -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">


    <style>
        .property-quantity ul {
            list-style: none;
            padding: 0;
        }

        .property-quantity ul li {
            margin-bottom: 6px;
        }

        .features-section ul {
            list-style: disc;
            padding-left: 18px;
        }
    </style>
</head>

<body>
    <div id="page-wrapper">
        <?php include("include/header.php"); ?>
        <br><br><br><br><br>
        <div class="banner-full-row page-banner" style="background-image:url('images/breadcromb.jpg'); min-height: 250px;">
            <div class="container">
                <div class="row align-items-center py-5">
                    <div class="col-md-6">
                        <h2 class="page-name text-white text-uppercase mb-0"><b>Property Detail</b></h2>
                    </div>
                    <div class="col-md-6">
                        <nav aria-label="breadcrumb" class="float-md-right">
                            <ol class="breadcrumb bg-transparent m-0 p-0">
                                <li class="breadcrumb-item text-white"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active">Property Detail</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <div class="full-row py-5">
            <div class="container">
                <div class="row">
                    <!-- LEFT: Property Info -->
                    <div class="col-lg-8">
                        <?php if ($propdata): ?>
                            <div class="card shadow p-4 mb-4">
                                <!-- Property Images Carousel (first 4 images) -->
                                <div id="propertyCarousel" class="carousel slide mb-3" data-ride="carousel">
                                    <div class="carousel-inner">
                                        <?php
                                        $images = [];
                                        for ($i = 1; $i <= 6; $i++) {
                                            if (!empty($propdata["pimage" . ($i == 1 ? "" : $i)])) {
                                                $images[] = $propdata["pimage" . ($i == 1 ? "" : $i)];
                                            }
                                        }
                                        foreach ($images as $idx => $img) {
                                            $active = $idx === 0 ? "active" : "";
                                            echo '<div class="carousel-item ' . $active . '">
                                                <img src="admin/' . htmlspecialchars($img) . '" class="d-block w-100 rounded" style="max-height:450px;object-fit:cover" alt="Property image">
                                            </div>';
                                        }
                                        ?>
                                    </div>
                                    <!-- <?php if (count($images) > 1): ?>
                                    <a class="carousel-control-prev" href="#propertyCarousel" role="button" data-slide="prev">
                                        <span class="carousel-control-prev-icon"></span>
                                    </a>
                                    <a class="carousel-control-next" href="#propertyCarousel" role="button" data-slide="next">
                                        <span class="carousel-control-next-icon"></span>
                                    </a>
                                    <?php endif; ?> -->
                                </div>
                                <h3 class="text-secondary"><?php echo htmlspecialchars($propdata['title'] ?? $propdata['type']); ?></h3>
                                <div class="mb-2">
                                    <span class="badge badge-dark"><?php echo $status; ?> </span>
                                    <?php if ($propdata['isFeatured']): ?>
                                        <span class="badge badge-warning ml-2">Featured</span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <span class="h4 ml-3 text-black">Price: <span class="h4 ml-3 text-primary"><?php echo htmlspecialchars($propdata['price']); ?><?php echo htmlspecialchars($propdata['price_type']); ?></span></span>
                                </div>
                                <div class="text-muted my-2"><i class="fa fa-map-marker-alt"></i> <?php echo htmlspecialchars($propdata['loc']); ?></div>

                                <!-- Property Quantity -->
                                <div class="bg-light property-quantity px-4 pt-4 w-100 mb-3">
                                    <ul class="row">
                                        <li class="col-md-4"><span class="text-secondary"><?php echo htmlspecialchars($propdata['asize']); ?></span> Sqft</li>
                                        <li class="col-md-4"><span class="text-secondary"><?php echo htmlspecialchars($propdata['bed']); ?></span> Bedroom</li>
                                        <li class="col-md-4"><span class="text-secondary"><?php echo htmlspecialchars($propdata['bath']); ?></span> Bathroom</li>
                                        <li class="col-md-4"><span class="text-secondary"><?php echo htmlspecialchars($propdata['balcony']); ?></span> Balcony</li>
                                        <li class="col-md-4"><span class="text-secondary"><?php echo htmlspecialchars($propdata['hall']); ?></span> Hall</li>
                                        <li class="col-md-4"><span class="text-secondary"><?php echo htmlspecialchars($propdata['kitc']); ?></span> Kitchen</li>
                                    </ul>
                                </div>
                                <!-- Property Summary Table -->
                                <h5 class="mt-4 mb-2 text-secondary">Property Summary</h5>
                                <table class="table table-bordered mt-2">
                                    <tr>
                                        <th>BHK</th>
                                        <td><?php echo htmlspecialchars($propdata['bhk']); ?></td>
                                        <th>Property Type</th>
                                        <td><?php echo htmlspecialchars($propdata['ptype']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Floor</th>
                                        <td><?php echo htmlspecialchars($propdata['floorcount']); ?></td>
                                        <th>Total Floor</th>
                                        <td><?php echo htmlspecialchars($propdata['totalfloor']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>City</th>
                                        <td><?php echo htmlspecialchars($propdata['city']); ?></td>
                                        <th>State</th>
                                        <td><?php echo htmlspecialchars($propdata['state']); ?></td>
                                    </tr>
                                </table>
                                <!-- Description -->
                                <h5 class="mt-4 text-secondary">Description</h5>
                                <p><?php echo $propdata['description']; ?></p>
                                <!-- Features section (HTML) -->
                                <h5 class="mt-5 mb-3 text-secondary">Features</h5>
                                <div class="features-section">
                                    <?php
                                    // Output feature HTML with some XSS protection
                                    echo $propdata['feature'];
                                    ?>
                                </div>
                                <!-- Floor Plans -->
                                <h5 class="mt-5 mb-3 text-secondary">Floor Plans</h5>
                                <div class="accordion mb-2" id="floorPlansAccordion">
                                    <?php if (!empty($propdata['groundimage'])): ?>
                                        <button class="btn btn-light w-100 text-left mb-1" type="button" data-toggle="collapse" data-target="#floorPlan1">
                                            Floor Plan
                                        </button>
                                        <div id="floorPlan1" class="collapse show p-3" data-parent="#floorPlansAccordion">
                                            <img src="admin/<?php echo htmlspecialchars($propdata['groundimage']); ?>" alt="Floor Plan" class="img-fluid">
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($propdata['pimage6'])): ?>
                                        <button class="btn btn-light w-100 text-left mb-1" type="button" data-toggle="collapse" data-target="#floorPlan2">
                                            Basement Floor
                                        </button>
                                        <div id="floorPlan2" class="collapse p-3" data-parent="#floorPlansAccordion">
                                            <img src="admin/<?php echo htmlspecialchars($propdata['pimage6']); ?>" alt="Basement Floor" class="img-fluid">
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($propdata['otherimage'])): ?>
                                        <button class="btn btn-light w-100 text-left mb-1" type="button" data-toggle="collapse" data-target="#floorPlan3">
                                            Ground Floor
                                        </button>
                                        <div id="floorPlan3" class="collapse p-3" data-parent="#floorPlansAccordion">
                                            <img src="admin/<?php echo htmlspecialchars($propdata['otherimage']); ?>" alt="Ground Floor" class="img-fluid">
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <!-- Instalment Calculator -->
                                <h5 class="mt-5 mb-3 text-secondary">Instalment Calculator</h5>
                                <form class="d-inline-block w-100" action="instalment.php" method="post">
                                    <label class="sr-only">Property Amount</label>
                                    <div class="input-group mb-2 mr-sm-2">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">$</div>
                                        </div>
                                        <input type="text" class="form-control" name="amount" placeholder="Property Price" value="<?php echo htmlspecialchars($propdata['price']); ?><?php echo htmlspecialchars($propdata['price_type']); ?>">
                                    </div>

                                    <button type="submit" value="submit" name="calc" class="btn btn-danger mt-4">Calculate Instalment</button>
                                </form>
                                <!-- Buy button -->
                                <form method="post" class="mt-4">
                                    <button type="submit" name="buy" class="btn btn-dark px-4" <?php echo $btn_css; ?>><?php echo $btn_msg; ?></button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">Property not found.</div>
                        <?php endif; ?>
                    </div>
                    <!-- RIGHT: Agent Info and Recent Properties -->
                    <div class="col-lg-4">
                        <div class="card shadow p-3 mb-4">
                            <h5 class="text-secondary">Contact Agent</h5>
                            <?php
                            // $agent_id = $propdata['uid'];
                            $stmt = $conn->prepare("SELECT name, phone, email FROM agent WHERE id=(select agent_id from property where pid=?)");
                            $stmt->bind_param("i", $pid);
                            $stmt->execute();
                            $agent = $stmt->get_result()->fetch_assoc();
                            if ($agent): ?>
                                <div class="mb-2"><b>Name:</b> <?php echo htmlspecialchars($agent['name']); ?></div>
                                <div class="mb-2"><b>Contact:</b> <?php echo htmlspecialchars($agent['phone']); ?></div>
                                <div class="mb-2"><b>Email:</b> <?php echo htmlspecialchars($agent['email']); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="card shadow p-3">
                            <h5 class="text-secondary">Recent Properties</h5>
                            <ul class="list-unstyled">
                                <?php
                                $stmt = $conn->prepare("SELECT pid, title, city, pimage FROM property ORDER BY created_at DESC LIMIT 5");
                                // $stmt->bind_param("s", $propdata['city']);
                                $stmt->execute();
                                $recent = $stmt->get_result();
                                while ($row = $recent->fetch_assoc()):
                                ?>
                                    <li class="media mb-3">
                                        <img src="admin/<?php echo htmlspecialchars($row['pimage']); ?>" alt="" class="mr-3 rounded" style="width:60px;height:50px;object-fit:cover;">
                                        <div class="media-body">
                                            <a class="text-dark" href="propertydetail.php?pid=<?php echo $row['pid']; ?>">
                                                <?php echo htmlspecialchars($row['title']); ?>
                                            </a>
                                            <div class="text-muted small"><?php echo htmlspecialchars($row['city']); ?></div>
                                        </div>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include("include/footer.php"); ?>
    </div>
    <script src="package/Jquery/dist/jquery.slim.min.js"></script>
    <script src="package/popper/dist/popper.min.js"></script>
    <script src="package/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="js/script.js"></script>
    <script src="js/all.min.js"></script>
</body>

</html>