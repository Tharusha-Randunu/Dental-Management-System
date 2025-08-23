<?php
// Start session only if none started yet
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


$currentFile = basename($_SERVER['PHP_SELF']);

// Define pages that don't require any login
$publicPages = [
    'index.php',
    'login.php',
    'patient_login.php',
    'forgot_password.php',
    'patient_forgot_password.php',
];

// Patient-only pages
$patientPages = [
    'patient_dashboard.php',
    'confirm_patient_logout.php',
    'edit_patient_profile.php',
    'patient_logout.php',
    'patient_reset_password.php',
];

// User-only pages
$userPages = [
    'dashboard.php',
    'confirm_logout.php',
    'edit_user_profile.php',
    'logout.php',
];

// Pages accessible by both logged-in users or patients
$sharedPages = [
    'view_bill.php',
    'view_lab_bill.php',
    'view_test_result.php',
];

// Session flags
$userLoggedIn = isset($_SESSION['username']);
$patientLoggedIn = isset($_SESSION['patient_username']);

// Helper function to check if current page is in an array of pages
function pathInArray($current, $array) {
    return in_array($current, $array);
}

// Access control logic
if (!pathInArray($currentFile, $publicPages)) {
    if (pathInArray($currentFile, $patientPages)) {
        // Patient pages require patient login
        if (!$patientLoggedIn) {
            header("Location: /Dental_System/patient_modules/patient_login.php");
            exit();
        }
    } elseif (pathInArray($currentFile, $userPages)) {
        // User pages require user login
        if (!$userLoggedIn) {
            header("Location: /Dental_System/auth/login.php");
            exit();
        }
    } elseif (pathInArray($currentFile, $sharedPages)) {
        // Shared pages require either user OR patient login
        if (!$userLoggedIn && !$patientLoggedIn) {
                        header("Location: /Dental_System/auth/login.php");
            exit();
        }
    } else {
        // If page not listed anywhere, default to user login required
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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dental Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />

    <style>
        body {
            background-color: #f0f4f8;
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>
<body>
    <div class="container">
