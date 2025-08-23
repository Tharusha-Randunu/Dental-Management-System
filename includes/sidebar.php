<?php 
// Start session if not started yet
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Only display sidebar if user (not patient) is logged in
if (isset($_SESSION['username'])):

// Make sure session and DB are available
if (!isset($name) || !isset($role) || !isset($modules)) {     
    include __DIR__ . '/../config/db.php';    

    $username = $_SESSION['username'];     
    $sql = "SELECT Role, Fullname, Profile_Picture FROM users WHERE Username='$username'";     
    $result = $conn->query($sql);     
    $user = $result->fetch_assoc();     
    $role = strtolower($user['Role']);     
    $name = $user['Fullname'];     
    $profilePic = $user['Profile_Picture'] ? 'data:image/jpeg;base64,' . base64_encode($user['Profile_Picture']) : '../assets/default-avatar.png';      

    $modules = [         
        'admin' => ['Patient Management', 'Appointment Scheduling', 'Billing Management', 'User Management', 'Inventory Management', 'Laboratory Management'],         
        'receptionist' => ['Patient Management', 'Appointment Scheduling', 'Billing Management'],         
        'lab_technician' => ['Patient Management', 'Laboratory Management'],         
        'dentist' => ['Patient Management', 'Laboratory Management']     
    ]; 
} 
?>

<style>
    /* Toggle button */
    .toggle-btn {
        position: fixed;
        top: 20px;
        right: 20px;  
        font-size: 26px;
        color: #0d47a1;
        background-color: rgb(255, 255, 255);
        padding: 10px 12px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        cursor: pointer;
        z-index: 1100;
        transition: background-color 0.3s ease;
    }

    .toggle-btn:hover {
        background-color: #bbdefb;
    }

    /* Sidebar styles */
    .sidebar {
        position: fixed;
        top: 0;
        right: -260px; 
        width: 250px;
        height: 100%;
        background: #f0f9ff;
        border-left: 2px solid #0d47a1;  
        padding: 25px 20px;
        transition: right 0.3s ease;  
        z-index: 1050;
        box-shadow: -4px 0 10px rgba(0,0,0,0.1);  
    }

    .sidebar.active {
        right: 0;  
    }

    .sidebar-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .sidebar-header img {
        width: 85px;
        height: 85px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #0d47a1;
    }

    .sidebar-header h5 {
        margin-top: 12px;
        margin-bottom: 4px;
        font-weight: 600;
        color: #0d47a1;
    }

    .sidebar-header small {
        font-size: 14px;
        color: #546e7a;
    }

    .sidebar a {
        display: block;
        color: #0d47a1;
        text-decoration: none;
        padding: 10px 0;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .sidebar a:hover {
        color: #1565c0;
    }

    .sidebar .btn-group {
        display: flex;
        flex-direction: column;
        margin-top: 30px;
    }

    .sidebar .btn {
        margin-bottom: 10px;
        font-size: 14px;
    }

   .btn-outline-primary.btn-sm:hover, .btn-outline-danger.btn-sm:hover:hover{
        color:aliceblue;
    }

    /* Optional backdrop */
    .sidebar-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        background: rgba(0,0,0,0.3);
        z-index: 1049;
    }

    .sidebar-backdrop.active {
        display: block;
    }
</style>

<!-- Toggle icon -->
<div class="toggle-btn" id="sidebarToggleBtn" onclick="toggleSidebar()">☰</div>


<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <img src="<?php echo $profilePic; ?>" alt="Profile Picture">
        <h5><?php echo $name; ?></h5>
        <small><?php echo ucfirst($role); ?></small>
    

    <?php

    echo "<a href='/Dental_System/views/dashboard.php'>Dashboard</a>";
    if (!empty($modules[$role])) {
        foreach ($modules[$role] as $module) {
            switch ($module) {
                case 'Patient Management':
                    echo "<a href='/Dental_System/modules/patient_management.php'>Patient Management</a>";
                    break;
                case 'Appointment Scheduling':
                    echo "<a href='/Dental_System/modules/appointment_scheduling.php'>Appointment Scheduling</a>";
                    break;
                case 'Billing Management':
                    echo "<a href='/Dental_System/modules/billing_management.php'>Billing Management</a>";
                    break;
                case 'User Management':
                    echo "<a href='/Dental_System/modules/user_management.php'>User Management</a>";
                    break;
                case 'Inventory Management':
                    echo "<a href='/Dental_System/modules/inventory_management.php'>Inventory Management</a>";
                    break;
                case 'Laboratory Management':
                    echo "<a href='/Dental_System/modules/laboratory_management.php'>Laboratory Management</a>";
                    break;
            }
        }
    }
    ?>

    <div class="btn-group">
        <a href="/Dental_System/modules/user_functions/view_user_profile.php" class="btn btn-outline-warning btn-sm ">View Profile</a>
        <a href="/Dental_System/modules/user_functions/edit_user_profile.php" class="btn btn-outline-primary btn-sm ">Edit Profile</a>
        <a href="/Dental_System/auth/logout.php" class="btn btn-outline-danger btn-sm ">Logout</a>
    </div>
</div></div>

<!-- Optional backdrop -->
<div class="sidebar-backdrop" id="sidebar-backdrop" onclick="toggleSidebar()"></div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        const toggleBtn = document.getElementById('sidebarToggleBtn');

        sidebar.classList.toggle('active');
        backdrop.classList.toggle('active');

        if (sidebar.classList.contains('active')) {
            toggleBtn.textContent = '✖'; // cross icon
        } else {
            toggleBtn.textContent = '☰'; // hamburger icon
        }
    }
</script>

<?php
endif;   
?>
