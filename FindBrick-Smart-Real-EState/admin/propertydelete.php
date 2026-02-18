<?php
include("config.php");

if (isset($_GET['pid'])) {
    $pid = intval($_GET['pid']);  // always sanitize user input
    
    $sql = "DELETE FROM property WHERE pid = {$pid}";
    $result = mysqli_query($con, $sql);

    if ($result) {
        $msg = "<p class='alert alert-success'>Property Deleted</p>";
    } else {
        $msg = "<p class='alert alert-warning'>Property Not Deleted</p>";
    }

    header("Location: propertyview.php?msg=" . urlencode($msg));
    exit();
} else {
    $msg = "<p class='alert alert-danger'>No property ID provided</p>";
    header("Location: propertyview.php?msg=" . urlencode($msg));
    exit();
}

mysqli_close($con);
?>
