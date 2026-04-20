<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$search = isset($_GET['search']) ? trim($_GET['search']) : "";

// search logic
if ($search !== "") {
    $stmt = $conn->prepare("
        SELECT * FROM uploads 
        WHERE visibility = 'public' 
        AND title LIKE ?
        ORDER BY created_at DESC
    ");
    $like = "%$search%";
    $stmt->bind_param("s", $like);
} else {
    $stmt = $conn->prepare("
        SELECT * FROM uploads 
        WHERE visibility = 'public'
        ORDER BY created_at DESC
    ");
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Library</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-4">

    <h3>🌐 Shared Library</h3>

    <a href="dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>

    <!-- SEARCH -->
    <form method="GET" class="mb-3">
        <input type="text" name="search" class="form-control" placeholder="Search files..." value="<?= htmlspecialchars($search) ?>">
    </form>

    <?php if ($result->num_rows === 0): ?>
        <div class="alert alert-info">No shared files found.</div>
    <?php else: ?>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Type</th>
                <th>Size</th>
                <th>Date</th>
                <th>Action</th>
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
                </td>
            </tr>
        <?php endwhile; ?>

        </tbody>
    </table>

    <?php endif; ?>

</div>

</body>
</html>