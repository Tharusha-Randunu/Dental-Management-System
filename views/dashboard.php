<?php
include '../includes/header.php';
include '../includes/sidebar.php'; // Include sidebar

// Include the database connection
include '../config/db.php'; // Adjust the path to 'config' folder

// Redirect if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");  // Adjusted to the new folder structure
    exit();
}

$username = $_SESSION['username'];

// Fetch the user's role and name from the database
$sql = "SELECT Role, Fullname FROM users WHERE Username='$username'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
$role = strtolower($user['Role']);  // Get the role (admin, receptionist, lab technician, dentist)
$name = $user['Fullname'];  // Get the name of the user

// Carousel modules visibility based on role
$modules = [
    'admin' => ['Patient Management', 'Appointment Scheduling', 'Billing Management', 'User Management', 'Inventory Management', 'Laboratory Management'],
    'receptionist' => ['Patient Management','Appointment Scheduling', 'Billing Management'],
    'lab_technician' => ['Patient Management','Laboratory Management'],
    'dentist' => ['Patient Management','Laboratory Management']
];

// Check if the role exists in the modules array, else set a default empty array
if (!array_key_exists($role, $modules)) {
    $modules[$role] = [];  // Assign an empty array if the role is invalid
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Dental Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
background: linear-gradient(135deg,rgb(215, 237, 255),rgb(239, 247, 251),rgb(176, 207, 251));
            background-size: 300% 300%;
            animation: gradientAnimation 15s ease infinite;
            font-family: 'Roboto', sans-serif;
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            max-width: 1200px;
        }
        h1 {
            color:rgb(41, 82, 159);
            font-weight: 600;
        }
        h4 {
            color: #6c757d;
        }
        .col-md-4 {
            display: flex;
            justify-content: center;
        }
        .card {
            margin: 20px 0;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 200px; /* Fixed height for all cards */
            width: 500px;  /* Fixed width for all cards */
            display: flex;
            flex-direction: column;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        .card-body {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            padding: 20px;
        }
        .card-title {
            font-weight: 500;
            color: #343a40;
        }
        .card-text {
            color: #495057;
            flex-grow: 1; /* Pushes button to bottom */
        }
        .module-btn {
            align-self: flex-start; /* Align button to bottom-left */
            margin-top: auto; /* Push button to the bottom */
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .module-btn:hover {
            background-color: #0056b3;
        }

        .module-btn {
            display: inline-flex;
            align-items: center;
            transition: transform 0.3s ease;
        }
        .module-btn i {
            margin-right: 8px;
            transition: transform 0.3s ease;
        }
        .module-btn:hover i {
            transform: translateX(5px);
        }

        
        /* Light pastel colors */
     /*   .bg-light-blue { background-color: #f0f9ff !important; } /* Lightest blue */
      /*  .bg-light-green { background-color: #f2fff5 !important; } /* Lightest green */
       /* .bg-light-yellow { background-color: #fffdf2 !important; } /* Lightest yellow */
      /*  .bg-light-pink { background-color: #fff5f8 !important; } /* Lightest pink */
       /* .bg-light-purple { background-color: #f8f5ff !important; } /* Lightest purple */
      /*  .bg-light-gray { background-color: #f9f9f9 !important; } /* Lightest gray */
    </style>
</head>
<body>
    

    <div class="container">
        <div class="text-center my-4">
            <h1>Dental Hub - DMS</h1>
            <h4>Welcome, <?php echo $name; ?>!</h4>
       
            <div class="d-flex justify-content-end p-3">
        <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
    </div>
       
       
        </div>
        

        <div class="row">
            <!-- Loop through the modules based on role -->
            <?php
            $colors = ['bg-light-blue', 'bg-light-green', 'bg-light-yellow', 'bg-light-pink', 'bg-light-purple', 'bg-light-gray'];
            $colorIndex = 0;

            if (!empty($modules[$role])) {
                foreach ($modules[$role] as $module) {
                    $colorClass = $colors[$colorIndex % count($colors)]; // Cycle through colors
                    echo "<div class='col-md-4 mb-4'>";
                    #echo "<div class='card $colorClass'>"; 
                    echo "<div class='card bg-white border'>";

                    echo "<div class='card-body'>";
                    echo "<h5 class='card-title'>$module</h5>";
                    echo "<div class='mt-auto'>"; // New div to push button to bottom-left

                    // Add buttons or links to navigate to module pages (with updated paths)
                    switch ($module) {
                        case 'Patient Management':
                            echo "<a href='../modules/patient_management.php' class='btn btn-primary module-btn'>
                                   More Info &nbsp&nbsp&nbsp <i class='bi bi-arrow-right'></i> </a>";   
                            break;
                        case 'Appointment Scheduling':
                            echo "<a href='../modules/appointment_scheduling.php' class='btn btn-primary module-btn'>
                             More Info &nbsp&nbsp&nbsp <i class='bi bi-arrow-right'></i> </a>";
                            break;
                        case 'Billing Management':
                            echo "<a href='../modules/billing_management.php' class='btn btn-primary module-btn'>
                             More Info &nbsp&nbsp&nbsp <i class='bi bi-arrow-right'></i> </a>";
                            break;
                        case 'User Management':
                            echo "<a href='../modules/user_management.php' class='btn btn-primary module-btn'>
                             More Info &nbsp&nbsp&nbsp <i class='bi bi-arrow-right'></i> </a>";
                            break;
                        case 'Inventory Management':
                            echo "<a href='../modules/inventory_management.php' class='btn btn-primary module-btn'>
                             More Info &nbsp&nbsp&nbsp <i class='bi bi-arrow-right'></i> </a>";
                            break;
                        case 'Laboratory Management':
                            echo "<a href='../modules/laboratory_management.php' class='btn btn-primary module-btn'>
                             More Info &nbsp&nbsp&nbsp <i class='bi bi-arrow-right'></i> </a>";
                            break;
                    }

                    echo "</div>"; // Closing div for button placement
                    echo '</div>'; // Closing div for card-body
                    echo '</div>'; // Closing div for card
                    echo '</div>'; // Closing div for col-md-4
                    $colorIndex++; // Move to the next color
                }
            } else {
                echo '<div class="col-12"><div class="card bg-light-blue"><div class="card-body"><h5 class="card-title">No Modules Available</h5><p>Your role does not have any modules assigned to it.</p></div></div></div>';
            }
            ?>
        </div>
    </div>


<?php include '../includes/footer.php'; ?>