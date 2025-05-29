<?php
include '../../includes/header.php';
include '../../config/db.php';

$errors = [];
$formData = [
    'nic' => '',
    'usercode' => '',
    'fullname' => '',
    'address' => '',
    'contact' => '',
    'gender' => '',
    'email' => '',
    'username' => '',
    'role' => '',
    'password' => ''
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($formData as $field => &$value) {
        $value = trim($_POST[$field] ?? '');
    }

    // Validate NIC
    if (strlen($formData['nic']) < 10 || strlen($formData['nic']) > 12) {
        $errors['nic'] = "NIC must be between 10 and 15 characters.";
    }

    // Validate usercode
    if (strlen($formData['usercode']) > 6) {
        $errors['usercode'] = "User code must not exceed 6 characters.";
    }

    // Validate contact number (10 digits only)
    if (!preg_match('/^\d{10}$/', $formData['contact'])) {
        $errors['contact'] = "Contact number must be exactly 10 digits.";
    }

    // Validate email format
    if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address.";
    }

    // Validate username
    if (strlen($formData['username']) < 6 || strlen($formData['username']) > 15) {
        $errors['username'] = "Username must be between 6 and 15 characters.";
    }

    // Validate password
    if (strlen($formData['password']) < 6 || strlen($formData['password']) > 15) {
        $errors['password'] = "Password must be between 6 and 15 characters.";
    }

    // Check for existing NIC, username, or email
    $stmt = $conn->prepare("SELECT * FROM users WHERE NIC = ? OR Username = ? OR Email = ?");
    $stmt->bind_param("sss", $formData['nic'], $formData['username'], $formData['email']);
    $stmt->execute();
    $existing = $stmt->get_result();
    if ($existing->num_rows > 0) {
        while ($row = $existing->fetch_assoc()) {
            if ($row['NIC'] === $formData['nic']) {
                $errors['nic'] = "NIC already exists.";
            }
            if ($row['Username'] === $formData['username']) {
                $errors['username'] = "Username already exists.";
            }
            if ($row['Email'] === $formData['email']) {
                $errors['email'] = "Email already exists.";
            }
        }
    }
    $stmt->close();

    // Handle profile picture
    $profilePic = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $profilePic = file_get_contents($_FILES['profile_picture']['tmp_name']);
    }

    // If no errors, insert into DB
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO users (NIC, user_code, Fullname, Address, Contact, Gender, Email, Username, Role, Password, profile_picture) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssss", $formData['nic'], $formData['usercode'], $formData['fullname'], $formData['address'], $formData['contact'], $formData['gender'], $formData['email'], $formData['username'], $formData['role'], $formData['password'], $profilePic);

        if ($stmt->execute()) {
            $message = "User added successfully!";
            header("refresh:2; url=../user_management.php");
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">Add New User</h2>

        <!-- Display success message -->
        <?php if (isset($message)) { ?>
            <div class="alert alert-info">
                <?php echo $message; ?>
            </div>
        <?php } ?>

        <!-- User Add Form -->
        <form method="POST" action="add_user.php" enctype="multipart/form-data">
            <?php
            function fieldClass($field, $errors) {
                return isset($errors[$field]) ? 'is-invalid' : '';
            }
            function fieldError($field, $errors) {
                return isset($errors[$field]) ? "<div class='invalid-feedback'>{$errors[$field]}</div>" : '';
            }
            ?>

            <div class="mb-3">
                <label for="nic" class="form-label">NIC</label>
                <input type="text" name="nic" id="nic" class="form-control <?= fieldClass('nic', $errors) ?>" value="<?= htmlspecialchars($formData['nic']) ?>" required maxlength="12">
                <?= fieldError('nic', $errors) ?>
            </div>
            <div class="mb-3">
                <label for="usercode" class="form-label">User Code</label>
                <input type="text" name="usercode" id="usercode" class="form-control <?= fieldClass('usercode', $errors) ?>" value="<?= htmlspecialchars($formData['usercode']) ?>" required maxlength="6">
                <?= fieldError('usercode', $errors) ?>
            </div>
            <div class="mb-3">
                <label for="fullname" class="form-label">Full Name</label>
                <input type="text" name="fullname" id="fullname" class="form-control" value="<?= htmlspecialchars($formData['fullname']) ?>" required maxlength="150">
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" name="address" id="address" class="form-control" value="<?= htmlspecialchars($formData['address']) ?>" required maxlength="250">
            </div>
            <div class="mb-3">
                <label for="contact" class="form-label">Contact</label>
                <input type="text" name="contact" id="contact" class="form-control <?= fieldClass('contact', $errors) ?>" value="<?= htmlspecialchars($formData['contact']) ?>" required  maxlength="10">
                <?= fieldError('contact', $errors) ?>
            </div>
            <div class="mb-3">
                <label for="gender" class="form-label">Gender</label>
                <select name="gender" id="gender" class="form-control" required>
                    <option value="Male" <?= $formData['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= $formData['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                    <option value="Other" <?= $formData['gender'] === 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select name="role" id="role" class="form-control" required>
                    <option value="Admin" <?= $formData['role'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="Dentist" <?= $formData['role'] === 'Dentist' ? 'selected' : '' ?>>Dentist</option>
                    <option value="Receptionist" <?= $formData['role'] === 'Receptionist' ? 'selected' : '' ?>>Receptionist</option>
                    <option value="Lab_Technician" <?= $formData['role'] === 'Lab_Technician' ? 'selected' : '' ?>>Lab Technician</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control <?= fieldClass('email', $errors) ?>" value="<?= htmlspecialchars($formData['email']) ?>" required>
                <?= fieldError('email', $errors) ?>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control <?= fieldClass('username', $errors) ?>" value="<?= htmlspecialchars($formData['username']) ?>" required minlength="6" maxlength="15">
                <?= fieldError('username', $errors) ?>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control <?= fieldClass('password', $errors) ?>" required minlength="6" maxlength="15">
                <?= fieldError('password', $errors) ?>
            </div>
            <div class="mb-3">
                <label for="profile_picture" class="form-label">Profile Picture</label>
                <input type="file" name="profile_picture" id="profile_picture" class="form-control" accept="image/*">
                <div class="form-text">File Types Allowed: JPEG, JPG, PNG.</div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="../user_management.php" class="btn btn-danger"><i class="bi bi-arrow-left"></i> Back</a>
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Add User</button>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
