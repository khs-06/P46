<?php
include("config.php");
$user = $_GET['id'];  // yaha 'id' actually user name hoga
$sql = "DELETE FROM admin WHERE user = '$user'";
$result = mysqli_query($con, $sql);

if($result) {
    $msg = "<p class='alert alert-success'>Admin Deleted</p>";
    header("Location:adminlist.php?msg=$msg");
} else {
    $msg = "<p class='alert alert-warning'>Admin Not Deleted</p>";
    header("Location:adminlist.php?msg=$msg");
}
mysqli_close($con);
?>
