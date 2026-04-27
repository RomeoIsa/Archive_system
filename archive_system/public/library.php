<?php
session_start();
require "../config/db.php";

/*
    AUTH CHECK
*/
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/*
    SESSION DATA
*/
$user_id = $_SESSION['user_id'];
$institution_id = $_SESSION['institution_id'] ?? 0;
$role = $_SESSION['role'] ?? 'student';
$name = $_SESSION['name'] ?? 'User';
$themeClass = ($role === 'staff') ? 'theme-staff' : 'theme-student';

$activePage = 'library';

/*
    FILTERS
*/
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$type = isset($_GET['type']) ? trim($_GET['type']) : "";

/*
    QUERY
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
    <link rel="stylesheet" href="../assests/style.css">
</head>

<body class="<?php echo $themeClass; ?>">

<div class="d-flex">

    <!-- SIDEBAR -->
    <?php include "../includes/sidebar.php"; ?>

    <!-- MAIN -->
    <div class="main-content flex-grow-1 p-4">

        <!-- TOP BAR -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>🌐 Shared Library</h4>

            <div class="d-flex align-items-center gap-3">
                <strong><?php echo $name; ?></strong>
                <img src="https://via.placeholder.com/35" class="rounded-circle">
            </div>
        </div>

        <!-- SEARCH + FILTER -->
        <form method="GET" class="mb-4">
            <div class="row g-2">

                <div class="col-md-6">
                    <input 
                        type="text" 
                        name="search" 
                        class="form-control search-bar"
                        placeholder="Search files..."
                        value="<?= htmlspecialchars($search) ?>"
                    >
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

        <!-- RESULTS -->
        <?php if ($result->num_rows === 0): ?>

            <div class="alert alert-info">No files found.</div>

        <?php else: ?>

            <div class="row">

                <?php while ($row = $result->fetch_assoc()): ?>

                <div class="col-md-4 mb-4">
                    <div class="card file-card h-100 p-3">

                        <!-- TITLE -->
                        <h6 class="mb-1">
                            <?= htmlspecialchars($row['title']) ?>
                        </h6>

                        <!-- DESCRIPTION -->
                        <small class="text-muted">
                            <?= htmlspecialchars(substr($row['description'], 0, 80)) ?>
                            <?= strlen($row['description']) > 80 ? '...' : '' ?>
                        </small>

                        <!-- META -->
                        <div class="mt-2 small text-muted">
                            By <?= htmlspecialchars($row['uploader_name']) ?>
                        </div>

                        <div class="mt-2 d-flex justify-content-between align-items-center">
                            <span class="badge bg-secondary">
                                <?= strtoupper($row['file_type']) ?>
                            </span>

                            <small>
                                <?= round($row['file_size'] / 1024, 2) ?> KB
                            </small>
                        </div>

                        <!-- VISIBILITY -->
                        <div class="mt-2">
                            <?php if ($row['visibility'] == 'public'): ?>
                                <span class="badge bg-success">Public</span>
                            <?php elseif ($row['visibility'] == 'institution'): ?>
                                <span class="badge bg-primary">Institution</span>
                            <?php else: ?>
                                <span class="badge bg-dark">Private</span>
                            <?php endif; ?>
                        </div>

                        <!-- ACTIONS -->
                        <div class="mt-auto d-flex justify-content-between pt-3">

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
</div>

</body>
</html>