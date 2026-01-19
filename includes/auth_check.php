<?php
// includes/auth_check.php
// This file is included at the top of any page that requires
// the user to be logged in. If not logged in, redirect to login page.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user_id'])) {
    // User is not logged in, redirect to login.
    header('Location: /WorkReminder/auth/login.php');
    exit;
}

