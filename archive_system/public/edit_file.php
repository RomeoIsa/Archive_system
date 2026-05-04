<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    die("Invalid request");
}

$file_id = intval($_GET['id']);

/* FETCH FILE */
$stmt = $conn->prepare("SELECT * FROM uploads WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $file_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("File not found or access denied");
}

$file = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit File</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5" style="max-width: 600px;">

    <div class="card p-4 shadow-sm">

        <h4 class="mb-3">✏️ Edit File</h4>

        <form method="POST" action="process_edit_file.php">

            <input type="hidden" name="id" value="<?= $file['id'] ?>">

            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control"
                       value="<?= htmlspecialchars($file['title']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($file['description']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Visibility</label>
                <select name="visibility" class="form-select">
                    <option value="private" <?= $file['visibility'] == 'private' ? 'selected' : '' ?>>Private</option>
                    <option value="institution" <?= $file['visibility'] == 'institution' ? 'selected' : '' ?>>Institution</option>
                    <option value="public" <?= $file['visibility'] == 'public' ? 'selected' : '' ?>>Public</option>
                </select>
            </div>

            <div class="d-flex justify-content-between">

                <a href="files.php" class="btn btn-secondary">
                    Cancel
                </a>

                <button class="btn btn-primary">
                    Update File
                </button>

            </div>

        </form>

    </div>

</div>

</body>
</html>