<?php
include("session-check.php");

// Helper function: Optimize image (resize to max width, compress)
function optimizeImage($src, $dest, $max_width = 1200, $quality = 80)
{
    $info = getimagesize($src);
    if (!$info)
        return false;
    $mime = $info['mime'];

    if ($mime == 'image/jpeg') {
        $image = imagecreatefromjpeg($src);
    } elseif ($mime == 'image/png') {
        $image = imagecreatefrompng($src);
    } elseif ($mime == 'image/jpg') {
        $image = imagecreatefromjpeg($src);
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

// Helper function: Secure file upload with validation
function processUpload($file, $uploadDir, $allowedTypes, $maxSize, &$error, $optimize = true)
{
    if ($file['error'] == 0) {
        $fileName = basename($file['name']);
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $targetPath = $uploadDir . time() . '_' . uniqid() . '.' . $fileType;

        // Validate file
        if (!in_array($fileType, $allowedTypes)) {
            $error .= "<p>{$fileName}: Invalid file type.</p>";
            return null;
        }
        if ($file['size'] > $maxSize) {
            $error .= "<p>{$fileName}: Exceeds max size of 2MB.</p>";
            return null;
        }
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // Optimize image if it's jpg/png
            if ($optimize && in_array($fileType, ['jpg', 'jpeg', 'png'])) {
                optimizeImage($targetPath, $targetPath);
            }
            return $targetPath;
        } else {
            $error .= "<p>{$fileName}: Upload failed.</p>";
            return null;
        }
    }
    return null;
}

// Validation and Insert Logic
$error = "";
$msg = "";

// Feature/Amenities handling (example: you can expand as needed)
// function insertFeatures($conn, $property_id, $features = [])
// {
//     foreach ($features as $feature) {
//         $stmt = $conn->prepare("INSERT INTO property_features (property_id, feature) VALUES (?, ?)");
//         $stmt->bind_param("is", $property_id, $feature);
//         $stmt->execute();
//     }
// }

// function insertAmenities($conn, $property_id, $amenities = [])
// {
//     foreach ($amenities as $amenity) {
//         $stmt = $conn->prepare("INSERT INTO property_amenities (property_id, amenity) VALUES (?, ?)");
//         $stmt->bind_param("is", $property_id, $amenity);
//         $stmt->execute();
//     }
// }

if (!isset($_SESSION['uemail'])) {
    header("location:login.php");
    exit();
}

if (isset($_POST['add'])) {
    // --- Sanitize and Validate ---
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

    // New fields
    $isFeatured = isset($_POST['isFeatured']) ? intval($_POST['isFeatured']) : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : 'available';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $uid = $_SESSION['uid'];

    $agent_id = $stmt = $conn->prepare("SELECT id FROM agent WHERE email=?");
    $stmt->bind_param("s", $_SESSION['uemail']);
    $stmt->execute();
    $result = $stmt->get_result();
    $agent_id = $result->fetch_assoc()['id'] ?? 0;
    $stmt->close();
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


    // Only insert if no critical errors
    if (empty($error) && $aimage && $aimage2 && $aimage3 && $aimage4) {
        $sql = "INSERT INTO property (
            title, ptype, stype, price_type, price, bed, bath, kitc, bhk, balcony, hall, totalfloor,
            floorcount, loc, city, state, asize, feature, pimage, pimage1, pimage2, pimage3, pimage4,
            pimage5, pimage6, groundimage, otherimage, status, uid, agent_id, isFeatured, description
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssssiiisiiisssssssssssssssiiiis",
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
            $property_id = $conn->insert_id;

            // Example: handle features/amenities arrays if you collect those from your form
            $features_array = isset($_POST['features']) ? $_POST['features'] : [];
            $amenities_array = isset($_POST['amenities']) ? $_POST['amenities'] : [];
            // insertFeatures($conn, $property_id, $features_array);
            // insertAmenities($conn, $property_id, $amenities_array);

            $msg = "<p class='alert alert-success'>Property Added Successfully!</p>";
        } else {
            $error .= "<p class='alert alert-danger'>Database error: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        if (empty($error))
            $error .= "<p class='alert alert-danger'>Required images are missing or invalid format.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Add Property - FindBrick</title>
    <link rel="stylesheet" href="package/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="package/font-awesome/css/font-awesome.min.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/fb-logo.png">

    <link rel="stylesheet" href="css/all.min.css">
    <!-- Google Fonts: Poppins & Playfair Display -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">

    <style>
        .property-form-wrapper {
            background: #ffffff;
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
                        <h2 class="section-title">Submit New Property</h2>
                        <?php echo $error; ?>
                        <?php echo $msg; ?>
                    </div>
                </div>
                <form method="post" enctype="multipart/form-data" autocomplete="off">
                    <!-- Basic Information Section -->
                    <div class="form-section">
                        <h5><i class="fa fa-info-circle"></i> Basic Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Property Title *</label>
                                    <input type="text" class="form-control" name="title" required
                                        placeholder="Enter Title" maxlength="30">
                                </div>
                                <div class="form-group">
                                    <label>Property Type *</label>
                                    <select class="form-control" required name="ptype">
                                        <option value="">Select Type</option>
                                        <option value="apartment">Apartment</option>
                                        <option value="flat">Flat</option>
                                        <option value="building">Building</option>
                                        <option value="house">House</option>
                                        <option value="villa">Villa</option>
                                        <option value="office">Office</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Selling Type *</label>
                                    <select class="form-control" required name="stype">
                                        <option value="">Select Status</option>
                                        <option value="rent">Rent</option>
                                        <option value="sale">Sale</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Price *</label>
                                    <div class="input-group">
                                        <select class="form-control col-2" required name="price_type">
                                            <option value="">Price_type</option>
                                            <option value="cr">Cr</option>
                                            <option value="lakh">Lakh</option>
                                            <option value="k">K</option>
                                        </select>
                                        <input type="text" class="form-control col-10" name="price" id="price" required
                                            placeholder="Enter Price" maxlength="7">
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
                                    <input type="number" class="form-control" name="bed" min="0" max="10" required
                                        placeholder="Enter Bedroom (Only no 1 to 10)">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Bathrooms *</label>
                                    <input type="number" class="form-control" name="bath" min="0" max="10" required
                                        placeholder="Enter Bathroom (Only no 1 to 10)">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Kitchen *</label>
                                    <input type="number" class="form-control" name="kitc" min="0" max="10" required
                                        placeholder="Enter Kitchen (Only no 1 to 10)">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>BHK *</label>
                                    <select class="form-control" required name="BHKtype">
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Balcony *</label>
                                    <input type="number" class="form-control" name="balcony" min="0" max="10" required
                                        placeholder="Enter Balcony (Only no 1 to 10)">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Hall *</label>
                                    <input type="number" class="form-control" name="hall" min="0" max="10" required
                                        placeholder="Enter Hall (Only no 1 to 10)">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Total Floor *</label>
                                    <input type="number" class="form-control" name="totalfloor" min="0" max="10"
                                        required placeholder="Enter Floor (Only no 1 to 10)">
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
                                    <input type="text" class="form-control" name="loc" required
                                        placeholder="Enter Address" maxlength="70">
                                </div>
                                <div class="form-group">
                                    <label>Floor *</label>
                                    <select class="form-control" required name="floorcount">
                                        <option value="">Select Floor</option>
                                        <option value="1 floor">1st Floor</option>
                                        <option value="2 floor">2nd Floor</option>
                                        <option value="3 floor">3rd Floor</option>
                                        <option value="4 floor">4th Floor</option>
                                        <option value="5 floor">5th Floor</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Area Size (sqft) *</label>
                                    <input type="text" class="form-control" name="asize" id="asize" required
                                        placeholder="Enter Area Size (in sqft)" maxlength="10">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>State *</label>
                                    <select class="form-control" required name="state" id="state">
                                        <option value="">Select state</option>
                                        <?php
                                        $sql = "select * from state";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        $state = $result->fetch_all(MYSQLI_ASSOC);
                                        foreach ($state as $index => $stateData):
                                        ?>
                                            <option value="<?php echo $stateData["sname"] ?>">
                                                <?php echo $stateData["sname"] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>City *</label>
                                    <select class="form-control" required name="city" id="city">
                                        <option value="">Select city</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Feature Section (like submitproperty.php) -->
                    <div class="form-section">
                        <!-- <label>Feature</label> -->
                        <h5><i class="fa fa-star"></i> Features</h5>
                        <p class="alert alert-danger">
                            * Important Please Do Not Remove Below Content Only Change <b>Yes</b> Or <b>No</b> or
                            Details and Do Not Add More Details
                        </p>
                        <textarea class="tinymce form-control" name="feature" rows="10" cols="30">
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
                    <!-- Images & Status Section (like submitproperty.php) -->
                    <div class="form-section">
                        <h5><i class="fa fa-image"></i> Image & Status</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Main Image *</label>
                                    <input type="file" class="form-control" name="aimage" accept=".jpg,.jpeg,.png"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label>Image 3 *</label>
                                    <input class="form-control" name="aimage3" type="file" accept=".jpg,.jpeg,.png"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label>Floor Plan Image</label>
                                    <input class="form-control" name="groundimage" type="file" accept=".jpg,.jpeg,.png">
                                </div>
                                <div class="form-group">
                                    <label>Status *</label>
                                    <select class="form-control" required name="status">
                                        <option value="">Select Status</option>
                                        <option value="available">Available</option>
                                        <option value="sold out">Sold Out</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Image 1 *</label>
                                    <input class="form-control" name="aimage1" type="file" accept=".jpg,.jpeg,.png"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label>Image 4 *</label>
                                    <input type="file" class="form-control" name="aimage4" accept=".jpg,.jpeg,.png"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label>Basement Floor Plan Image</label>
                                    <input class="form-control" name="aimage6" type="file" accept=".jpg,.jpeg,.png">
                                </div>
                                <div class="form-group">
                                    <label>Is Featured? *</label>
                                    <select class="form-control" required name="isFeatured">
                                        <option value="">Select...</option>
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Image 2 *</label>
                                    <input type="file" class="form-control" name="aimage2" accept=".jpg,.jpeg,.png"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label>Image 5</label>
                                    <input type="file" class="form-control" name="aimage5" accept=".jpg,.jpeg,.png">
                                </div>
                                <div class="form-group">
                                    <label>Ground Floor Plan Image</label>
                                    <input class="form-control" name="Otherimage" type="file" accept=".jpg,.jpeg,.png">
                                </div>
                            </div>
                        </div>
                        <div class="form-section">
                            <h5><i class="fa fa-list"></i> Description</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <textarea class="tinymce form-control" name="description" rows="5"
                                            placeholder="Enter property description..." maxlength="10"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Submit Button -->
                    <div class="text-center">
                        <button type="submit" name="add" id="submitPropertyBtn" class="submit-btn">
                            <i class="fa fa-check-circle"></i> Submit Property
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include("include/footer.php"); ?>
    <!-- <script src="package/Jquery/dist/jquery.slim.min.js"></script> -->
    <script src="package/Jquery/dist/jquery.min.js"></script>
    <script src="package/popper/dist/popper.min.js"></script>
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
            $('#state').on('change', function() {
                var selectedState = $(this).val();
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
                                options += '<option value="' + city + '">' + city + '</option>';
                            });
                            $citySelect.html(options);
                        },
                        error: function(xhr, status, error) {
                            $citySelect.html('<option value="">Error loading cities</option>');
                            console.log('AJAX error:', status, error);
                        }
                    });
                } else {
                    $citySelect.html('<option value="">Select city</option>');
                }
            });
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