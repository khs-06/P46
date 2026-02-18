<?php
include("session-check.php");
// include("config.php");

$error = "";
$msg = "";

if (!isset($_SESSION['uemail'])) {
    header("location:login.php");
    exit();
}

$pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;

// Helper: Optimize image upload (reuse from submitproperty.php)
function optimizeImage($src, $dest, $max_width = 1200, $quality = 80)
{
    $info = getimagesize($src);
    if (!$info)
        return false;
    $mime = $info['mime'];
    if ($mime == 'image/jpeg' || $mime == 'image/jpg') {
        $image = imagecreatefromjpeg($src);
    } elseif ($mime == 'image/png') {
        $image = imagecreatefrompng($src);
    } else {
        return false;
    }
    $width = imagesx($image);
    $height = imagesy($image);
    if ($width > $max_width) {
        $new_width = $max_width;
        $new_height = floor($height * ($max_width / $width));
        $tmp_img = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($tmp_img, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        $image = $tmp_img;
    }
    imagejpeg($image, $dest, $quality);
    imagedestroy($image);
    return true;
}

function processUpload($file, $uploadDir, $allowedTypes, $maxSize, &$error, $optimize = true, $oldPath = "")
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
            if ($optimize && in_array($fileType, ['jpg', 'jpeg', 'png'])) {
                optimizeImage($targetPath, $targetPath);
            }
            return $targetPath;
        } else {
            $error .= "<p>{$fileName}: Upload failed.</p>";
            return $oldPath;
        }
    }
    return $oldPath;
}

// Fetch property details
$sql = "SELECT * FROM property WHERE pid=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $pid);
$stmt->execute();
$result = $stmt->get_result();
$property = $result->fetch_assoc();
$stmt->close();

if (!$property) {
    $error = "<div class='alert alert-danger'>Property not found.</div>";
}

