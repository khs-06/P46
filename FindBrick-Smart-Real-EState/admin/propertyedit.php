<?php
session_start();
require("config.php");

if (!isset($_SESSION['auser'])) {
    header("location:dashboard.php");
    exit();
}

$error = "";
$msg = "";

// Get property ID from request
$pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;

// Fetch property details
$property = [];
if ($pid) {
    $sql = "SELECT * FROM property WHERE pid=?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $pid);
    $stmt->execute();
    $result = $stmt->get_result();
    $property = $result->fetch_assoc();
    $stmt->close();
    if (!$property) {
        $error = "<div class='alert alert-danger'>Property not found.</div>";
    }
} else {
    $error = "<div class='alert alert-danger'>Invalid Property ID.</div>";
}

// Handle update
if (isset($_POST['update']) && $property) {
    // --- Sanitize/Validate ---
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
    $uid          = $property['uid'];
    $agent_id     = $property['agent_id'];

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

    $uploadDir = "uploads/properties/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $allowedTypes = ['jpg', 'jpeg', 'png'];
    $maxSize = 2 * 1024 * 1024;

    $aimage   = processUpload($_FILES['aimage'], $uploadDir, $allowedTypes, $maxSize, $error, $property['pimage']);
    $aimage1  = processUpload($_FILES['aimage1'], $uploadDir, $allowedTypes, $maxSize, $error, $property['pimage1']);
    $aimage2  = processUpload($_FILES['aimage2'], $uploadDir, $allowedTypes, $maxSize, $error, $property['pimage2']);
    $aimage3  = processUpload($_FILES['aimage3'], $uploadDir, $allowedTypes, $maxSize, $error, $property['pimage3']);
    $aimage4  = processUpload($_FILES['aimage4'], $uploadDir, $allowedTypes, $maxSize, $error, $property['pimage4']);
    $aimage5  = processUpload($_FILES['aimage5'], $uploadDir, $allowedTypes, $maxSize, $error, $property['pimage5']);
    $aimage6  = processUpload($_FILES['aimage6'], $uploadDir, $allowedTypes, $maxSize, $error, $property['pimage6']);
    $groundimage  = processUpload($_FILES['groundimage'], $uploadDir, $allowedTypes, $maxSize, $error, $property['groundimage']);
    $otherimage   = processUpload($_FILES['Otherimage'], $uploadDir, $allowedTypes, $maxSize, $error, $property['otherimage']);

    // Update property only if no error
    if (empty($error)) {
        $sql = "UPDATE property SET
            title=?, ptype=?, stype=?, price_type=?, price=?, bed=?, bath=?, kitc=?, bhk=?, balcony=?, hall=?, totalfloor=?,
            floorcount=?, loc=?, city=?, state=?, asize=?, feature=?, pimage=?, pimage1=?, pimage2=?, pimage3=?, pimage4=?, pimage5=?, pimage6=?, groundimage=?, otherimage=?,
            status=?, uid=?, agent_id=?, isFeatured=?, description=?
            WHERE pid=?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param(
            "sssssiiisiiisssssssssssssssiiiisi",
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
            $description,
            $pid
        );
        if ($stmt->execute()) {
            $msg = "<div class='alert alert-success'>Property updated successfully!</div>";
            header("Location: propertyview.php?msg=" . urlencode($msg));
            exit();
        } else {
            $error .= "<div class='alert alert-danger'>Database error: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Property - FB Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
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

        .img-preview {
            height: 80px;
            margin: 3px 0;
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
                    <h3 class="mb-0">Edit Property</h3>
                </div>
                <div class="card card-custom p-4 mb-5">
                    <h4 class="mb-3">Update Property</h4>
                    <?= $error ?>
                    <?= $msg ?>
                    <?php if ($property): ?>
                        <form method="post" enctype="multipart/form-data" autocomplete="off">
                            <!-- Basic Information Section -->
                            <div class="form-section">
                                <h5>📝 Basic Information</h5>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Property Title *</label>
                                        <input type="text" name="title" class="form-control" required maxlength="50"
                                            value="<?= htmlspecialchars($property['title']) ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Property Type *</label>
                                        <select name="ptype" class="form-control" required>
                                            <option value="">Select Type</option>
                                            <?php foreach (['apartment', 'flat', 'building', 'house', 'villa', 'office'] as $opt): ?>
                                                <option value="<?= $opt ?>" <?= ($property['ptype'] == $opt ? 'selected' : '') ?>><?= ucfirst($opt) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Selling Type *</label>
                                        <select name="stype" class="form-control" required>
                                            <option value="">Select Status</option>
                                            <option value="sale" <?= $property['stype'] == 'sale' ? 'selected' : '' ?>>Sale</option>
                                            <option value="rent" <?= $property['stype'] == 'rent' ? 'selected' : '' ?>>Rent</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label>Price Type *</label>
                                        <select name="price_type" class="form-control" required>
                                            <option value="">Select Price Type</option>
                                            <option value="cr" <?= $property['price_type'] == 'cr' ? 'selected' : '' ?>>Cr</option>
                                            <option value="lakh" <?= $property['price_type'] == 'lakh' ? 'selected' : '' ?>>Lakh</option>
                                            <option value="k" <?= $property['price_type'] == 'k' ? 'selected' : '' ?>>K</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Price *</label>
                                        <input type="text" name="price" id="price" class="form-control" required
                                            value="<?= htmlspecialchars($property['price']) ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>BHK *</label>
                                        <select name="BHKtype" class="form-control" required>
                                            <?php foreach (['1 BHK', '2 BHK', '3 BHK', '4 BHK', '5 BHK', '1,2 BHK', '2,3 BHK', '2,3,4 BHK'] as $opt): ?>
                                                <option value="<?= $opt ?>" <?= ($property['bhk'] == $opt ? 'selected' : '') ?>><?= $opt ?></option>
                                            <?php endforeach; ?>
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
                                        <input type="number" name="bed" class="form-control" min="0" max="10" required value="<?= $property['bed'] ?>">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Bathrooms *</label>
                                        <input type="number" name="bath" class="form-control" min="0" max="10" required value="<?= $property['bath'] ?>">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Kitchen *</label>
                                        <input type="number" name="kitc" class="form-control" min="0" max="10" required value="<?= $property['kitc'] ?>">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Balcony *</label>
                                        <input type="number" name="balcony" class="form-control" min="0" max="10" required value="<?= $property['balcony'] ?>">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Hall *</label>
                                        <input type="number" name="hall" class="form-control" min="0" max="10" required value="<?= $property['hall'] ?>">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Total Floor *</label>
                                        <input type="number" name="totalfloor" class="form-control" min="0" max="10" required value="<?= $property['totalfloor'] ?>">
                                    </div>
                                </div>
                            </div>
                            <!-- Location Section -->
                            <div class="form-section">
                                <h5>📍 Location</h5>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Address *</label>
                                        <input type="text" name="loc" class="form-control" required maxlength="100" value="<?= htmlspecialchars($property['loc']) ?>">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Floor *</label>
                                        <select name="floorcount" class="form-control" required>
                                            <?php foreach (['1 floor', '2 floor', '3 floor', '4 floor', '5 floor'] as $opt): ?>
                                                <option value="<?= $opt ?>" <?= ($property['floorcount'] == $opt ? 'selected' : '') ?>><?= ucfirst($opt) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>City *</label>
                                        <input type="text" name="city" class="form-control" required maxlength="50" value="<?= htmlspecialchars($property['city']) ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>State *</label>
                                        <input type="text" name="state" class="form-control" required maxlength="50" value="<?= htmlspecialchars($property['state']) ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Area Size (sqft) *</label>
                                        <input type="text" name="asize" class="form-control" required maxlength="20" value="<?= htmlspecialchars($property['asize']) ?>">
                                    </div>
                                </div>
                            </div>
                            <!-- Features Section -->
                            <div class="form-section">
                                <h5>✨ Features</h5>
                                <p class="alert alert-danger">
                                    * Important: Do Not Remove Below Content. Only Change <b>Yes</b> Or <b>No</b> or Details and Do Not Add More Details
                                </p>
                                <textarea name="feature" class="tinymce form-control" rows="6"><?= htmlspecialchars($property['feature']) ?></textarea>
                            </div>
                            <!-- Images & Status Section -->
                            <div class="form-section">
                                <h5>🖼 Images & Status</h5>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Main Image *</label>
                                        <input type="file" name="aimage" class="form-control" accept=".jpg,.jpeg,.png">
                                        <?php if ($property['pimage']): ?>
                                            <img src="<?= $property['pimage'] ?>" class="img-preview">
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Image 1 *</label>
                                        <input type="file" name="aimage1" class="form-control" accept=".jpg,.jpeg,.png">
                                        <?php if ($property['pimage1']): ?>
                                            <img src="<?= $property['pimage1'] ?>" class="img-preview">
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Image 2 *</label>
                                        <input type="file" name="aimage2" class="form-control" accept=".jpg,.jpeg,.png">
                                        <?php if ($property['pimage2']): ?>
                                            <img src="<?= $property['pimage2'] ?>" class="img-preview">
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Image 3 *</label>
                                        <input type="file" name="aimage3" class="form-control" accept=".jpg,.jpeg,.png">
                                        <?php if ($property['pimage3']): ?>
                                            <img src="<?= $property['pimage3'] ?>" class="img-preview">
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Image 4 *</label>
                                        <input type="file" name="aimage4" class="form-control" accept=".jpg,.jpeg,.png">
                                        <?php if ($property['pimage4']): ?>
                                            <img src="<?= $property['pimage4'] ?>" class="img-preview">
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Image 5</label>
                                        <input type="file" name="aimage5" class="form-control" accept=".jpg,.jpeg,.png">
                                        <?php if ($property['pimage5']): ?>
                                            <img src="<?= $property['pimage5'] ?>" class="img-preview">
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Basement Floor Plan Image</label>
                                        <input type="file" name="aimage6" class="form-control" accept=".jpg,.jpeg,.png">
                                        <?php if ($property['pimage6']): ?>
                                            <img src="<?= $property['pimage6'] ?>" class="img-preview">
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Floor Plan Image</label>
                                        <input type="file" name="groundimage" class="form-control" accept=".jpg,.jpeg,.png">
                                        <?php if ($property['groundimage']): ?>
                                            <img src="<?= $property['groundimage'] ?>" class="img-preview">
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Ground Floor Plan Image</label>
                                        <input type="file" name="Otherimage" class="form-control" accept=".jpg,.jpeg,.png">
                                        <?php if ($property['otherimage']): ?>
                                            <img src="<?= $property['otherimage'] ?>" class="img-preview">
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Status *</label>
                                        <select name="status" class="form-control" required>
                                            <option value="available" <?= $property['status'] == 'available' ? 'selected' : '' ?>>Available</option>
                                            <option value="sold out" <?= $property['status'] == 'sold out' ? 'selected' : '' ?>>Sold Out</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Is Featured? *</label>
                                        <select name="isFeatured" class="form-control" required>
                                            <option value="0" <?= $property['isFeatured'] == 0 ? 'selected' : '' ?>>No</option>
                                            <option value="1" <?= $property['isFeatured'] == 1 ? 'selected' : '' ?>>Yes</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- Description Section -->
                            <div class="form-section">
                                <h5>📝 Description</h5>
                                <div class="form-group">
                                    <textarea name="description" class="tinymce form-control" rows="5"><?= htmlspecialchars($property['description']) ?></textarea>
                                </div>
                            </div>
                            <!-- Submit Button -->
                            <div class="col-12 text-center mt-3">
                                <button type="submit" name="update" class="btn btn-primary px-4">
                                    Update Property
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../package/tinymce/tinymce.min.js"></script>
    <script src="../package/tinymce/init-tinymce.min.js"></script>
</body>

</html>