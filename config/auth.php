<?php
session_start();
// Check if admin is logged in
function check_login() {
    return isset($_SESSION['admin_id']);
}

// Check if user is admin
function check_admin() {
    return isset($_SESSION['admin_id']); // Assuming admin_id implies admin role
}

// Redirect to login page if not logged in
function redirect_if_not_logged_in() {
    if (!check_login()) {
        header('Location: admin_login.php'); // Redirect to admin login page
        exit;
    }
}

// Redirect to dashboard if logged in
function redirect_if_logged_in() {
    if (check_login()) {
        header('Location: admin_dashboard.php'); // Redirect to admin dashboard page
        exit;
    }
}

// Redirect to login page if not admin
function redirect_if_not_admin() {
    if (!check_admin()) {
        header('Location: admin_login.php'); // Redirect to admin login page
        exit;
    }
}
?>
