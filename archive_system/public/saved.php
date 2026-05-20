<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'student';
$name = $_SESSION['name'] ?? 'User';

$themeClass = ($role === 'staff')
    ? 'theme-staff'
    : 'theme-student';

$activePage = 'saved';

/*
|--------------------------------------------------------------------------
| FILTERS
|--------------------------------------------------------------------------
*/
$search = $_GET['search'] ?? "";
$type = $_GET['type'] ?? "";
$sort = $_GET['sort'] ?? "latest";

$search = trim($search);
$like = "%$search%";

/*
|--------------------------------------------------------------------------
| QUERY
|--------------------------------------------------------------------------
*/
$sql = "
SELECT uploads.*, users.name AS uploader_name
FROM saved
JOIN uploads ON saved.upload_id = uploads.id
JOIN users ON uploads.user_id = users.id
WHERE saved.user_id = ?
AND (
    uploads.title LIKE ?
    OR uploads.description LIKE ?
)
";

$params = [$user_id, $like, $like];
$types = "iss";

if (!empty($type)) {
    $sql .= " AND uploads.file_type = ?";
    $types .= "s";
    $params[] = $type;
}

$sql .= ($sort === "oldest")
    ? " ORDER BY saved.created_at ASC"
    : " ORDER BY saved.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>

    <title>Saved Items</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assests/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

</head>

<body class="<?= $themeClass; ?>">

<div class="d-flex">

    <?php include "../includes/sidebar.php"; ?>

    <div class="main-content flex-grow-1 p-4">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center page-header">

            <div>
                <h4 class="mb-1">
                    <i class="bi bi-bookmarks-fill"></i>
                    Saved Items
                </h4>

                <small class="text-muted">
                    Your bookmarked materials
                </small>
            </div>

            <div class="d-flex align-items-center gap-3">
                <strong><?= htmlspecialchars($name) ?></strong>
                <img src="https://via.placeholder.com/40" class="rounded-circle dashboard-avatar">
            </div>

        </div>

        <!-- SEARCH + FILTER -->
        <form method="GET" class="mb-4">

            <div class="row g-2 align-items-center">

                <div class="col-md-5">
                    <input
                        type="text"
                        name="search"
                        class="form-control search-box"
                        placeholder="Search saved files..."
                        value="<?= htmlspecialchars($search) ?>">
                </div>

                <div class="col-md-2">
                    <select name="type" class="form-select filter-select">

                        <option value="">All Types</option>
                        <option value="pdf" <?= $type == 'pdf' ? 'selected' : '' ?>>PDF</option>
                        <option value="docx" <?= $type == 'docx' ? 'selected' : '' ?>>DOCX</option>
                        <option value="jpg" <?= $type == 'jpg' ? 'selected' : '' ?>>JPG</option>
                        <option value="png" <?= $type == 'png' ? 'selected' : '' ?>>PNG</option>
                        <option value="txt" <?= $type == 'txt' ? 'selected' : '' ?>>TXT</option>

                    </select>
                </div>

                <div class="col-md-2">
                    <select name="sort" class="form-select filter-select">

                        <option value="latest" <?= $sort == 'latest' ? 'selected' : '' ?>>Latest</option>
                        <option value="oldest" <?= $sort == 'oldest' ? 'selected' : '' ?>>Oldest</option>

                    </select>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>

            </div>

        </form>

        <!-- RESULTS -->
        <?php if ($result->num_rows === 0): ?>

            <div class="empty-state">

                <div class="empty-state-icon">⭐</div>

                <h5>No Saved Files</h5>

                <p>Start saving files to see them here.</p>

            </div>

        <?php else: ?>

            <div class="row">

                <?php while ($row = $result->fetch_assoc()): ?>

                    <div class="col-md-4 mb-4">

                        <div class="card file-card h-100 p-3">

                            <!-- TOP -->
                            <div class="d-flex justify-content-between align-items-center mb-2">

                                <div class="file-title">
                                    <?= htmlspecialchars($row['title']) ?>
                                </div>

                                <i class="bi bi-star-fill text-warning"></i>

                            </div>

                            <!-- DESCRIPTION -->
                            <p class="file-description mb-3">

                                <?= htmlspecialchars(substr($row['description'], 0, 100)) ?>
                                <?= strlen($row['description']) > 100 ? '...' : '' ?>

                            </p>

                            <!-- META -->
                            <div class="meta-text mb-2">
                                <i class="bi bi-person"></i>
                                <?= htmlspecialchars($row['uploader_name']) ?>
                            </div>

                            <!-- FILE INFO -->
                            <div class="d-flex justify-content-between align-items-center mb-3">

                                <span class="badge bg-light text-dark border">
                                    <?= strtoupper($row['file_type']) ?>
                                </span>

                                <small class="text-muted">
                                    <?= round($row['file_size'] / 1024, 2) ?> KB
                                </small>

                            </div>

                            <!-- VISIBILITY -->
                            <div class="mb-3">

                                <?php if ($row['visibility'] === 'public'): ?>
                                    <span class="badge bg-success">Public</span>

                                <?php elseif ($row['visibility'] === 'institution'): ?>
                                    <span class="badge bg-primary">Institution</span>

                                <?php else: ?>
                                    <span class="badge bg-secondary">Private</span>
                                <?php endif; ?>

                            </div>

                            <!-- ACTIONS -->
                            <div class="mt-auto d-flex gap-2">

                                <a href="view_file.php?id=<?= $row['id'] ?>"
                                   class="btn btn-outline-primary btn-sm action-btn">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <a href="download.php?id=<?= $row['id'] ?>"
                                   class="btn btn-success btn-sm action-btn">
                                    <i class="bi bi-download"></i>
                                </a>

                                <button
                                    class="btn btn-warning btn-sm action-btn save-btn"
                                    data-id="<?= $row['id']; ?>">

                                    <i class="bi bi-star-fill"></i>

                                </button>

                            </div>

                        </div>

                    </div>

                <?php endwhile; ?>

            </div>

        <?php endif; ?>

    </div>

</div>

<script>

document.querySelectorAll('.save-btn').forEach(button => {

    button.addEventListener('click', function () {

        let card = this.closest('.col-md-4');
        let uploadId = this.dataset.id;

        fetch('toggle_save.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'upload_id=' + uploadId
        })
        .then(() => {

            card.style.transition = '0.25s ease';
            card.style.opacity = '0';
            card.style.transform = 'scale(0.95)';

            setTimeout(() => {
                card.remove();
            }, 250);

        });

    });

});

</script>

</body>
</html>