<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$institution_id = $_SESSION['institution_id'] ?? 0;
$role = $_SESSION['role'] ?? 'student';
$name = $_SESSION['name'] ?? 'User';
$themeClass = ($role === 'staff') ? 'theme-staff' : 'theme-student';

$activePage = 'library';

/* FILTERS */
$search = $_GET['search'] ?? "";
$type = $_GET['type'] ?? "";
$sort = $_GET['sort'] ?? "latest";

$search = trim($search);

/* QUERY */
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

if (!empty($type)) {
    $sql .= " AND uploads.file_type = ?";
    $types .= "s";
    $params[] = $type;
}

/* SORT */
$sql .= ($sort === "oldest") 
    ? " ORDER BY uploads.created_at ASC" 
    : " ORDER BY uploads.created_at DESC";

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
    <link rel="stylesheet" href="../assests/style.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="<?= $themeClass; ?>">

<div class="d-flex">

    <?php include "../includes/sidebar.php"; ?>

    <div class="main-content flex-grow-1 p-4">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0"><i class="bi bi-book"></i> Shared Library</h4>
                <small class="text-muted">Browse materials uploaded by others</small>
            </div>

            <div class="d-flex align-items-center gap-3">
                <strong><?= htmlspecialchars($name) ?></strong>
                <img src="https://via.placeholder.com/35" class="rounded-circle">
            </div>
        </div>

        <!-- SEARCH + FILTER -->
        <form method="GET" class="mb-4">
            <div class="row g-2 align-items-center">

                <div class="col-md-5">
                    <input 
                        type="text" 
                        name="search" 
                        class="form-control"
                        placeholder="Search by title or description..."
                        value="<?= htmlspecialchars($search) ?>"
                    >
                </div>

                <div class="col-md-2">
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="pdf" <?= $type == 'pdf' ? 'selected' : '' ?>>PDF</option>
                        <option value="docx" <?= $type == 'docx' ? 'selected' : '' ?>>DOCX</option>
                        <option value="jpg" <?= $type == 'jpg' ? 'selected' : '' ?>>JPG</option>
                        <option value="png" <?= $type == 'png' ? 'selected' : '' ?>>PNG</option>
                        <option value="txt" <?= $type == 'txt' ? 'selected' : '' ?>>TXT</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="sort" class="form-select">
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

            <div class="alert alert-info">
                No materials found. Try adjusting your search or filters.
            </div>

        <?php else: ?>

            <div class="row">

                <?php while ($row = $result->fetch_assoc()): ?>

                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm border-0 h-100 p-3 file-card">

                        <!-- TITLE -->
                        <h6 class="fw-semibold mb-1">
                            <?= htmlspecialchars($row['title']) ?>
                        </h6>

                        <!-- DESCRIPTION -->
                        <p class="text-muted small mb-2">
                            <?= htmlspecialchars(substr($row['description'], 0, 90)) ?>
                            <?= strlen($row['description']) > 90 ? '...' : '' ?>
                        </p>

                        <!-- META -->
                        <div class="small text-muted mb-2">
                            <i class="bi bi-person"></i>
                            <?= htmlspecialchars($row['uploader_name']) ?>
                        </div>

                        <!-- FILE INFO -->
                        <div class="d-flex justify-content-between align-items-center mb-2">

                            <!-- File Type Badge -->
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-file-earmark"></i>
                                <?= strtoupper($row['file_type']) ?>
                            </span>

                            <!-- File Size -->
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
                               class="btn btn-outline-primary btn-sm w-50">
                               <i class="bi bi-eye"></i> View
                            </a>

                            <a href="download.php?id=<?= $row['id'] ?>" 
                               class="btn btn-success btn-sm w-50">
                               <i class="bi bi-download"></i> Download
                            </a>

                        </div>

                    </div>
                </div>

                <?php endwhile; ?>

            </div>

        <?php endif; ?>

    </div>
</div>

</body>
</html>