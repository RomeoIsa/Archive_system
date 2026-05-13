<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$activePage = 'settings';
$user_id = $_SESSION['user_id'];

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Default profile image
$profile_image = !empty($user['profile_image'])
    ? '../uploads/profiles/' . $user['profile_image']
    : '../assets/images/default-avatar.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assests/style.css">

</head>
<body>

<div class="d-flex">

    <!-- Sidebar -->
    <?php include '../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-grow-1 settings-container">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Profile Settings</h2>
                <p class="text-muted mb-0">Manage your profile and account preferences</p>
            </div>
        </div>

        <form id="settingsForm" action="update_settings.php" method="POST" enctype="multipart/form-data">



            <!-- Profile Card -->
            <div class="card settings-card text-center">
                <div class="card-body">

                    <div class="profile-image-wrapper mb-3" data-bs-toggle="modal" data-bs-target="#profilePhotoModal">
                        <img src="<?= $profile_image ?>" class="profile-image rounded-circle mb-4"
                         width="120"
                     height="120"
                         alt="Profile Image">

                        <div class="profile-overlay">
                            <div>
                                <i class="bi bi-camera-fill d-block mb-1"></i>
                                Change
                            </div>
                        </div>
                    </div>

                    <h4 class="fw-semibold mb-1">
                        <?= htmlspecialchars($user['name']) ?>
                    </h4>

                    <p class="text-muted mb-0 text-capitalize">
                        <?= htmlspecialchars($user['role']) ?>
                    </p>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="card settings-card">
                <div class="card-header">
                    <i class="bi bi-person-lines-fill me-2"></i>
                    Personal Information
                </div>

                <div class="card-body">
                    <div class="row g-4">

                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control"
                                value="<?= htmlspecialchars($user['name']) ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Matric Number</label>
                            <input type="text" name="matric_number" class="form-control"
                                value="<?= htmlspecialchars($user['matric_number'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <input type="text" name="department" class="form-control"
                                value="<?= htmlspecialchars($user['department'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Level</label>

                            <select name="level" class="form-select">
                                <option value="">Select Level</option>
                                <option value="100L" <?= ($user['level'] ?? '') == '100L' ? 'selected' : '' ?>>100L</option>
                                <option value="200L" <?= ($user['level'] ?? '') == '200L' ? 'selected' : '' ?>>200L</option>
                                <option value="300L" <?= ($user['level'] ?? '') == '300L' ? 'selected' : '' ?>>300L</option>
                                <option value="400L" <?= ($user['level'] ?? '') == '400L' ? 'selected' : '' ?>>400L</option>
                                <option value="500L" <?= ($user['level'] ?? '') == '500L' ? 'selected' : '' ?>>500L</option>
                            </select>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Preferences -->
            <div class="card settings-card">
                <div class="card-header">
                    <i class="bi bi-sliders me-2"></i>
                    Account Preferences
                </div>

                <div class="card-body">
                    <div class="row g-4">

                        <div class="col-md-6">
                            <label class="form-label">Theme</label>

                            <select name="theme" class="form-select">
                                <option value="light" <?= ($user['theme'] ?? 'light') == 'light' ? 'selected' : '' ?>>Light Mode</option>
                                <option value="dark" <?= ($user['theme'] ?? '') == 'dark' ? 'selected' : '' ?>>Dark Mode</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Language</label>

                            <select name="language" class="form-select">
                                <option value="english">English</option>
                                <option value="french">French</option>
                            </select>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="d-flex justify-content-end mb-4">
                <button type="submit" class="btn btn-dark btn-save">
                    <i class="bi bi-check-circle me-2"></i>
                    Save Changes
                </button>
            </div>

        </form>

        <!-- Danger Zone -->
        <div class="danger-zone">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h5 class="fw-bold text-danger mb-1">Delete Account</h5>
                    <p class="text-muted mb-0">
                        Permanently delete your account and all associated data.
                    </p>
                </div>

                <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                    <i class="bi bi-trash3 me-2"></i>
                    Delete Account
                </button>
            </div>
        </div>

    </div>
</div>

<!-- Profile Photo Modal -->
<div class="modal fade" id="profilePhotoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">

            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Profile Photo</h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center p-4">

                <img src="<?= $profile_image ?>"
                     class="rounded-circle mb-4"
                     width="120"
                     height="120"
                     style="object-fit: cover;">

                <form action="upload_profile_image.php" method="POST" enctype="multipart/form-data">

                    <input type="file"
                           name="profile_image"
                           class="form-control mb-3"
                           accept="image/*"
                           required>

                    <button type="submit" class="btn btn-dark w-100 mb-2">
                        <i class="bi bi-upload me-2"></i>
                        Upload New Photo
                    </button>
                </form>

                <?php if (!empty($user['profile_image'])): ?>
                    <a href="remove_profile_image.php"
                       class="btn btn-outline-danger w-100">
                        <i class="bi bi-trash me-2"></i>
                        Remove Photo
                    </a>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">

            <div class="modal-header border-0">
                <h5 class="modal-title text-danger fw-bold">
                    Delete Account
                </h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p>
                    This action cannot be undone. All your uploads,
                    saved files, and activity data may be permanently removed.
                </p>
            </div>

            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    Cancel
                </button>

                <a href="delete_account.php" class="btn btn-danger">
                    Yes, Delete Account
                </a>
            </div>

        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>