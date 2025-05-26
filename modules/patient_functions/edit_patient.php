<?php
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../config/db.php';

$original_nic = $_GET['nic'] ?? '';

$errors = [];
$message = '';
$patient = null;

// Fetch patient data
if ($original_nic) {
    $stmt = $conn->prepare("SELECT NIC, Fullname, Address, Contact, Gender, Email, Username, Password, profile_picture FROM patients WHERE NIC = ?");
    $stmt->bind_param('s', $original_nic);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $patient = $result->fetch_assoc();
    } else {
        $message = "Patient not found!";
    }
    $stmt->close();
} else {
    $message = "NIC not specified!";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect input safely
    $nic = trim($_POST['nic'] ?? '');
    $fullname = trim($_POST['fullname'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // === VALIDATIONS ===

    // NIC: required, max 15, unique except current
    if ($nic === '') {
        $errors['nic'] = "NIC is required.";
    } elseif (strlen($nic) > 15) {
        $errors['nic'] = "NIC must be at most 15 characters.";
    } else {
        $stmt = $conn->prepare("SELECT NIC FROM patients WHERE NIC = ? AND NIC != ?");
        $stmt->bind_param('ss', $nic, $original_nic);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors['nic'] = "NIC already exists.";
        }
        $stmt->close();
    }

    // Fullname: required, max 100
    if ($fullname === '') {
        $errors['fullname'] = "Full name is required.";
    } elseif (strlen($fullname) > 100) {
        $errors['fullname'] = "Full name must be less than 100 characters.";
    }

    // Address: required, max 200
    if ($address === '') {
        $errors['address'] = "Address is required.";
    } elseif (strlen($address) > 200) {
        $errors['address'] = "Address must be less than 200 characters.";
    }

    // Contact: required
    if ($contact === '') {
        $errors['contact'] = "Contact is required.";
    }

    // Gender: required
    if ($gender === '') {
        $errors['gender'] = "Gender is required.";
    }

    // Email: required, valid, unique except current
    if ($email === '') {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Valid email is required.";
    } else {
        $stmt = $conn->prepare("SELECT Email FROM patients WHERE Email = ? AND NIC != ?");
        $stmt->bind_param('ss', $email, $original_nic);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors['email'] = "Email already exists.";
        }
        $stmt->close();
    }

    // Username: required, min 6, unique except current
    if ($username === '') {
        $errors['username'] = "Username is required.";
    } elseif (strlen($username) < 6) {
        $errors['username'] = "Username must be at least 6 characters.";
    } else {
        $stmt = $conn->prepare("SELECT Username FROM patients WHERE Username = ? AND NIC != ?");
        $stmt->bind_param('ss', $username, $original_nic);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors['username'] = "Username already exists.";
        }
        $stmt->close();
    }

    // Password: required, min 6, max 15
    if ($password === '') {
        $errors['password'] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters.";
    } elseif (strlen($password) > 15) {
        $errors['password'] = "Password must be at most 15 characters.";
    }



    // Contact: required, exactly 10 digits
if ($contact === '') {
    $errors['contact'] = "Contact is required.";
} elseif (!preg_match('/^\d{10}$/', $contact)) {
    $errors['contact'] = "Contact must be exactly 10 digits.";
}


    // === END VALIDATIONS ===

    if (empty($errors)) {
        // Update patient
        $sql = "UPDATE patients SET NIC=?, Fullname=?, Address=?, Contact=?, Gender=?, Email=?, Username=?, Password=?";
        $params = [$nic, $fullname, $address, $contact, $gender, $email, $username, $password];
        $types = "ssssssss";

        // Handle profile picture if uploaded
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
            $imgData = file_get_contents($_FILES['profile_picture']['tmp_name']);
            $sql .= ", profile_picture=?";
            $params[] = $imgData;
            $types .= "s";
        }

        $sql .= " WHERE NIC=?";
        $params[] = $original_nic;
        $types .= "s";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                    setTimeout(function() {
                        window.location.href = '../patient_management.php';
                    }, 2000);
                });
            </script>";
        } else {
            $message = "Error updating record: " . $conn->error;
        }

        $stmt->close();
    }
}
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">Edit Patient Details</h2>

        <?php if ($message): ?>
            <div class="alert alert-warning"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if ($patient): ?>
            <form method="POST" enctype="multipart/form-data" novalidate>
                <div class="mb-3 text-center">
                    <?php if (!empty($patient['profile_picture'])): ?>
                        <img src="data:image/jpeg;base64,<?= base64_encode($patient['profile_picture']) ?>" class="rounded-circle" width="150" height="150" alt="Profile Picture">
                    <?php else: ?>
                        <img src="../../assets/default-avatar.png" class="rounded-circle" width="150" height="150" alt="Default Avatar">
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="profile_picture" class="form-label">Upload New Profile Picture</label>
                    <input
                        type="file"
                        name="profile_picture"
                        id="profile_picture"
                        class="form-control"
                        accept="image/*"
                    >
                    <div class="form-text">File Types Allowed: JPEG, JPG, PNG.</div>
                </div>

                <div class="mb-3">
                    <label for="nic" class="form-label">NIC</label>
                    <input
                        type="text"
                        name="nic"
                        id="nic"
                        class="form-control <?= !empty($errors['nic']) ? 'border-danger' : '' ?>"
                        value="<?= htmlspecialchars($_POST['nic'] ?? $patient['NIC']) ?>"
                        required
                        minlength="10"
                        maxlength="12"
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
                        value="<?= htmlspecialchars($_POST['fullname'] ?? $patient['Fullname']) ?>"
                        required
                        maxlength="150"
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
                        value="<?= htmlspecialchars($_POST['address'] ?? $patient['Address']) ?>"
                        required
                        maxlength="250"
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
        value="<?= htmlspecialchars($_POST['contact'] ?? $patient['Contact']) ?>"
        required
        minlength="10"
        maxlength="10"
        pattern="\d{10}"
        title="Contact must be exactly 10 digits"
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
                        class="form-select <?= !empty($errors['gender']) ? 'border-danger' : '' ?>"
                        required
                    >
                        <option value="" <?= (($_POST['gender'] ?? $patient['Gender']) === '') ? 'selected' : '' ?>>Select Gender</option>
                        <option value="Male" <?= (($_POST['gender'] ?? $patient['Gender']) === 'Male') ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= (($_POST['gender'] ?? $patient['Gender']) === 'Female') ? 'selected' : '' ?>>Female</option>
                        <option value="Other" <?= (($_POST['gender'] ?? $patient['Gender']) === 'Other') ? 'selected' : '' ?>>Other</option>
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
                        value="<?= htmlspecialchars($_POST['email'] ?? $patient['Email']) ?>"
                        required
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
                        value="<?= htmlspecialchars($_POST['username'] ?? $patient['Username']) ?>"
                        required
                        minlength="6"
                        maxlength="15"
                    >
                    <?php if (!empty($errors['username'])): ?>
                        <div class="text-danger mt-1"><?= $errors['username'] ?></div>
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
                        value="<?= htmlspecialchars($_POST['password'] ?? $patient['Password']) ?>"
                    >
                    <?php if (!empty($errors['password'])): ?>
                        <div class="text-danger mt-1"><?= $errors['password'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="../patient_management.php" class="btn btn-danger">
                        <i class="bi bi-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Save Changes
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body text-center text-success">
        Patient updated successfully!
      </div>
    </div>
  </div>
</div>

<?php include '../../includes/footer.php'; ?>
