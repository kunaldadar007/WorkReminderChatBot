<?php
// index.php
// Redirect to login or dashboard depending on session.
session_start();
if (!empty($_SESSION['user_id'])) {
    header('Location: /WorkReminder/dashboard.php');
} else {
    header('Location: /WorkReminder/auth/login.php');
}
exit;

