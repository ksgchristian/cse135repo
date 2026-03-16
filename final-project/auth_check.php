<?php
session_start();

function requireLogin() {
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit();
    }
}

function requireRole($allowedRoles) {
    requireLogin();

    if (!in_array($_SESSION['role'], $allowedRoles, true)) {
        http_response_code(403);
        echo "<h1>403 Forbidden</h1>";
        echo "<p>You do not have permission to access this page.</p>";
        exit();
    }
}
?>
