<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require "../config/db.php";

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'student';
$name = $_SESSION['name'] ?? 'User';

$themeClass = ($role === 'staff') ? 'theme-staff' : 'theme-student';

$activePage = 'dashboard';

$stats = [];

/* -----------------------
   STUDENT STATS
------------------------*/
if ($role === 'student') {

    $uploads = $conn->query("
        SELECT COUNT(*) as total
        FROM uploads
        WHERE user_id = $user_id
    ")->fetch_assoc()['total'] ?? 0;

    $downloads = 0;
    $check = $conn->query("SHOW TABLES LIKE 'downloads'");
    if ($check && $check->num_rows > 0) {
        $downloads = $conn->query("
            SELECT COUNT(*) as total
            FROM downloads
            WHERE user_id = $user_id
        ")->fetch_assoc()['total'] ?? 0;
    }

    $saved = 0;
    $checkSaved = $conn->query("SHOW TABLES LIKE 'saved'");
    if ($checkSaved && $checkSaved->num_rows > 0) {
        $saved = $conn->query("
            SELECT COUNT(*) as total
            FROM saved
            WHERE user_id = $user_id
        ")->fetch_assoc()['total'] ?? 0;
    }

    $stats = [
        ['label' => '<i class="bi bi-cloud-upload"></i> Uploads', 'value' => $uploads],
        ['label' => '<i class="bi bi-cloud-download"></i> Downloads', 'value' => $downloads],
        ['label' => '<i class="bi bi-bookmark-fill"></i> Saved', 'value' => $saved],
    ];
}

/* -----------------------
   STAFF STATS
------------------------*/
if ($role === 'staff') {

    $uploads = $conn->query("
        SELECT COUNT(*) as total FROM uploads
    ")->fetch_assoc()['total'] ?? 0;

    $downloads = 0;
    $check = $conn->query("SHOW TABLES LIKE 'downloads'");
    if ($check && $check->num_rows > 0) {
        $downloads = $conn->query("
            SELECT COUNT(*) as total FROM downloads
        ")->fetch_assoc()['total'] ?? 0;
    }

    $users = $conn->query("
        SELECT COUNT(*) as total FROM users
    ")->fetch_assoc()['total'] ?? 0;

    $stats = [
        ['label' => 'Uploads', 'value' => $uploads],
        ['label' => 'Downloads', 'value' => $downloads],
        ['label' => 'Users', 'value' => $users],
    ];
}

/* -----------------------
   RECENTLY VIEWED
------------------------*/
$recentStmt = $conn->prepare("
    SELECT uploads.id,
           uploads.title,
           uploads.file_type,
           recent_views.viewed_at
    FROM recent_views
    JOIN uploads ON recent_views.upload_id = uploads.id
    WHERE recent_views.user_id = ?
    ORDER BY recent_views.viewed_at DESC
    LIMIT 5
");

$recentStmt->bind_param("i", $user_id);
$recentStmt->execute();
$recentFiles = $recentStmt->get_result();

/* -----------------------
   RECENTLY UPLOADED
------------------------*/
$recentUploadsStmt = $conn->prepare("
    SELECT uploads.id,
           uploads.title,
           uploads.file_type,
           uploads.created_at,
           users.name AS uploader_name
    FROM uploads
    JOIN users ON uploads.user_id = users.id
    ORDER BY uploads.created_at DESC
    LIMIT 5
");

$recentUploadsStmt->execute();
$recentUploads = $recentUploadsStmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assests/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="<?= $themeClass; ?>">

<div class="d-flex">

    <?php include "../includes/sidebar.php"; ?>

    <div class="main-content flex-grow-1 p-4">

        <!-- TOP BAR -->
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div></div>

            <div class="d-flex align-items-center gap-3">

                <strong><?= htmlspecialchars($name); ?></strong>

                <img src="https://via.placeholder.com/35" class="rounded-circle">
            </div>

        </div>

        <!-- WELCOME -->
        <div class="d-flex justify-content-between align-items-center">

            <div>
                <h4>Welcome back, <?= htmlspecialchars($name); ?> 👋</h4>
                <p class="text-muted">Find, learn and share academic resources.</p>
            </div>

            <button class="btn btn-primary" onclick="openModal()">
                + Upload Material
            </button>

        </div>

        <!-- STATS -->
        <div class="row mt-4">

            <?php foreach ($stats as $stat): ?>
                <div class="col-md-4">
                    <div class="card stat-card p-3 shadow-sm border-0">
                        <h6><?= $stat['label']; ?></h6>
                        <h3><?= $stat['value']; ?></h3>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>

        <!-- RECENT SECTION -->
        <div class="row mt-4">

            <!-- RECENTLY VIEWED -->
            <div class="col-md-6">
                <div class="card p-3 shadow-sm border-0">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">
                            <i class="bi bi-clock-history"></i>
                            Recently Viewed
                        </h6>
                    </div>

                    <ul class="list-group list-group-flush">

                        <?php if ($recentFiles->num_rows === 0): ?>
                            <li class="list-group-item text-muted">
                                No recent views yet
                            </li>
                        <?php endif; ?>

                        <?php while ($row = $recentFiles->fetch_assoc()): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">

                                <div>
                                    <div class="fw-semibold">
                                        <?= htmlspecialchars($row['title']) ?>
                                    </div>
                                    <small class="text-muted">
                                        <?= strtoupper($row['file_type']) ?>
                                    </small>
                                </div>

                                <small class="text-muted">
                                    <?= date("M j, H:i", strtotime($row['viewed_at'])) ?>
                                </small>

                            </li>
                        <?php endwhile; ?>

                    </ul>

                </div>
            </div>

            <!-- RECENTLY UPLOADED -->
            <div class="col-md-6">
                <div class="card p-3 shadow-sm border-0">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">
                            <i class="bi bi-cloud-upload"></i>
                            Recently Uploaded
                        </h6>
                    </div>

                    <ul class="list-group list-group-flush">

                        <?php if ($recentUploads->num_rows === 0): ?>
                            <li class="list-group-item text-muted">
                                No uploads yet
                            </li>
                        <?php endif; ?>

                        <?php while ($up = $recentUploads->fetch_assoc()): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">

                                <div>
                                    <div class="fw-semibold">
                                        <?= htmlspecialchars($up['title']) ?>
                                    </div>
                                    <small class="text-muted">
                                        <?= htmlspecialchars($up['uploader_name']) ?>
                                    </small>
                                </div>

                                <small class="text-muted">
                                    <?= date("M j, H:i", strtotime($up['created_at'])) ?>
                                </small>

                            </li>
                        <?php endwhile; ?>

                    </ul>

                </div>
            </div>

        </div>

    </div>
</div>

<!-- MODAL -->
<div id="uploadModal" class="custom-modal" style="display:none;">
    <div class="custom-modal-content p-4">

        <div class="d-flex justify-content-between mb-3">
            <h5><i class="bi bi-cloud-upload-fill"></i> Upload Material</h5>
            <button class="btn btn-sm btn-outline-danger" onclick="closeModal()">✕</button>
        </div>

        <form action="upload_handler.php" method="POST" enctype="multipart/form-data">

            <input type="text" name="title" class="form-control mb-2" placeholder="Title" required>
            <textarea name="description" class="form-control mb-2" placeholder="Description"></textarea>
            <input type="file" name="file" class="form-control mb-2" required>

            <select name="visibility" class="form-control mb-3">
                <option value="private">Private</option>
                <option value="institution">Institution</option>
                <option value="public">Public</option>
            </select>

            <button class="btn btn-success w-100">Upload</button>
        </form>

    </div>
</div>

<script>
function openModal() {
    document.getElementById("uploadModal").style.display = "flex";
}

function closeModal() {
    document.getElementById("uploadModal").style.display = "none";
}

window.onclick = function(e) {
    let modal = document.getElementById("uploadModal");
    if (e.target === modal) modal.style.display = "none";
}
</script>

</body>
</html>