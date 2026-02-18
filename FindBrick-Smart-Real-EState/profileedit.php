<?php
include("session-check.php");
include("config.php");

$error = $msg = '';
$uid = $_SESSION['uid'] ?? 0;
$uemail = $_SESSION['uemail'] ?? '';

// Fetch user type and current data
$stmt = $conn->prepare("SELECT * FROM user WHERE uid=?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$utype = $user['utype'] ?? '';
$isAgentBuilder = ($utype === 'agent' || $utype === 'builder');

// If agent/builder, get agent table data
$agent = [];
if ($isAgentBuilder) {
    $stmt = $conn->prepare("SELECT * FROM agent WHERE email=?");
    $stmt->bind_param("s", $uemail);
    $stmt->execute();
    $agent = $stmt->get_result()->fetch_assoc() ?: [];
    $stmt->close();
}

// Handle form submit
if (isset($_POST['update'])) {
    // Common fields
    $name = trim($_POST['name']);
    // $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    // $password = trim($_POST['password']);
    $image = $user['image'] ?? '';
    // Agent/builder fields
    $city = $bio = $speciality = '';
    $agentImage = $agent['image'] ?? '';

    // Image upload logic
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $file = $_FILES['image'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $imgname = "uploads/profile/" . time() . '_' . uniqid() . '.' . $ext;
            if (!is_dir("uploads/profile")) mkdir("uploads/profile", 0755, true);
            move_uploaded_file($file['tmp_name'], $imgname);
            $image = $imgname;
            $agentImage = $imgname;
        } else {
            $error .= "<div class='alert alert-danger'>Invalid image format.</div>";
        }
    }

    // For agent/builder
    if ($isAgentBuilder) {
        $city = trim($_POST['city']);
        $bio = trim($_POST['bio']);
        $speciality = trim($_POST['speciality']);
    }

    // Update User table
    if (empty($error)) {
        $sql = "UPDATE user SET name=?, phone=?, image=? WHERE uid=?";
        $params = [$name, $email, $phone, $image];
        $types = "ssss";
        if (!empty($password)) {
            $params[] = password_hash($password, PASSWORD_DEFAULT);
            $types .= "s";
        }
        $params[] = $uid;
        $types .= "i";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        if ($stmt->execute()) {
            $msg .= "<div class='alert alert-success'>User profile updated.</div>";
        } else {
            $error .= "<div class='alert alert-danger'>User update failed.</div>";
        }
        $stmt->close();
    }

    // Update Agent table if agent/builder
    if ($isAgentBuilder && empty($error)) {
        $sql = "UPDATE agent SET name=?, email=?, phone=?, image=?, city=?, bio=?, speciality=?" . (!empty($password) ? ", password=?" : "") . " WHERE id=?";
        $params = [$name, $email, $phone, $agentImage, $city, $bio, $speciality];
        $types = "ssssss";
        if (!empty($password)) {
            $params[] = password_hash($password, PASSWORD_DEFAULT);
            $types .= "s";
        }
        $params[] = $agent['id'];
        $types .= "i";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        if ($stmt->execute()) {
            $msg .= "<div class='alert alert-success'>Agent/Builder profile updated.</div>";
        } else {
            $error .= "<div class='alert alert-danger'>Agent/Builder update failed.</div>";
        }
        $stmt->close();
    }
    // Refresh data after update
    header("Location: profile_edit.php?msg=" . urlencode($msg));
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Edit Profile - FindBrick</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="package/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <!-- Google Fonts: Poppins & Playfair Display -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" type="image/x-icon" href="images/fb-logo.png">
    <link rel="stylesheet" href="css/header.css">
    <style>
        body { background: #f6f8fb; }
        .profile-edit-card {
            max-width: 540px;
            margin: 40px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,.07);
            padding: 32px;
        }
        .profile-img {
            width: 110px;
            height: 110px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #007bff;
            margin-bottom: 16px;
        }
        .form-group label { font-weight: 500; }
    </style>
</head>
<body>
    <?php include("include/header.php"); ?>
    <br><br><br><br>
    <div class="container">
        <div class="profile-edit-card shadow">
            <h3 class="mb-4 text-primary text-center">Edit Profile</h3>
            <?= $error ?>
            <?= $msg ?>
            <form method="post" enctype="multipart/form-data" autocomplete="off">
                <div class="text-center mb-3">
                    <img src="admin/<?= htmlspecialchars($user['uimage'] ?? 'images/default-profile.png') ?>" class="profile-img" id="previewImg">
                    <div>
                        <input type="file" name="image" accept=".jpg,.jpeg,.png" class="d-block mx-auto mt-2" onchange="readURL(this)">
                    </div>
                </div>
                <div class="form-group">
                    <label>Name *</label>
                    <input type="text" class="form-control" name="name" required maxlength="50"
                        value="<?= htmlspecialchars($user['uname'] ?? $agent['uname'] ?? '') ?>">
                </div>
                <!-- <div class="form-group">
                    <label>Email *</label>
                    <input type="email" class="form-control" name="email" required maxlength="80"
                        value="<?= htmlspecialchars($user['uemail'] ?? $agent['uemail'] ?? '') ?>">
                </div> -->
                <!-- <div class="form-group">
                    <label>Password <span class="text-muted">(leave blank to keep unchanged)</span></label>
                    <input type="password" class="form-control" name="password" maxlength="32" autocomplete="new-password">
                </div> -->
                <div class="form-group">
                    <label>Phone *</label>
                    <input type="text" class="form-control" name="phone" required maxlength="20"
                        value="<?= htmlspecialchars($user['uphone'] ?? $agent['uphone'] ?? '') ?>">
                </div>
                <?php if ($isAgentBuilder): ?>
                    <div class="form-group">
                        <label>City *</label>
                        <input type="text" class="form-control" name="city" required maxlength="40"
                            value="<?= htmlspecialchars($agent['city'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Bio</label>
                        <textarea class="form-control" name="bio" rows="3" maxlength="200"><?= htmlspecialchars($agent['bio'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Speciality</label>
                        <input type="text" class="form-control" name="speciality" maxlength="80"
                            value="<?= htmlspecialchars($agent['speciality'] ?? '') ?>">
                    </div>
                <?php endif; ?>
                <div class="text-center mt-4">
                    <button type="submit" name="update" class="btn btn-primary btn-lg px-5 rounded-pill">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php include("include/footer.php"); ?>
    <!-- <script src="package/Jquery/dist/jquery.min.js"></script>
    <script src="package/bootstrap/dist/js/bootstrap.min.js"></script> -->
    <script src="package/Jquery/dist/jquery.slim.min.js"></script>
    <script src="package/popper/dist/popper.min.js"></script>
    <script src="package/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="package/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/all.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        function readURL(input){
            if(input.files && input.files[0]){
                var reader = new FileReader();
                reader.onload = function(e){
                    $('#previewImg').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>