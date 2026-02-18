<?php
// include("../config.php");
include("../session-check.php");
// session_start();
// include("config.php");

$user_id = $_SESSION['uid'] ?? 0;
$property_id = intval($_POST['property_id'] ?? 0);

if (!$user_id) {
    echo 'login';
    exit;
}

$stmt = $conn->prepare("SELECT id FROM property_likes WHERE user_id=? AND property_id=?");
$stmt->bind_param("ii", $user_id, $property_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    // Already liked, so unlike
    $conn->query("DELETE FROM property_likes WHERE user_id=$user_id AND property_id=$property_id");
    echo 'unliked';
} else {
    // Not yet liked, so insert
    $conn->query("INSERT INTO property_likes (user_id, property_id) VALUES ($user_id, $property_id)");
    echo 'liked';
}
?>