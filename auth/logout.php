<?php

include '../includes/header.php';
?>

<div class="container d-flex flex-column justify-content-center align-items-center vh-100">
    <div class="card p-4 text-center">
        <h2>Are you sure you want to log out?</h2>
        <div class="mt-3">
            <a href="confirm_logout.php" class="btn btn-danger">Yes, Logout</a>
            <a href="../views/dashboard.php" class="btn btn-secondary">No, Go Back</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