if (isset($_POST['update']) && $property) {
    // --- Sanitize/Validate ---
    $title = trim($_POST['title']);
    $ptype = $_POST['ptype'];
    $stype = $_POST['stype'];
    $price_type = $_POST['price_type'];
    $price = $_POST['price'];
    $bed = (int) $_POST['bed'];
    $bath = (int) $_POST['bath'];
    $kitc = (int) $_POST['kitc'];
    $BHKtype = $_POST['BHKtype'];
    $balcony = (int) $_POST['balcony'];
    $hall = (int) $_POST['hall'];
    $totalfloor = (int) $_POST['totalfloor'];
    $floorcount = $_POST['floorcount'];
    $loc = trim($_POST['loc']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    $asize = trim($_POST['asize']);
    $feature = trim($_POST['feature']);
    $isFeatured = isset($_POST['isFeatured']) ? intval($_POST['isFeatured']) : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : 'available';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $uid = $_SESSION['uid'];
    $agent_id = $property['agent_id'];

    // Validation
    if (strlen($title) < 5)
        $error .= "<p>Title must be at least 5 characters.</p>";
    if (!is_numeric($price) || $price <= 0)
        $error .= "<p>Price must be a positive number.</p>";
    foreach (['bed', 'bath', 'kitc', 'balcony', 'hall', 'totalfloor'] as $numf) {
        if ($$numf < 0 || $$numf > 10)
            $error .= "<p>" . ucfirst($numf) . " must be between 0 and 10.</p>";
    }
    if (empty($ptype) || empty($stype) || empty($BHKtype) || empty($floorcount) || empty($city) || empty($state) || empty($loc) || empty($asize)) {
        $error .= "<p>All required fields must be filled.</p>";
    }

    // File upload rules
    $image_path = 'uploads/properties/';
    $uploadDir = 'admin/' . $image_path;
    if (!is_dir($uploadDir))
        mkdir($uploadDir, 0755, true);

    $allowedTypes = ['jpg', 'jpeg', 'png'];
    $maxSize = 2 * 1024 * 1024; // 2MB

    // Upload files to admin/uploads/properties/
    $aimage = processUpload($_FILES['aimage'], $uploadDir, $allowedTypes, $maxSize, $error);
    $aimage1 = processUpload($_FILES['aimage1'], $uploadDir, $allowedTypes, $maxSize, $error);
    $aimage2 = processUpload($_FILES['aimage2'], $uploadDir, $allowedTypes, $maxSize, $error);
    $aimage3 = processUpload($_FILES['aimage3'], $uploadDir, $allowedTypes, $maxSize, $error);
    $aimage4 = processUpload($_FILES['aimage4'], $uploadDir, $allowedTypes, $maxSize, $error);

    // Optional Images
    $aimage5 = isset($_FILES['aimage5']) ? processUpload($_FILES['aimage5'], $uploadDir, $allowedTypes, $maxSize, $error) : null;
    $aimage6 = isset($_FILES['aimage6']) ? processUpload($_FILES['aimage6'], $uploadDir, $allowedTypes, $maxSize, $error) : null;
    $groundimage = isset($_FILES['groundimage']) ? processUpload($_FILES['groundimage'], $uploadDir, $allowedTypes, $maxSize, $error) : null;
    $otherimage = isset($_FILES['Otherimage']) ? processUpload($_FILES['Otherimage'], $uploadDir, $allowedTypes, $maxSize, $error) : null;

    // Remove 'admin/' from file path before saving to DB
    function removeAdminPrefix($path)
    {
        return preg_replace('/^admin\//', '', $path);
    }

    $aimage = $aimage ? removeAdminPrefix($aimage) : null;
    $aimage1 = $aimage1 ? removeAdminPrefix($aimage1) : null;
    $aimage2 = $aimage2 ? removeAdminPrefix($aimage2) : null;
    $aimage3 = $aimage3 ? removeAdminPrefix($aimage3) : null;
    $aimage4 = $aimage4 ? removeAdminPrefix($aimage4) : null;
    $aimage5 = $aimage5 ? removeAdminPrefix($aimage5) : null;
    $aimage6 = $aimage6 ? removeAdminPrefix($aimage6) : null;
    $groundimage = $groundimage ? removeAdminPrefix($groundimage) : null;
    $otherimage = $otherimage ? removeAdminPrefix($otherimage) : null;


    // Only update if no critical errors
    if (empty($error)) {
        $sql = "UPDATE property SET
            title=?, ptype=?, stype=?, price_type=?, price=?, bed=?, bath=?, kitc=?, bhk=?, balcony=?, hall=?, totalfloor=?,
            floorcount=?, loc=?, city=?, state=?, asize=?, feature=?, pimage=?, pimage1=?, pimage2=?, pimage3=?, pimage4=?,
            pimage5=?, pimage6=?, groundimage=?, otherimage=?, status=?, uid=?, agent_id=?, isFeatured=?, description=?
            WHERE pid=?";

        $stmt = $conn->prepare($sql);
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
            header("Location: your_property.php?msg=" . urlencode($msg));
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
    <meta charset="utf-8">
    <title>Update Property - FindBrick</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="package/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">

    <link rel="shortcut icon" type="image/x-icon" href="images/fb-logo.png">

    <style>
        .property-form-wrapper {
            background: #fff;
            border-radius: 10px;
            padding: 30px;
            margin: 40px 0;
        }

        .section-title {
            position: relative;
            margin-bottom: 30px;
            padding-bottom: 15px;
        }

        .section-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: #007bff;
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

        .submit-btn {
            background: #007bff;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .submit-btn:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

        .alert {
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .form-control {
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: none;
        }

        .img-preview {
            height: 80px;
            margin: 3px 0;
        }
    </style>
</head>

<body>
    <?php include("include/header.php"); ?>
    <div class="main-container">
        <div class="container">
            <br><br>
            <div class="property-form-wrapper">
                <div class="row">
                    <div class="col-12">
                        <h2 class="section-title">Update Property</h2>
                        <?php echo $error; ?>
                        <?php echo $msg; ?>
                    </div>
                </div>
                <?php if ($property): ?>
                    <form method="post" enctype="multipart/form-data" autocomplete="off">
                        <!-- Basic Information Section -->
                        <div class="form-section">
                            <h5><i class="fa fa-info-circle"></i> Basic Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Property Title *</label>
                                        <input type="text" class="form-control" name="title"
                                            value="<?= htmlspecialchars($property['title']) ?>" required maxlength="30">
                                    </div>
                                    <div class="form-group">
                                        <label>Property Type *</label>
                                        <select class="form-control" required name="ptype">
                                            <option value="">Select Type</option>
                                            <?php foreach (['apartment', 'flat', 'building', 'house', 'villa', 'office'] as $opt): ?>
                                                <option value="<?= $opt ?>" <?= ($property['ptype'] == $opt ? 'selected' : '') ?>>
                                                    <?= ucfirst($opt) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Selling Type *</label>
                                        <select class="form-control" required name="stype">
                                            <option value="">Select Status</option>
                                            <option value="rent" <?= $property['stype'] == 'rent' ? 'selected' : '' ?>>Rent
                                            </option>
                                            <option value="sale" <?= $property['stype'] == 'sale' ? 'selected' : '' ?>>Sale
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Price *</label>
                                        <div class="input-group">
                                            <select class="form-control col-2" required name="price_type">
                                                <option value="">Price_type</option>
                                                <option value="cr" <?= $property['price_type'] == 'cr' ? 'selected' : '' ?>>Cr
                                                </option>
                                                <option value="lakh" <?= $property['price_type'] == 'lakh' ? 'selected' : '' ?>>Lakh</option>
                                                <option value="k" <?= $property['price_type'] == 'k' ? 'selected' : '' ?>>K
                                                </option>
                                            </select>
                                            <input type="text" class="form-control col-10" name="price" id="price"
                                                value="<?= htmlspecialchars($property['price']) ?>" required maxlength="7">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Property Details Section -->
                        <div class="form-section">
                            <h5><i class="fa fa-building"></i> Property Details</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Bedrooms *</label>
                                        <input type="number" class="form-control" name="bed" min="0" max="10"
                                            value="<?= $property['bed'] ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Bathrooms *</label>
                                        <input type="number" class="form-control" name="bath" min="0" max="10"
                                            value="<?= $property['bath'] ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Kitchen *</label>
                                        <input type="number" class="form-control" name="kitc" min="0" max="10"
                                            value="<?= $property['kitc'] ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>BHK *</label>
                                        <select class="form-control" required name="BHKtype">
                                            <?php foreach (['1 BHK', '2 BHK', '3 BHK', '4 BHK', '5 BHK', '1,2 BHK', '2,3 BHK', '2,3,4 BHK'] as $opt): ?>
                                                <option value="<?= $opt ?>" <?= ($property['bhk'] == $opt ? 'selected' : '') ?>>
                                                    <?= $opt ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Balcony *</label>
                                        <input type="number" class="form-control" name="balcony" min="0" max="10"
                                            value="<?= $property['balcony'] ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Hall *</label>
                                        <input type="number" class="form-control" name="hall" min="0" max="10"
                                            value="<?= $property['hall'] ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Total Floor *</label>
                                        <input type="number" class="form-control" name="totalfloor" min="0" max="10"
                                            value="<?= $property['totalfloor'] ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Location Section -->
                        <div class="form-section">
                            <h5><i class="fa fa-map-marker"></i> Location</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Address *</label>
                                        <input type="text" class="form-control" name="loc"
                                            value="<?= htmlspecialchars($property['loc']) ?>" required maxlength="70">
                                    </div>
                                    <div class="form-group">
                                        <label>Floor *</label>
                                        <select class="form-control" required name="floorcount">
                                            <?php foreach (['1 floor', '2 floor', '3 floor', '4 floor', '5 floor'] as $opt): ?>
                                                <option value="<?= $opt ?>" <?= ($property['floorcount'] == $opt ? 'selected' : '') ?>><?= ucfirst($opt) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Area Size (sqft) *</label>
                                        <input type="text" class="form-control" name="asize"
                                            value="<?= htmlspecialchars($property['asize']) ?>" required maxlength="10">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>State *</label>
                                        <select class="form-control" required name="state" id="state">
                                            <?php
                                            $sql = "select * from state";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->execute();
                                            $result = $stmt->get_result();
                                            $state = $result->fetch_all(MYSQLI_ASSOC);

                                            foreach ($state as $opt): ?>
                                                <option value="<?= $opt['sname'] ?>" <?= ($property['state'] == $opt['sname'] ? 'selected' : '') ?>>
                                                    <?= ucfirst($opt['sname']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>    
                                    </div>
                                    <div class="form-group">
                                        <label>City *</label>
                                        <select class="form-control" required name="city" id="city">
                                            <!-- <option value="">Select city</option> -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Feature Section -->
                        <div class="form-section">
                            <h5><i class="fa fa-star"></i> Features</h5>
                            <p class="alert alert-danger">
                                * Important Please Do Not Remove Below Content Only Change <b>Yes</b> Or <b>No</b> or
                                Details and Do Not Add More Details
                            </p>
                            <textarea class="tinymce form-control" name="feature" rows="10"
                                cols="30"><?= htmlspecialchars($property['feature']) ?></textarea>
                        </div>
                        <!-- Images & Status -->
                        <div class="form-section">
                            <h5><i class="fa fa-image"></i> Image & Status</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Main Image *</label>
                                        <input type="file" class="form-control" name="aimage" accept=".jpg,.jpeg,.png">
                                        <?php if ($property['pimage']): ?>
                                            <img src="admin/<?= $property['pimage'] ?>" class="img-preview">
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group">
                                        <label>Image 3 *</label>
                                        <input type="file" class="form-control" name="aimage3" accept=".jpg,.jpeg,.png">
                                        <?php if ($property['pimage3']): ?>
                                            <img src="admin/<?= $property['pimage3'] ?>" class="img-preview">
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group">
                                        <label>Floor Plan Image</label>
                                        <input type="file" class="form-control" name="groundimage" accept=".jpg,.jpeg,.png">
                                        <?php if ($property['groundimage']): ?>
                                            <img src="admin/<?= $property['groundimage'] ?>" class="img-preview">
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group">
                                        <label>Status *</label>
                                        <select class="form-control" name="status" required>
                                            <option value="available" <?= $property['status'] == 'available' ? 'selected' : '' ?>>Available</option>
                                            <option value="sold out" <?= $property['status'] == 'sold out' ? 'selected' : '' ?>>Sold Out</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Image 1 *</label>
                                        <input type="file" class="form-control" name="aimage1" accept=".jpg,.jpeg,.png">
                                        <?php if ($property['pimage1']): ?>
                                            <img src="admin/<?= $property['pimage1'] ?>" class="img-preview">
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group">
                                        <label>Image 4 *</label>
                                        <input type="file" class="form-control" name="aimage4" accept=".jpg,.jpeg,.png">
                                        <?php if ($property['pimage4']): ?>
                                            <img src="admin/<?= $property['pimage4'] ?>" class="img-preview">
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group">
                                        <label>Basement Floor Plan Image</label>
                                        <input type="file" class="form-control" name="aimage6" accept=".jpg,.jpeg,.png">
                                        <?php if ($property['pimage6']): ?>
                                            <img src="admin/<?= $property['pimage6'] ?>" class="img-preview">
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group">
                                        <label>Is Featured? *</label>
                                        <select class="form-control" name="isFeatured" required>
                                            <option value="0" <?= $property['isFeatured'] == 0 ? 'selected' : '' ?>>No</option>
                                            <option value="1" <?= $property['isFeatured'] == 1 ? 'selected' : '' ?>>Yes
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Image 2 *</label>
                                        <input type="file" class="form-control" name="aimage2" accept=".jpg,.jpeg,.png">
                                        <?php if ($property['pimage2']): ?>
                                            <img src="admin/<?= $property['pimage2'] ?>" class="img-preview">
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group">
                                        <label>Image 5</label>
                                        <input type="file" class="form-control" name="aimage5" accept=".jpg,.jpeg,.png">
                                        <?php if ($property['pimage5']): ?>
                                            <img src="admin/<?= $property['pimage5'] ?>" class="img-preview">
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group">
                                        <label>Ground Floor Plan Image</label>
                                        <input type="file" class="form-control" name="Otherimage" accept=".jpg,.jpeg,.png">
                                        <?php if ($property['otherimage']): ?>
                                            <img src="admin/<?= $property['otherimage'] ?>" class="img-preview">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-section">
                                <h5><i class="fa fa-list"></i> Description</h5>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <textarea class="tinymce form-control" name="description"
                                                rows="5" maxlength="100"><?= htmlspecialchars($property['description']) ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Submit Button -->
                        <div class="text-center">
                            <button type="submit" name="update" class="submit-btn">
                                <i class="fa fa-check-circle"></i> Update Property
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php include("include/footer.php"); ?>
    <script src="package/Jquery/dist/jquery.min.js"></script>
    <script src="package/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="js/all.min.js"></script>
    <script src="js/script.js"></script>
    <script src="package/tinymce/tinymce.min.js"></script>
    <script src="package/tinymce/init-tinymce.min.js"></script>

    <script>
        // --- Only allow digits on price input ---
        $('#price').on('keypress', function(e) {
            if ($.inArray(e.keyCode, [8, 9, 37, 39, 46]) !== -1) return;
            if (e.which < 48 || e.which > 57) e.preventDefault();
        });
        $('#submitPropertyBtn').on('click', function() {
            if (!valid) e.preventDefault();
        });
    </script>
    <script>
        $(document).ready(function() {
            function loadCities(selectedState, selectedCity) {
                var $citySelect = $('#city');
                $citySelect.html('<option value="">Loading...</option>');
                if (selectedState) {
                    $.ajax({
                        url: 'ajax/get_city_by_state.php',
                        type: 'GET',
                        data: {
                            state: selectedState
                        },
                        dataType: 'json',
                        success: function(cities) {
                            var options = '<option value="">Select city</option>';
                            $.each(cities, function(i, city) {
                                var selected = (city === selectedCity) ? ' selected' : '';
                                options += '<option value="' + city + '"' + selected + '>' + city + '</option>';
                            });
                            $citySelect.html(options);
                        }
                    });
                } else {
                    $citySelect.html('<option value="">Select city</option>');
                }
            }

            $('#state').on('change', function() {
                loadCities($(this).val(), null);
            });

            // On page load, if editing (propertyedit.php)
            <?php if (isset($property['state']) && isset($property['city'])): ?>
                loadCities("<?php echo addslashes($property['state']); ?>", "<?php echo addslashes($property['city']); ?>");
            <?php endif; ?>
        });
    </script>
     <script>
        // --- Only allow digits on price input ---
        $('#price').on('keypress', function(e) {
            if ($.inArray(e.keyCode, [8, 9, 37, 39, 46]) !== -1) return;
            if (e.which < 48 || e.which > 57) e.preventDefault();
        });

        $('input[type="number"]').on('input', function() {
            var value = $(this).val();
            if (value < 0) $(this).val(0);
            if (value > 10) $(this).val(10);
        });
    </script>
</body>

</html>