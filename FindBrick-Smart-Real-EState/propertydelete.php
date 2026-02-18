<?php
include("session-check.php");
// include("config.php");

if (!isset($_SESSION['uemail'])) {
    header("location:login.php");
    exit();
}

$pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;
$error = "";
$msg = "";

if ($pid > 0) {
    // Remove images and related data if needed, here we just delete property
    $stmt = $conn->prepare("DELETE FROM property WHERE pid=?");
    $stmt->bind_param("i", $pid);
    if ($stmt->execute()) {
        $msg = "<div class='alert alert-success'>Property deleted successfully!</div>";
        header("Location: feature_property.php?msg=" . urlencode($msg));
    } else {
        $error = "<div class='alert alert-danger'>Property not deleted. Database error.</div>";
        header("Location: ypur_property.php?msg=" . urlencode($msg));
    }
    $stmt->close();
}
