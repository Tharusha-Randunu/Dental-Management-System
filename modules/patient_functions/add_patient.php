<?php
include '../../includes/header.php';
include '../../config/db.php';

$errors = [
    'nic' => '',
    'fullname' => '',
    'address' => '',
    'contact' => '',
    'gender' => '',
    'email' => '',
    'username' => '',
    'password' => '',
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nic = trim($_POST['nic']);
    $fullname = trim($_POST['fullname']);
    $address = trim($_POST['address']);
    $contact = trim($_POST['contact']);
    $gender = trim($_POST['gender']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $profilePic = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $profilePic = file_get_contents($_FILES['profile_picture']['tmp_name']);
    }

    // Validation
    if (empty($nic)) {
        $errors['nic'] = 'NIC is required.';
    } elseif (strlen($nic) < 10 || strlen($nic) > 12) {
        $errors['nic'] = 'NIC must be between 10 and 15 characters.';
    }

    if (empty($fullname)) {
        $errors['fullname'] = 'Full Name is required.';
    } elseif (strlen($fullname) > 150) {
        $errors['fullname'] = 'Full Name must not exceed 150 characters.';
    }

    if (empty($address)) {
        $errors['address'] = 'Address is required.';
    } elseif (strlen($address) > 250) {
        $errors['address'] = 'Address must not exceed 250 characters.';
    }

    if (empty($contact)) {
        $errors['contact'] = 'Contact is required.';
    } elseif (!preg_match('/^\d{10}$/', $contact)) {
        $errors['contact'] = 'Contact must be exactly 10 digits.';
    }

    if (empty($gender)) {
        $errors['gender'] = 'Gender is required.';
    }

    if (empty($email)) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    if (empty($username)) {
        $errors['username'] = 'Username is required.';
    } elseif (strlen($username) < 6 || strlen($username) > 15) {
        $errors['username'] = 'Username must be between 6 and 15 characters.';
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required.';
    } elseif (strlen($password) < 6 || strlen($password) > 15) {
        $errors['password'] = 'Password must be between 6 and 15 characters.';
    }

    // Check if there are no errors before inserting
    $hasErrors = false;
    foreach ($errors as $error) {
        if (!empty($error)) {
            $hasErrors = true;
            break;
        }
    }




    // Check if NIC, Username, or Email already exists
$checkStmt = $conn->prepare("SELECT NIC, Username, Email FROM patients WHERE NIC = ? OR Username = ? OR Email = ?");
$checkStmt->bind_param("sss", $nic, $username, $email);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($row['NIC'] === $nic) {
            $errors['nic'] = 'NIC already exists.';
        }
        if ($row['Username'] === $username) {
            $errors['username'] = 'Username already exists.';
        }
        if ($row['Email'] === $email) {
            $errors['email'] = 'Email already exists.';
        }
    }
    $hasErrors = true;
}
$checkStmt->close();




    if (!$hasErrors) {

        
        $stmt = $conn->prepare("INSERT INTO patients (NIC, Fullname, Address, Contact, Gender, Email, Username, Password, profile_picture)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $nic, $fullname, $address, $contact, $gender, $email, $username, $password, $profilePic);

        if ($stmt->execute()) {
             $message = "Patient added successfully.";
             echo "<script>
            setTimeout(function() {
                window.location.href = '../patient_management.php';
            }, 3000);
          </script>";
        } else {
            $errors['general'] = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">Add New Patient</h2>

        <!--  MESSAGE DISPLAY -->
        <?php if (isset($message)): ?>
            <div class="alert alert-info">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($errors['general']) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="add_patient.php" enctype="multipart/form-data" id="patientForm" novalidate>
            <div class="mb-3">
                <label for="nic" class="form-label">NIC</label>
                <input
                    type="text"
                    name="nic"
                    id="nic"
                    class="form-control <?= !empty($errors['nic']) ? 'border-danger' : '' ?>"
                    required
                    minlength="10"
                    maxlength="12"
                    value="<?= isset($nic) ? htmlspecialchars($nic) : '' ?>"
                >
                <?php if (!empty($errors['nic'])): ?>
                    <div class="text-danger mt-1"><?= $errors['nic'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="fullname" class="form-label">Full Name</label>
                <input
                    type="text"
                    name="fullname"
                    id="fullname"
                    class="form-control <?= !empty($errors['fullname']) ? 'border-danger' : '' ?>"
                    required
                    maxlength="150"
                    value="<?= isset($fullname) ? htmlspecialchars($fullname) : '' ?>"
                >
                <?php if (!empty($errors['fullname'])): ?>
                    <div class="text-danger mt-1"><?= $errors['fullname'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input
                    type="text"
                    name="address"
                    id="address"
                    class="form-control <?= !empty($errors['address']) ? 'border-danger' : '' ?>"
                    required
                    maxlength="250"
                    value="<?= isset($address) ? htmlspecialchars($address) : '' ?>"
                >
                <?php if (!empty($errors['address'])): ?>
                    <div class="text-danger mt-1"><?= $errors['address'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="contact" class="form-label">Contact</label>
                <input
                    type="text"
                    name="contact"
                    id="contact"
                    class="form-control <?= !empty($errors['contact']) ? 'border-danger' : '' ?>"
                    required
                    minlength="10"
                    maxlength="10"
                    pattern="\d{10}"
                    title="Contact number must be exactly 10 digits."
                    value="<?= isset($contact) ? htmlspecialchars($contact) : '' ?>"
                >
                <?php if (!empty($errors['contact'])): ?>
                    <div class="text-danger mt-1"><?= $errors['contact'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="gender" class="form-label">Gender</label>
                <select
                    name="gender"
                    id="gender"
                    class="form-control <?= !empty($errors['gender']) ? 'border-danger' : '' ?>"
                    required
                >
                    <option value="">Select Gender</option>
                    <option value="Male" <?= (isset($gender) && $gender === 'Male') ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= (isset($gender) && $gender === 'Female') ? 'selected' : '' ?>>Female</option>
                    <option value="Other" <?= (isset($gender) && $gender === 'Other') ? 'selected' : '' ?>>Other</option>
                </select>
                <?php if (!empty($errors['gender'])): ?>
                    <div class="text-danger mt-1"><?= $errors['gender'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    class="form-control <?= !empty($errors['email']) ? 'border-danger' : '' ?>"
                    required
                    value="<?= isset($email) ? htmlspecialchars($email) : '' ?>"
                >
                <?php if (!empty($errors['email'])): ?>
                    <div class="text-danger mt-1"><?= $errors['email'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input
                    type="text"
                    name="username"
                    id="username"
                    class="form-control <?= !empty($errors['username']) ? 'border-danger' : '' ?>"
                    required minlength="6" maxlength="15"
                    autocomplete="new-username"
                    value=""
                >
                <?php if (!empty($errors['username'])): ?>
                    <div class="text-danger"><?= $errors['username'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-control <?= !empty($errors['password']) ? 'border-danger' : '' ?>"
                    required
                    minlength="6"
                    maxlength="15"
                >
                <?php if (!empty($errors['password'])): ?>
                    <div class="text-danger mt-1"><?= $errors['password'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="profile_picture" class="form-label">Profile Picture</label>
                <input
                    type="file"
                    name="profile_picture"
                    id="profile_picture"
                    class="form-control"
                    accept="image/*"
                >
                <div class="form-text">File Types Allowed: JPEG, JPG, PNG.</div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="../patient_management.php" class="btn btn-danger"><i class="bi bi-arrow-left"></i> Back</a>
                <button type="submit" class="btn btn-success ms-2"><i class="bi bi-check-lg"></i> Add Patient</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('patientForm').addEventListener('submit', function (e) {
        const form = e.target;
        const inputs = form.querySelectorAll('input, select');
        let valid = true;

        inputs.forEach(input => {
            input.classList.remove('border-danger');
            const errorDiv = input.nextElementSibling;
            if (errorDiv && errorDiv.classList.contains('text-danger')) {
                errorDiv.style.display = 'none';
            }

            if (!input.checkValidity()) {
                input.classList.add('border-danger');
                valid = false;

                // Show custom messages under fields on client side as fallback
                if (!errorDiv || !errorDiv.classList.contains('text-danger')) {
                    const errMsg = document.createElement('div');
                    errMsg.classList.add('text-danger', 'mt-1');
                    errMsg.textContent = input.validationMessage;
                    input.parentNode.appendChild(errMsg);
                } else {
                    errorDiv.style.display = 'block';
                }
            }
        });

        if (!valid) {
            e.preventDefault();
        }
    });
</script>

<?php include '../../includes/footer.php'; ?>
