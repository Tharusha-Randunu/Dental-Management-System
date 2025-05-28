<?php
// Start session only if none started yet
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}




$currentFile = basename($_SERVER['PHP_SELF']);

// Pages that don't require login
$publicPages = ['index.php', 'login.php', 'patient_login.php'];

// Only explicitly list patient-only pages
$patientPages = ['patient_dashboard.php', 'confirm_patient_logout.php', 'edit_patient_profile.php','patient_forgot_password.php','patient_logout.php','patient_reset_password.php']; 

$userLoggedIn = isset($_SESSION['username']);
$patientLoggedIn = isset($_SESSION['patient_username']);

if (!in_array($currentFile, $publicPages)) {
    if (in_array($currentFile, $patientPages)) {
        if (!$patientLoggedIn) {
            header("Location: /Dental_System/patient_modules/patient_login.php");
            exit();
        }
    } else {
        // Everything else is assumed to be user-restricted
        if (!$userLoggedIn) {
            header("Location: /Dental_System/auth/login.php");
            exit();
        }
    }
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
        

