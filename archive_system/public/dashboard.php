<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'] ?? 'student';
$name = $_SESSION['name'] ?? 'User';
$themeClass = ($role === 'staff') ? 'theme-staff' : 'theme-student';

// active page for sidebar
$activePage = 'dashboard';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assests/style.css">
</head>

<body class="<?php echo $themeClass; ?>">

<div class="d-flex">

    <!-- SIDEBAR -->
    <?php include "../includes/sidebar.php"; ?>

    <!-- MAIN CONTENT -->
    <div class="main-content flex-grow-1 p-4">

        <!-- TOP BAR -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            

            <div class="d-flex align-items-center gap-3">
                <span class="notification">🔔</span>
                <strong><?php echo $name; ?></strong>
                <img src="https://via.placeholder.com/35" class="rounded-circle">
            </div>
        </div>

        <!-- HEADER -->
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
            <div class="col-md-3">
                <div class="card stat-card p-3">
                    <h6>Total Uploads</h6>
                    <h3>8</h3>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card stat-card p-3">
                    <h6>Total Downloads</h6>
                    <h3>24</h3>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card stat-card p-3">
                    <h6>Purchases</h6>
                    <h3>5</h3>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card stat-card p-3">
                    <h6>Saved</h6>
                    <h3>12</h3>
                </div>
            </div>
        </div>

        <!-- CONTENT -->
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
                        <li class="list-group-item">Purchased material</li>
                        <li class="list-group-item">Uploaded file</li>
                    </ul>
                </div>
            </div>

        </div>

    </div>
</div>

<!-- ===================== -->
<!-- UPLOAD MODAL -->
<!-- ===================== -->

<div id="uploadModal" class="custom-modal" style="display:none;">

    <div class="custom-modal-content p-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5>📤 Upload Material</h5>
            <button class="btn btn-sm btn-outline-danger" onclick="closeModal()">✕</button>
        </div>

        <form action="upload_handler.php" method="POST" enctype="multipart/form-data">

            <div class="mb-3">
                <label>Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control"></textarea>
            </div>

            <div class="mb-3">
                <label>File</label>
                <input type="file" name="file" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Visibility</label>
                <select name="visibility" class="form-control">
                    <option value="private">Private</option>
                    <option value="institution">Institution</option>
                    <option value="public">Public</option>
                </select>
            </div>

            <button class="btn btn-success w-100">Upload</button>
        </form>

    </div>
</div>

<!-- JS -->
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