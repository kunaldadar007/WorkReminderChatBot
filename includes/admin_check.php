<?php
// includes/admin_check.php
// Require user to be logged in AND have admin role.

require_once __DIR__ . '/auth_check.php';

if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    die('Access denied: admin only.');
}

