<?php
session_start();
require "../config/db.php";

/*
    SAFE SESSION HANDLING
*/
$user_id = $_SESSION['user_id'] ?? null;
$institution_id = $_SESSION['institution_id'] ?? 0;

/*
    FILTERS
*/
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$type = isset($_GET['type']) ? trim($_GET['type']) : "";

/*
    QUERY WITH USER JOIN (UPLOADERS)
*/
$sql = "
SELECT uploads.*, users.name AS uploader_name
FROM uploads
JOIN users ON uploads.user_id = users.id
WHERE 
(
    uploads.visibility = 'public'
    OR (uploads.visibility = 'institution' AND uploads.institution_id = ?)
)
AND (uploads.title LIKE ? OR uploads.description LIKE ?)
";

$params = [];
$types = "iss";

$like = "%$search%";

$params[] = $institution_id;
$params[] = $like;
$params[] = $like;

/*
    FILE TYPE FILTER
*/
if (!empty($type)) {
    $sql .= " AND uploads.file_type = ?";
    $types .= "s";
    $params[] = $type;
}

$sql .= " ORDER BY uploads.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();

$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shared Library</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-4">

    <h3>🌐 Shared Library</h3>

    <a href="dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>

    <!-- SEARCH + FILTERS -->
    <form method="GET" class="mb-4">

        <div class="row">

            <div class="col-md-6">
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="Search files..." 
                       value="<?= htmlspecialchars($search) ?>">
            </div>

            <div class="col-md-3">
                <select name="type" class="form-control">
                    <option value="">All Types</option>
                    <option value="pdf" <?= $type == 'pdf' ? 'selected' : '' ?>>PDF</option>
                    <option value="docx" <?= $type == 'docx' ? 'selected' : '' ?>>DOCX</option>
                    <option value="jpg" <?= $type == 'jpg' ? 'selected' : '' ?>>JPG</option>
                    <option value="png" <?= $type == 'png' ? 'selected' : '' ?>>PNG</option>
                    <option value="txt" <?= $type == 'txt' ? 'selected' : '' ?>>TXT</option>
                </select>
            </div>

            <div class="col-md-3">
                <button class="btn btn-primary w-100">Search</button>
            </div>

        </div>

    </form>

    <?php if ($result->num_rows === 0): ?>

        <div class="alert alert-info">
            No files found.
        </div>

    <?php else: ?>

    <!-- CARD GRID -->
    <div class="row">

        <?php while ($row = $result->fetch_assoc()): ?>

        <div class="col-md-4 mb-4">

            <div class="card shadow-sm h-100">

                <div class="card-body">

                    <!-- TITLE -->
                    <h5 class="card-title">
                        <?= htmlspecialchars($row['title']) ?>
                    </h5>

                    <!-- DESCRIPTION -->
                    <p class="card-text text-muted">
                        <?= htmlspecialchars(substr($row['description'], 0, 90)) ?>
                        <?= strlen($row['description']) > 90 ? '...' : '' ?>
                    </p>

                    <!-- UPLOADER -->
                    <p class="mb-1">
                        <strong>Uploaded by:</strong>
                        <?= htmlspecialchars($row['uploader_name']) ?>
                    </p>

                    <!-- FILE INFO -->
                    <p class="mb-1">
                        <strong>Type:</strong> <?= strtoupper($row['file_type']) ?>
                    </p>

                    <p class="mb-2">
                        <strong>Size:</strong> <?= round($row['file_size'] / 1024, 2) ?> KB
                    </p>

                    <!-- VISIBILITY BADGE -->
                    <?php if ($row['visibility'] == 'public'): ?>
                        <span class="badge bg-success">Public</span>
                    <?php elseif ($row['visibility'] == 'institution'): ?>
                        <span class="badge bg-primary">Institution</span>
                    <?php else: ?>
                        <span class="badge bg-dark">Private</span>
                    <?php endif; ?>

                </div>

                <!-- ACTIONS -->
                <div class="card-footer bg-white d-flex justify-content-between">

                    <a href="view_file.php?id=<?= $row['id'] ?>" 
                       class="btn btn-sm btn-primary">
                       View
                    </a>

                    <a href="download.php?file=<?= $row['file_name'] ?>" 
                       class="btn btn-sm btn-success">
                       Download
                    </a>

                </div>

            </div>

        </div>

        <?php endwhile; ?>

    </div>

    <?php endif; ?>

</div>

</body>
</html>