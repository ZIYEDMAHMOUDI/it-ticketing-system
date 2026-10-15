<?php
// Stops the page if the user is not logged in or does not have an allowed role
function requireRole(array $allowedRoles): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }

    if (!in_array($_SESSION['role'], $allowedRoles, true)) {
        http_response_code(403);
        die('Access denied.');
    }
}