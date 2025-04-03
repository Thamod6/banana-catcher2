<?php
require_once 'includes/session.php';

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Clear remember token if it exists
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time()-3600, '/');
}

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
?> 