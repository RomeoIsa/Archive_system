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

    $uploads = $conn->query("SELECT COUNT(*) as total FROM uploads WHERE user_id = $user_id")
        ->fetch_assoc()['total'] ?? 0;

    /* SAFE DOWNLOAD CHECK */
    $downloads = 0;
    $check = $conn->query("SHOW TABLES LIKE 'downloads'");
    if ($check && $check->num_rows > 0) {
        $downloads = $conn->query("SELECT COUNT(*) as total FROM downloads WHERE user_id = $user_id")
            ->fetch_assoc()['total'] ?? 0;
    }

    $saved = 0;
    $checkSaved = $conn->query("SHOW TABLES LIKE 'saved'");
    if ($checkSaved && $checkSaved->num_rows > 0) {
        $saved = $conn->query("SELECT COUNT(*) as total FROM saved WHERE user_id = $user_id")
            ->fetch_assoc()['total'] ?? 0;
    }

    $stats = [
        ['label' => ' <i class="bi bi-cloud-upload"></i> &nbsp; Total Uploads', 'value' => $uploads],
        ['label' => '<i class="bi bi-cloud-download"></i> &nbsp; Total Downloads', 'value' => $downloads],
        ['label' => '<i class="bi bi-wallet"></i> &nbsp; Purchases', 'value' => 0],
        ['label' => '<i class="bi bi-bookmark-fill"></i> &nbsp; Saved', 'value' => $saved],
    ];
}

/* -----------------------
   STAFF STATS
------------------------*/
if ($role === 'staff') {

    $uploads = $conn->query("SELECT COUNT(*) as total FROM uploads")
        ->fetch_assoc()['total'] ?? 0;

    $downloads = 0;
    $check = $conn->query("SHOW TABLES LIKE 'downloads'");
    if ($check && $check->num_rows > 0) {
        $downloads = $conn->query("SELECT COUNT(*) as total FROM downloads")
            ->fetch_assoc()['total'] ?? 0;
    }

    $earnings = 0;
    $checkEarn = $conn->query("SHOW TABLES LIKE 'earnings'");
    if ($checkEarn && $checkEarn->num_rows > 0) {
        $earnings = $conn->query("SELECT SUM(amount) as total FROM earnings")
            ->fetch_assoc()['total'] ?? 0;
    }

    $users = $conn->query("SELECT COUNT(*) as total FROM users")
        ->fetch_assoc()['total'] ?? 0;

    $stats = [
        ['label' => 'Total Uploads', 'value' => $uploads],
        ['label' => 'Total Downloads', 'value' => $downloads],
        ['label' => 'Earnings', 'value' => '₦' . number_format($earnings)],
        ['label' => 'Active Users', 'value' => $users],
    ];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assests/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="<?php echo $themeClass; ?>">

<div class="d-flex">

    <?php include "../includes/sidebar.php"; ?>

    <div class="main-content flex-grow-1 p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div></div>

            <div class="d-flex align-items-center gap-3">
                <span class="notification"><i class="bi bi-bell-fill"></i></span>
                <strong><?php echo $name; ?></strong>
                <img src="https://via.placeholder.com/35" class="rounded-circle">
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4>Welcome back, <?php echo $name; ?> 👋</h4>
                <p class="text-muted">Find, learn and share academic resources.</p>
            </div>

            <button class="btn btn-primary" onclick="openModal()" id="BtnUpload">
                + Upload Material
            </button>
        </div>

        <!-- STATS -->
        <div class="row mt-4">

            <?php foreach ($stats as $stat): ?>
                <div class="col-md-3">
                    <div class="card stat-card p-3">
                        <h6><?= $stat['label']; ?></h6>
                        <h3><?= $stat['value']; ?></h3>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>

        <div class="row mt-4">

            <div class="col-md-6">
                <div class="card p-3">
                    <h6>Recent Uploads</h6>
                    <ul class="list-group">
                        <li class="list-group-item">Data Structures Notes</li>
                        <li class="list-group-item">Database Systems</li>
                        <li class="list-group-item">Calculus Summary</li>
                    </ul>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card p-3">
                    <h6>Recent Activity</h6>
                    <ul class="list-group">
                        <li class="list-group-item">Downloaded notes</li>
                        <li class="list-group-item">Uploaded file</li>
                        <li class="list-group-item">Saved material</li>
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