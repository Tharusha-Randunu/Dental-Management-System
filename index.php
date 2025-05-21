<?php
session_start();
include 'config/db.php';

// Redirect if staff user already logged in
if (isset($_SESSION['username'])) {
    header("Location: views/dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Dental Hub - DMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />

<style>
  body {
    min-height: 100vh;
    margin: 0;
    background: linear-gradient(135deg, #1e3c72, #2a5298, #6dd5fa);
    background-size: 300% 300%;
    animation: gradientBackground 12s ease infinite;
  }

  @keyframes gradientBackground {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }

  .page-container {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 4rem 3rem; /* increased padding */
  }

  .text-center {
    max-width: 900px;
    width: 100%;
  }

  .text-center > h1 {
    margin-bottom: 2rem; /* increased spacing below title */
  }

  .text-center > p {
    margin-bottom: 3rem; /* increased spacing below subtitle */
  }

  .d-flex.gap-4.flex-wrap.justify-content-center {
    gap: 2.5rem !important; /* increase gap between cards */
  }

  .card:hover {
    transform: translateY(-4px);
    transition: 0.3s ease;
  }
</style>
</head>
<body>

<div class="page-container">
    <div class="text-center">
        <h1 class="mb-3 text-white fw-bold">Dental Hub - DMS</h1>
        <p class="text-white-50 mb-4">Welcome to your trusted Dental Management System</p>

        <div class="d-flex gap-4 flex-wrap justify-content-center">
            <!-- Patient Login Card -->
            <a href="patient_modules/patient_login.php" class="text-decoration-none">
                <div class="card shadow-sm p-4 border-0" style="width: 260px;">
                    <div class="card-body">
                        <div class="mb-3 text-primary">
                            <i class="bi bi-person-circle" style="font-size: 2.5rem;"></i>
                        </div>
                        <h5 class="card-title">Patient Login</h5>
                        <p class="card-text text-muted small">Access your dental records and appointments</p>
                        <button class="btn btn-outline-primary mt-3 w-100">Go to Patient Login</button>
                    </div>
                </div>
            </a>

            <!-- Staff Login Card -->
            <a href="auth/login.php" class="text-decoration-none">
                <div class="card shadow-sm p-4 border-0" style="width: 260px;">
                    <div class="card-body">
                        <div class="mb-3 text-primary">
                            <i class="bi bi-people-fill" style="font-size: 2.5rem;"></i>
                        </div>
                        <h5 class="card-title">Staff Login</h5>
                        <p class="card-text text-muted small">Manage patients, appointments and more</p>
                        <button class="btn btn-outline-primary mt-3 w-100">Go to Staff Login</button>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

</body>
</html>
