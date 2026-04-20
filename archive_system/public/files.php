<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// search input
$search = isset($_GET['search']) ? trim($_GET['search']) : "";

// base query
if ($search !== "") {
    $stmt = $conn->prepare("
        SELECT * FROM uploads 
        WHERE user_id = ? AND title LIKE ? 
        ORDER BY created_at DESC
    ");
    $like = "%$search%";
    $stmt->bind_param("is", $user_id, $like);
} else {
    $stmt = $conn->prepare("
        SELECT * FROM uploads 
        WHERE user_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Files</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-4">

    <h3>📂 My Files</h3>

    <a href="dashboard.php" class="btn btn-secondary mb-3">Back</a>

    <!-- SEARCH BAR -->
    <form method="GET" class="mb-3">
        <input type="text" name="search" class="form-control" placeholder="Search files by title..." value="<?= htmlspecialchars($search) ?>">
    </form>

    <?php if ($result->num_rows === 0): ?>
        <div class="alert alert-info">No files found.</div>
    <?php else: ?>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Type</th>
                <th>Size</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>

        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= strtoupper($row['file_type']) ?></td>
                <td><?= round($row['file_size'] / 1024, 2) ?> KB</td>
                <td><?= $row['created_at'] ?></td>
                <td>
                    <a class="btn btn-success btn-sm"
                       href="download.php?file=<?= $row['file_name'] ?>">
                       Download
                    </a>

                    <a class="btn btn-danger btn-sm"
                       href="delete_file.php?id=<?= $row['id'] ?>"
                       onclick="return confirm('Delete this file?')">
                       Delete
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>

        </tbody>
    </table>

    <?php endif; ?>

</div>

</body>
</html>