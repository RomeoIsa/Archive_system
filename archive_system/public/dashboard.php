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

    <!-- Bootstrap CSS (styling only) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../assets/style.css">
</head>

<body>

<div class="container mt-4">

    <h3>Welcome to your Dashboard</h3>
    <a href="library.php" class="btn btn-info ms-2">
    Browse Library
</a>

    <a href="logout.php" class="btn btn-danger mb-3">Logout</a>

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

    <!-- UPLOAD BUTTON -->
    <button class="btn btn-primary" onclick="openModal()">
        + Upload File
    </button>
    <a href="files.php" class="btn btn-dark ms-2">
    My Files
</a>

</div>

<!-- ===================== -->
<!-- UPLOAD MODAL -->
<!-- ===================== -->

<div id="uploadModal" class="custom-modal" style="display:none;">

    <div class="custom-modal-content">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5>Upload Document</h5>
            <button class="btn btn-sm btn-danger" onclick="closeModal()">X</button>
        </div>

        <form action="upload_handler.php" method="POST" enctype="multipart/form-data" id="uploadForm">

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
        <option value="private">Private (Only me)</option>
        <option value="institution">Institution (Shared)</option>
        <option value="public">Public (Anyone)</option>
    </select>
</div>

            <button type="submit" class="btn btn-success w-100">
                Upload
            </button>

        </form>

    </div>

</div>

<!-- ===================== -->
<!-- JS CONTROL -->
<!-- ===================== -->

<script>
function openModal() {
    document.getElementById("uploadModal").style.display = "block";
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

// SAFETY: FORCE HIDDEN ON PAGE LOAD
window.addEventListener("load", function () {
    document.getElementById("uploadModal").style.display = "none";

    let form = document.getElementById("uploadForm");
    if (form) form.reset();
});
</script>

</body>
</html>