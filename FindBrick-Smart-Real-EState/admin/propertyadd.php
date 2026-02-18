<?php
session_start();
require("config.php");

if (!isset($_SESSION['auser'])) {
    header("location:dashboard.php");
    exit();
}

$error = "";
$msg = "";
?>
<?php
if (isset($_POST['add'])) {
    // --- Sanitize and Validate ---
    $title        = trim($_POST['title']);
    $ptype        = $_POST['ptype'];
    $stype        = $_POST['stype'];
    $price_type   = $_POST['price_type'];
    $price        = $_POST['price'];
    $bed          = (int)$_POST['bed'];
    $bath         = (int)$_POST['bath'];
    $kitc         = (int)$_POST['kitc'];
    $BHKtype      = $_POST['BHKtype'];
    $balcony      = (int)$_POST['balcony'];
    $hall         = (int)$_POST['hall'];
    $totalfloor   = (int)$_POST['totalfloor'];
    $floorcount   = $_POST['floorcount'];
    $loc          = trim($_POST['loc']);
    $city         = trim($_POST['city']);
    $state        = trim($_POST['state']);
    $asize        = trim($_POST['asize']);
    $feature      = trim($_POST['feature']);
    $isFeatured   = isset($_POST['isFeatured']) ? intval($_POST['isFeatured']) : 0;
    $status       = isset($_POST['status']) ? $_POST['status'] : 'available';
    $description  = isset($_POST['description']) ? trim($_POST['description']) : '';
    $uid          = $_SESSION['uid'];

    // Get agent id from session or other logic
    $agent_id     = $stmt = $con->prepare("SELECT id FROM agent WHERE email=?");
    $stmt->bind_param("s", $_SESSION['auser']);
    $stmt->execute();
    $result = $stmt->get_result();
    $agent_id = $result->fetch_assoc()['id'] ?? 0;
    $stmt->close();

    // Validation
    if (strlen($title) < 5) $error .= "<p>Title must be at least 5 characters.</p>";
    if (!is_numeric($price) || $price <= 0) $error .= "<p>Price must be a positive number.</p>";
    foreach (['bed', 'bath', 'kitc', 'balcony', 'hall', 'totalfloor'] as $numf) {
        if ($$numf < 0 || $$numf > 10) $error .= "<p>" . ucfirst($numf) . " must be between 0 and 10.</p>";
    }
    if (empty($ptype) || empty($stype) || empty($BHKtype) || empty($floorcount) || empty($city) || empty($state) || empty($loc) || empty($asize)) {
        $error .= "<p>All required fields must be filled.</p>";
    }

    // Handle image uploads
    function processUpload($file, $uploadDir, $allowedTypes, $maxSize, &$error, $oldPath = "")
    {
        if ($file['error'] == 0 && !empty($file['name'])) {
            $fileName = basename($file['name']);
            $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $targetPath = $uploadDir . time() . '_' . uniqid() . '.' . $fileType;
            if (!in_array($fileType, $allowedTypes)) {
                $error .= "<p>{$fileName}: Invalid file type.</p>";
                return $oldPath;
            }
            if ($file['size'] > $maxSize) {
                $error .= "<p>{$fileName}: Exceeds max size of 2MB.</p>";
                return $oldPath;
            }
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                return $targetPath;
            } else {
                $error .= "<p>{$fileName}: Upload failed.</p>";
                return $oldPath;
            }
        }
        return $oldPath;
    }

    // File upload rules
    $uploadDir = "admin/uploads/properties/";
    $image_path = 'uploads/properties/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $allowedTypes = ['jpg', 'jpeg', 'png'];
    $maxSize = 2 * 1024 * 1024;

    // Required Images
    $aimage   = processUpload($_FILES['aimage'], $image_path, $allowedTypes, $maxSize, $error);
    $aimage1  = processUpload($_FILES['aimage1'], $image_path, $allowedTypes, $maxSize, $error);
    $aimage2  = processUpload($_FILES['aimage2'], $image_path, $allowedTypes, $maxSize, $error);
    $aimage3  = processUpload($_FILES['aimage3'], $image_path, $allowedTypes, $maxSize, $error);
    $aimage4  = processUpload($_FILES['aimage4'], $image_path, $allowedTypes, $maxSize, $error);

    // Optional Images
    $aimage5      = isset($_FILES['aimage5'])      ? processUpload($_FILES['aimage5'], $image_path, $allowedTypes, $maxSize, $error) : null;
    $aimage6      = isset($_FILES['aimage6'])      ? processUpload($_FILES['aimage6'], $image_path, $allowedTypes, $maxSize, $error) : null;
    $groundimage  = isset($_FILES['groundimage'])  ? processUpload($_FILES['groundimage'], $image_path, $allowedTypes, $maxSize, $error) : null;
    $otherimage   = isset($_FILES['Otherimage'])   ? processUpload($_FILES['Otherimage'], $image_path, $allowedTypes, $maxSize, $error) : null;

    // Only insert if no critical errors
    if (empty($error) && $aimage && $aimage2 && $aimage3 && $aimage4) {
        $sql = "INSERT INTO property (
            title, ptype, stype, price_type, price, bed, bath, kitc, bhk, balcony, hall, totalfloor,
            floorcount, loc, city, state, asize, feature, pimage, pimage1, pimage2, pimage3, pimage4,
            pimage5, pimage6, groundimage, otherimage, status, uid, agent_id, isFeatured, description
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $con->prepare($sql);
        $stmt->bind_param(
            "sssssiiisiiissssssssssssssiiiis",
            $title,
            $ptype,
            $stype,
            $price_type,
            $price,
            $bed,
            $bath,
            $kitc,
            $BHKtype,
            $balcony,
            $hall,
            $totalfloor,
            $floorcount,
            $loc,
            $city,
            $state,
            $asize,
            $feature,
            $aimage,
            $aimage1,
            $aimage2,
            $aimage3,
            $aimage4,
            $aimage5,
            $aimage6,
            $groundimage,
            $otherimage,
            $status,
            $uid,
            $agent_id,
            $isFeatured,
            $description
        );
        if ($stmt->execute()) {
            $property_id = $con->insert_id;
            $msg = "<p class='alert alert-success'>Property Added Successfully!</p>";
        } else {
            $error .= "<p class='alert alert-danger'>Database error: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        if (empty($error)) $error .= "<p class='alert alert-danger'>Required images are missing or invalid format.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Property - FB Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="shortcut icon" type="image/x-icon" href="../images/fb-logo.png">

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f5f7fa;
        }

        .sidebar {
            height: 100vh;
            background: #fff;
            border-right: 1px solid #ddd;
            padding-top: 1rem;
        }

        .sidebar h4 {
            font-weight: bold;
            color: #0d6efd;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            margin: 6px 10px;
            color: #333;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 500;
            transition: 0.3s;
        }

        .sidebar a i {
            margin-right: 10px;
            font-size: 18px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #0d6efd;
            color: #fff;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
        }

        .topbar {
            background: #fff;
            padding: 12px 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .topbar input {
            border-radius: 20px;
            padding: 5px 15px;
            border: 1px solid #ccc;
        }

        .card-custom {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .form-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 25px;
        }

        .form-section h5 {
            color: #2c3e50;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2">
                <?php include("sidebar.php"); ?>
            </div>
            <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4">
                <div class="topbar mt-2 mb-4 rounded">
                    <h3 class="mb-0">Add Property</h3>
                    <div class="d-flex align-items-center gap-3">
                        <input type="text" placeholder="Search...">
                        <i class="bi bi-bell fs-4 text-secondary"></i>
                        <a href="profile.php">
                            <img src="assets/img/profiles/avatar-01.png" class="rounded-circle" width="40" height="40" alt="Profile">
                        </a>
                    </div>
                </div>
                <div class="card card-custom p-4 mb-5">
                    <h4 class="mb-3">Property Details</h4>
                    <?= $error ?>
                    <?= $msg ?>
                    <form method="post" enctype="multipart/form-data" autocomplete="off">
                        <!-- Basic Information Section -->
                        <div class="form-section">
                            <h5>📝 Basic Information</h5>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Property Title *</label>
                                    <input type="text" name="title" class="form-control" required maxlength="50">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Property Type *</label>
                                    <select name="ptype" class="form-control" required>
                                        <option value="">Select Type</option>
                                        <option value="apartment">Apartment</option>
                                        <option value="flat">Flat</option>
                                        <option value="building">Building</option>
                                        <option value="house">House</option>
                                        <option value="villa">Villa</option>
                                        <option value="office">Office</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Selling Type *</label>
                                    <select name="stype" class="form-control" required>
                                        <option value="">Select Status</option>
                                        <option value="sale">Sale</option>
                                        <option value="rent">Rent</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>Price Type *</label>
                                    <select name="price_type" class="form-control" required>
                                        <option value="">Select Price Type</option>
                                        <option value="cr">Cr</option>
                                        <option value="lakh">Lakh</option>
                                        <option value="k">K</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Price *</label>
                                    <input type="text" name="price" id="price" class="form-control" required placeholder="Enter Price">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>BHK *</label>
                                    <select name="BHKtype" class="form-control" required>
                                        <option value="">Select BHK</option>
                                        <option value="1 BHK">1 BHK</option>
                                        <option value="2 BHK">2 BHK</option>
                                        <option value="3 BHK">3 BHK</option>
                                        <option value="4 BHK">4 BHK</option>
                                        <option value="5 BHK">5 BHK</option>
                                        <option value="1,2 BHK">1,2 BHK</option>
                                        <option value="2,3 BHK">2,3 BHK</option>
                                        <option value="2,3,4 BHK">2,3,4 BHK</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- Property Details Section -->
                        <div class="form-section">
                            <h5>🏠 Property Details</h5>
                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <label>Bedrooms *</label>
                                    <input type="number" name="bed" class="form-control" min="0" max="10" required placeholder="1-10">
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Bathrooms *</label>
                                    <input type="number" name="bath" class="form-control" min="0" max="10" required placeholder="1-10">
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Kitchen *</label>
                                    <input type="number" name="kitc" class="form-control" min="0" max="10" required placeholder="1-10">
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Balcony *</label>
                                    <input type="number" name="balcony" class="form-control" min="0" max="10" required placeholder="1-10">
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Hall *</label>
                                    <input type="number" name="hall" class="form-control" min="0" max="10" required placeholder="1-10">
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Total Floor *</label>
                                    <input type="number" name="totalfloor" class="form-control" min="0" max="10" required placeholder="1-10">
                                </div>
                            </div>
                        </div>
                        <!-- Location Section -->
                        <div class="form-section">
                            <h5>📍 Location</h5>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Address *</label>
                                    <input type="text" name="loc" class="form-control" required maxlength="100">
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Floor *</label>
                                    <select name="floorcount" class="form-control" required>
                                        <option value="">Select Floor</option>
                                        <option value="1 floor">1st Floor</option>
                                        <option value="2 floor">2nd Floor</option>
                                        <option value="3 floor">3rd Floor</option>
                                        <option value="4 floor">4th Floor</option>
                                        <option value="5 floor">5th Floor</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>City *</label>
                                    <input type="text" name="city" class="form-control" required maxlength="50">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>State *</label>
                                    <input type="text" name="state" class="form-control" required maxlength="50">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Area Size (sqft) *</label>
                                    <input type="text" name="asize" class="form-control" required maxlength="20">
                                </div>
                            </div>
                        </div>
                        <!-- Features Section -->
                        <div class="form-section">
                            <h5>✨ Features</h5>
                            <p class="alert alert-danger">
                                * Important: Do Not Remove Below Content. Only Change <b>Yes</b> Or <b>No</b> or Details and Do Not Add More Details
                            </p>
                            <textarea name="feature" class="tinymce form-control" rows="6">
<!---feature area start--->
<ul>
  <li>Property Age : 10 Years</li>
  <li>Swiming Pool : Yes</li>
  <li>Parking : Yes</li>
  <li>GYM : Yes</li>
</ul>
<ul>
  <li>Type : Apartment</li>
  <li>Security : Yes</li>
  <li>Dining Capacity : 10 People</li>
  <li>Church/Temple : No</li>
</ul>
<ul>
  <li>3rd Party : No</li>
  <li>Elevator : Yes</li>
  <li>CCTV : Yes</li>
  <li>Water Supply : Ground Water / Tank</li>
</ul>
<!---feature area end--->
                            </textarea>
                        </div>
                        <!-- Images & Status Section -->
                        <div class="form-section">
                            <h5>🖼 Images & Status</h5>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Main Image *</label>
                                    <input type="file" name="aimage" class="form-control" accept=".jpg,.jpeg,.png" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Image 1 *</label>
                                    <input type="file" name="aimage1" class="form-control" accept=".jpg,.jpeg,.png" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Image 2 *</label>
                                    <input type="file" name="aimage2" class="form-control" accept=".jpg,.jpeg,.png" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Image 3 *</label>
                                    <input type="file" name="aimage3" class="form-control" accept=".jpg,.jpeg,.png" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Image 4 *</label>
                                    <input type="file" name="aimage4" class="form-control" accept=".jpg,.jpeg,.png" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Image 5</label>
                                    <input type="file" name="aimage5" class="form-control" accept=".jpg,.jpeg,.png">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Basement Floor Plan Image</label>
                                    <input type="file" name="aimage6" class="form-control" accept=".jpg,.jpeg,.png">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Floor Plan Image</label>
                                    <input type="file" name="groundimage" class="form-control" accept=".jpg,.jpeg,.png">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Ground Floor Plan Image</label>
                                    <input type="file" name="Otherimage" class="form-control" accept=".jpg,.jpeg,.png">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Status *</label>
                                    <select name="status" class="form-control" required>
                                        <option value="">Select Status</option>
                                        <option value="available">Available</option>
                                        <option value="sold out">Sold Out</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Is Featured? *</label>
                                    <select name="isFeatured" class="form-control" required>
                                        <option value="">Select...</option>
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- Description Section -->
                        <div class="form-section">
                            <h5>📝 Description</h5>
                            <div class="form-group">
                                <textarea name="description" class="tinymce form-control" rows="5" placeholder="Enter property description..."></textarea>
                            </div>
                        </div>
                        <!-- Submit Button -->
                        <div class="col-12 text-center mt-3">
                            <button type="submit" name="add" class="btn btn-primary px-4">
                                Submit Property
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
    <!-- Bootstrap & jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../package/tinymce/tinymce.min.js"></script>
    <script src="../package/tinymce/init-tinymce.min.js"></script>
</body>

</html>