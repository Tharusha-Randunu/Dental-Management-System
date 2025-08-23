<?php
session_start();
include '../../includes/header.php';
include '../../config/db.php';

// Check user logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../../auth/login.php");
    exit;
}

$username = $_SESSION['username'];

// Fetch user details by username
$sql = "SELECT NIC, user_code, Fullname, Role, Address, Contact, Gender, Email, Username, Password, Profile_Picture 
        FROM users WHERE Username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $message = "User not found!";
} else {
    $user = $result->fetch_assoc();
}

$errors = [
    'address' => '',
    'contact' => '',
    'email' => '',
    'username' => '',
    'password' => '',
];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $address = trim($_POST['address']);
    $contact = trim($_POST['contact']);
    $email = trim($_POST['email']);
    $new_username = trim($_POST['username']);
    $password = trim($_POST['password']);  

    // Validation flags
    $valid = true;

    // Address max 250 chars
    if (empty($address)) {
        $errors['address'] = "Address is required.";
        $valid = false;
    } elseif (strlen($address) > 250) {
        $errors['address'] = "Address cannot exceed 250 characters.";
        $valid = false;
    }

    // Contact exactly 10 digits
    if (empty($contact)) {
        $errors['contact'] = "Contact is required.";
        $valid = false;
    } elseif (!preg_match('/^\d{10}$/', $contact)) {
        $errors['contact'] = "Contact must be exactly 10 digits.";
        $valid = false;
    }

    // Email validation
    if (empty($email)) {
        $errors['email'] = "Email is required.";
        $valid = false;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
        $valid = false;
    } else {
        // Check uniqueness  
        $sql = "SELECT Username FROM users WHERE Email = ? AND Username != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors['email'] = "Email is already in use.";
            $valid = false;
        }
        $stmt->close();
    }

    // Username validation (6-15 chars)
    if (empty($new_username)) {
        $errors['username'] = "Username is required.";
        $valid = false;
    } elseif (strlen($new_username) < 6 || strlen($new_username) > 15) {
        $errors['username'] = "Username must be 6 to 15 characters.";
        $valid = false;
    } else {
        // Check uniqueness  
        $sql = "SELECT Username FROM users WHERE Username = ? AND Username != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $new_username, $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors['username'] = "Username is already taken.";
            $valid = false;
        }
        $stmt->close();
    }

    // Password validation (6-15 chars)
    if (empty($password)) {
        $errors['password'] = "Password is required.";
        $valid = false;
    } elseif (strlen($password) < 6 || strlen($password) > 15) {
        $errors['password'] = "Password must be 6 to 15 characters.";
        $valid = false;
    }

    // If valid, update record
    if ($valid) {
        $update_sql = "UPDATE users SET Address=?, Contact=?, Email=?, Username=?, Password=?";
        $types = "sssss";
        $params = [$address, $contact, $email, $new_username, $password];

        // Handle profile picture upload
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            // Validate file type
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
            if (in_array($_FILES['profile_picture']['type'], $allowed_types)) {
                $imageData = file_get_contents($_FILES['profile_picture']['tmp_name']);
                $update_sql .= ", Profile_Picture=?";
                $types .= "s";
                $params[] = $imageData;
            } else {
                $errors['profile_picture'] = "Invalid file type for profile picture.";
                $valid = false;
            }
        }

        $update_sql .= " WHERE Username=?";
        $types .= "s";
        $params[] = $username;

        if ($valid) {
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param($types, ...$params);
            if ($stmt->execute()) {
                // Update session username if changed
                if ($new_username !== $username) {
                    $_SESSION['username'] = $new_username;
                    $username = $new_username;
                }
                // Refresh user data from DB to show updated info
                $sql = "SELECT NIC, user_code, Fullname, Role, Address, Contact, Gender, Email, Username, Password, Profile_Picture 
                        FROM users WHERE Username = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();

                echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                            successModal.show();
                        });
                      </script>";
            } else {
                echo "<script>alert('Error updating record: " . $conn->error . "');</script>";
            }
            $stmt->close();
        }
    }
}
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">Edit Your Profile</h2>

        <?php if (isset($message)) { ?>
            <div class="alert alert-warning"><?php echo $message; ?></div>
        <?php } ?>

        <?php if (isset($user)) { ?>
            <form method="POST" enctype="multipart/form-data" novalidate>
                <div class="mb-3 text-center">
                    <?php if (!empty($user['Profile_Picture'])): ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($user['Profile_Picture']); ?>" alt="Profile Picture" class="rounded-circle" width="150" height="150">
                    <?php else: ?>
                        <img src="../../assets/default-avatar.png" alt="No Image" class="rounded-circle" width="150" height="150">
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label class="form-label">Upload New Profile Picture</label>
                    <input type="file" name="profile_picture" class="form-control" accept=".jpeg,.jpg,.png">
                    <?php if (isset($errors['profile_picture']) && $errors['profile_picture']): ?>
                        <div class="text-danger small"><?php echo $errors['profile_picture']; ?></div>
                    <?php endif; ?>
                    <div class="form-text">File Types Allowed: JPEG, JPG, PNG.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">NIC</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['NIC']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">User Code</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['user_code']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['Fullname']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['Role']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Gender</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['Gender']); ?>" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control <?php echo $errors['address'] ? 'is-invalid' : ''; ?>" maxlength="250" required value="<?php echo htmlspecialchars($user['Address'] ?? ''); ?>">
                    <?php if ($errors['address']): ?>
                        <div class="invalid-feedback"><?php echo $errors['address']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contact</label>
                    <input type="text" name="contact" class="form-control <?php echo $errors['contact'] ? 'is-invalid' : ''; ?>" maxlength="10" required value="<?php echo htmlspecialchars($user['Contact'] ?? ''); ?>">
                    <?php if ($errors['contact']): ?>
                        <div class="invalid-feedback"><?php echo $errors['contact']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control <?php echo $errors['email'] ? 'is-invalid' : ''; ?>" maxlength="50" required value="<?php echo htmlspecialchars($user['Email'] ?? ''); ?>">
                    <?php if ($errors['email']): ?>
                        <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control <?php echo $errors['username'] ? 'is-invalid' : ''; ?>" minlength="6" maxlength="15" required value="<?php echo htmlspecialchars($user['Username'] ?? ''); ?>">
                    <?php if ($errors['username']): ?>
                        <div class="invalid-feedback"><?php echo $errors['username']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control <?php echo $errors['password'] ? 'is-invalid' : ''; ?>" minlength="6" maxlength="15" required value="<?php echo htmlspecialchars($user['Password'] ?? ''); ?>">
                    <?php if ($errors['password']): ?>
                        <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="/Dental_System/views/dashboard.php" class="btn btn-danger"><i class="bi bi-arrow-left"></i> Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save Changes</button>
                </div>
            </form>
        <?php } ?>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Success</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Changes have been successfully saved!
            </div>
            <div class="modal-footer">
                <a href="../../views/dashboard.php" class="btn btn-primary">OK</a>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
