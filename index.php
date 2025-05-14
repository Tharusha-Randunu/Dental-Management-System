<?php
session_start();

// Include the database connection
include 'config/db.php';

if (!isset($_SESSION['username'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: login.php");
    exit();
} else {
    // If logged in, redirect to the dashboard inside the 'views' folder
    header("Location: views/dashboard.php");
    exit();
}
?>
