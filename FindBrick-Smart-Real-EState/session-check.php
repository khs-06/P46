<?php
// Start output buffering to prevent "headers already sent" errors
ob_start();

ini_set('session.cache_limiter', 'public');
session_cache_limiter(false);

try {
    // Start session
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
        include("config.php");
    }

    // Check login session
    if (!isset($_SESSION['uemail'])) {
        // Redirect to login if not logged in
        header("Location: login.php");
        exit();
    }
} catch (Exception $e) {
    // Handle session exceptions gracefully
    error_log('Session error: ' . $e->getMessage());
    // Optionally, redirect to an error page or login
    header("Location: login.php?error=session");
    exit();
}

// End output buffering
ob_end_flush();
?>