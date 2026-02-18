<?php
session_start();
include("config.php");

// Check admin session
if (!isset($_SESSION['auser'])) {
    header("location:index.php");
    exit();
}

// Check if delete ID exists
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // correct ID variable

    // Fetch user data
    $getUser = mysqli_query($con, "SELECT * FROM user WHERE uid='$id'");
    if ($getUser && mysqli_num_rows($getUser) > 0) {
        $user = mysqli_fetch_assoc($getUser);
        $userType = $user['utype'];
        $userImage = "user/" . $user['image'];

        // Delete user image if exists
        if (!empty($user['image']) && file_exists($userImage)) {
            unlink($userImage);
        }

        // Delete from related tables based on user type
        if ($userType == 'agent') {
            // echo '$<script>confirm("you want to delete this agent properties");</script>';
            // if (true) {
                $deleteagent = mysqli_query($con, "DELETE FROM agent WHERE user_id='$id'");
                mysqli_query($con, "DELETE FROM property WHERE uid='$id'");
            // }
        } 
        // elseif ($userType == 'builder') {
        //     mysqli_query($con, "DELETE FROM builder WHERE user_id='$id'");
        // } elseif ($userType == 'client') {
        //     mysqli_query($con, "DELETE FROM client WHERE user_id='$id'");
        // }

        // Delete from user table
        $deleteUser = mysqli_query($con, "DELETE FROM user WHERE uid='$id'");

        if ($deleteUser && $deleteagent) {
            echo "<script>alert('User deleted successfully from all tables'); window.location='useragent.php';</script>";
        } else {
            echo "<script>alert('Error deleting user'); window.location='useragent.php';</script>";
        }
    } else {
        echo "<script>alert('User not found!'); window.location='useragent.php';</script>";
    }
} else {
    header("location:useragent.php");
    exit();
}
?>
