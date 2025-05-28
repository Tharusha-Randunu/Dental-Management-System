<?php
// Start session only if none started yet
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if user is not logged in and not already on the login page
$currentPage = basename($_SERVER['PHP_SELF']);
if (!isset($_SESSION['username']) && $currentPage !== 'login.php') {
    header("Location: ../auth/login.php"); // Adjust path if needed
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dental Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f0f4f8;
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>
<body>
    <div class="container">
