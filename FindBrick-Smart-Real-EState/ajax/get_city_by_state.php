<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("../config.php"); // Make sure config.php exists and sets $conn

header('Content-Type: application/json; charset=utf-8');

if (isset($_GET['state'])) {
    $state = $_GET['state'];
    $stmt = $conn->prepare("SELECT city.cname FROM city JOIN state ON city.sid = state.sid WHERE state.sname = ?");
    $stmt->bind_param("s", $state);
    $stmt->execute();
    $result = $stmt->get_result();
    $cities = [];
    while ($row = $result->fetch_assoc()) {
        $cities[] = $row['cname'];
    }
    echo json_encode($cities);
} else {
    echo json_encode([]);
}
?>