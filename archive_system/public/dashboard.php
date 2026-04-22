<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>

<body class="bg-light">

<div class="container mt-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">📊 Dashboard</h3>

        <div>
            <a href="library.php" class="btn btn-outline-primary me-2">
                🌐 Library
            </a>

            <a href="files.php" class="btn btn-outline-dark me-2">
                📂 My Files
            </a>

            <a href="logout.php" class="btn btn-danger">
                Logout
            </a>
        </div>
    </div>

    <!-- SUCCESS MESSAGE -->
    <?php if (isset($_SESSION['upload_success'])): ?>
        <div class="alert alert-success">
            <?php 
                echo $_SESSION['upload_success'];
                unset($_SESSION['upload_success']);
            ?>
        </div>
    <?php endif; ?>

    <!-- ERROR MESSAGE -->
    <?php if (isset($_SESSION['upload_error'])): ?>
        <div class="alert alert-danger">
            <?php 
                echo $_SESSION['upload_error']; 
                unset($_SESSION['upload_error']);
            ?>
        </div>
    <?php endif; ?>

    <!-- MAIN CARD -->
    <div class="card shadow-sm p-4 " style="width: 250px;">

        <h5 class="mb-3">Quick Actions</h5>

        <button class="btn btn-primary" onclick="openModal()">
            + Upload File
        </button>

    </div>

</div>

<!-- ===================== -->
<!-- UPLOAD MODAL -->
<!-- ===================== -->

<div id="uploadModal" class="custom-modal" style="display:none;">

    <div class="custom-modal-content p-4 rounded shadow">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">📤 Upload File</h5>
            <button class="btn btn-sm btn-outline-danger" onclick="closeModal()">✕</button>
        </div>

        <form action="upload_handler.php" method="POST" enctype="multipart/form-data" id="uploadForm">

            <!-- TITLE -->
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" placeholder="Enter file title..." required>
            </div>

            <!-- DESCRIPTION -->
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Brief description..."></textarea>
            </div>

            <!-- FILE -->
            <div class="mb-3">
                <label class="form-label">Choose File</label>
                <input type="file" name="file" class="form-control" required>
                <small class="text-muted">Allowed: PDF, DOCX, JPG, PNG, TXT (Max 5MB)</small>
            </div>

            <!-- VISIBILITY -->
            <div class="mb-4">
                <label class="form-label">Visibility</label>
                <select name="visibility" class="form-control">
                    <option value="private">🔒 Private (Only me)</option>
                    <option value="institution">🏫 Institution</option>
                    <option value="public">🌍 Public</option>
                </select>
            </div>

            <!-- SUBMIT -->
            <button type="submit" class="btn btn-success w-100">
                Upload File
            </button>

        </form>

    </div>

</div>

<!-- ===================== -->
<!-- JS CONTROL -->
<!-- ===================== -->

<script>
function openModal() {
    document.getElementById("uploadModal").style.display = "flex";
}

function closeModal() {
    document.getElementById("uploadModal").style.display = "none";
}

// CLOSE ON OUTSIDE CLICK
window.onclick = function(event) {
    let modal = document.getElementById("uploadModal");
    if (event.target === modal) {
        modal.style.display = "none";
    }
}

// RESET FORM ON LOAD
window.addEventListener("load", function () {
    document.getElementById("uploadModal").style.display = "none";

    let form = document.getElementById("uploadForm");
    if (form) form.reset();
});
</script>

</body>
</html>