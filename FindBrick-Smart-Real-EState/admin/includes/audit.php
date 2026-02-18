<?php
// fb1/admin/includes/audit.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config.php';

function log_action($action, $entity, $entity_id = null, $details = null) {
    
    global $con;

    // who is acting (fallback to 'system')
    $actor = isset($_SESSION['auser']) ? $_SESSION['auser'] : 'system';

    // request metadata
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;

    // statement
    $sql = "INSERT INTO activity_log (actor, action, entity, entity_id, details, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('sssssss', $actor, $action, $entity, $entity_id, $details, $ip, $ua);
    $stmt->execute();
}
