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

$themeClass = ($role === 'staff')
    ? 'theme-staff'
    : 'theme-student';

$activePage = 'library';

/*
|--------------------------------------------------------------------------
| FILE ICON HELPER
|--------------------------------------------------------------------------
*/
function getFileIcon($type)
{
    return match (strtolower($type)) {
        'pdf' => '<i class="bi bi-file-earmark-pdf"></i>',
        'doc', 'docx' => '<i class="bi bi-files"></i>',
        'ppt', 'pptx' => '<i class="bi bi-file-earmark-ppt-fill"></i>',
        'xls', 'xlsx' => '<i class="bi bi-filetype-xls"></i>',
        'jpg', 'jpeg', 'png', 'webp' => '<i class="bi bi-file-earmark-image"></i>',
        'zip', 'rar', '7z' => '<i class="bi bi-file-earmark-zip"></i>',
        'mp4' => '<i class="bi bi-filetype-mp4"></i>',
        'mp3', 'wav' => '<i class="bi bi-filetype-mp3"></i>',
        'txt' => '<i class="bi bi-filetype-txt"></i>',
        default => '<i class="bi bi-archive"></i>'
    };
}

/*
|--------------------------------------------------------------------------
| FILTERS
|--------------------------------------------------------------------------
*/
$search = $_GET['search'] ?? "";
$type = $_GET['type'] ?? "";
$sort = $_GET['sort'] ?? "latest";

$search = trim($search);

/*
|--------------------------------------------------------------------------
| QUERY
|--------------------------------------------------------------------------
*/
$sql = "
SELECT 
    uploads.*, 
    users.name AS uploader_name,
    saved.upload_id AS saved_upload_id

FROM uploads

JOIN users 
ON uploads.user_id = users.id

LEFT JOIN saved 
ON uploads.id = saved.upload_id 
AND saved.user_id = ?

WHERE 
(
    uploads.visibility = 'public'
    OR (
        uploads.visibility = 'institution'
        AND uploads.institution_id = ?
    )
)

AND (
    uploads.title LIKE ?
    OR uploads.description LIKE ?
)
";

$params = [];
$types = "iiss";

$like = "%$search%";

$params[] = $user_id;
$params[] = $institution_id;
$params[] = $like;
$params[] = $like;

/*
|--------------------------------------------------------------------------
| FILE TYPE FILTER
|--------------------------------------------------------------------------
*/
if (!empty($type)) {

    $sql .= " AND uploads.file_type = ?";

    $types .= "s";
    $params[] = $type;
}

/*
|--------------------------------------------------------------------------
| SORTING
|--------------------------------------------------------------------------
*/
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
            <div class="d-flex justify-content-between align-items-center page-header">

                <div>

                    <h4 class="mb-1">
                        <i class="bi bi-book"></i>
                        Shared Library
                    </h4>

                    <small class="text-muted">
                        Browse materials uploaded by students and lecturers
                    </small>

                </div>

                <div class="d-flex align-items-center gap-3">

                    <strong>
                        <?= htmlspecialchars($name) ?>
                    </strong>

                    <img
                        src="https://via.placeholder.com/40"
                        class="rounded-circle dashboard-avatar"
                        width="40"
                        height="40">

                </div>

            </div>

            <!-- SEARCH + FILTERS -->
            <form method="GET" class="mb-4">

                <div class="row g-2 align-items-center">

                    <!-- SEARCH -->
                    <div class="col-md-5">  

                        <input
                            type="text"
                            name="search"
                            class="form-control search-box"
                            placeholder="Search by title or description..."
                            value="<?= htmlspecialchars($search) ?>">

                    </div>

                    <!-- TYPE -->
                    <div class="col-md-2">

                        <select name="type" class="form-select filter-select">

                            <option value="">All Types</option>

                            <option value="pdf" <?= $type == 'pdf' ? 'selected' : '' ?>>
                                PDF
                            </option>

                            <option value="docx" <?= $type == 'docx' ? 'selected' : '' ?>>
                                DOCX
                            </option>

                            <option value="pptx" <?= $type == 'pptx' ? 'selected' : '' ?>>
                                PPTX
                            </option>

                            <option value="xlsx" <?= $type == 'xlsx' ? 'selected' : '' ?>>
                                XLSX
                            </option>

                            <option value="jpg" <?= $type == 'jpg' ? 'selected' : '' ?>>
                                JPG
                            </option>

                            <option value="png" <?= $type == 'png' ? 'selected' : '' ?>>
                                PNG
                            </option>

                            <option value="txt" <?= $type == 'txt' ? 'selected' : '' ?>>
                                TXT
                            </option>

                            <option value="zip" <?= $type == 'zip' ? 'selected' : '' ?>>
                                ZIP
                            </option>

                        </select>

                    </div>

                    <!-- SORT -->
                    <div class="col-md-2">

                        <select name="sort" class="form-select filter-select">

                            <option value="latest" <?= $sort == 'latest' ? 'selected' : '' ?>>
                                Latest
                            </option>

                            <option value="oldest" <?= $sort == 'oldest' ? 'selected' : '' ?>>
                                Oldest
                            </option>

                        </select>

                    </div>

                    <!-- SEARCH BUTTON -->
                    <div class="col-md-2">

                        <button class="btn btn-primary w-100">

                            <i class="bi bi-search"></i>
                            Search

                        </button>

                    </div>

                </div>

            </form>

            <!-- RESULTS -->
            <?php if ($result->num_rows === 0): ?>

                <div class="empty-state">

                    <div class="empty-state-icon">
                        <i class="bi bi-rocket-takeoff-fill"></i>
                    </div>

                    <h5>
                       Nothing to see here...
                    </h5>

                    <p>
                        Try adjusting your search or filters.
                    </p>

                </div>

            <?php else: ?>

                <div class="row">

                    <?php while ($row = $result->fetch_assoc()): ?>

                        <?php
                        $isSaved = !is_null($row['saved_upload_id']);
                        ?>

                        <div class="col-md-4 mb-4">

                            <div class="card h-100 p-3 file-card">

                                <!-- TOP -->
                                <div class="d-flex align-items-center mb-3">

                                    <div class="file-icon">
                                        <?= getFileIcon($row['file_type']) ?>
                                    </div>

                                    <div class="flex-grow-1">

                                        <div class="file-title">
                                            <?= htmlspecialchars($row['title']) ?>
                                        </div>

                                        <div class="meta-text">

                                            <i class="bi bi-person"></i>

                                            <?= htmlspecialchars($row['uploader_name']) ?>

                                        </div>

                                    </div>

                                </div>

                                <!-- DESCRIPTION -->
                                <p class="file-description mb-3">

                                    <?= htmlspecialchars(substr($row['description'], 0, 100)) ?>

                                    <?= strlen($row['description']) > 100 ? '...' : '' ?>

                                </p>

                                <!-- FILE INFO -->
                                <div class="d-flex justify-content-between align-items-center mb-3">

                                    <span class="badge bg-light text-dark border">

                                        <?= strtoupper($row['file_type']) ?>

                                    </span>

                                    <small class="text-muted">

                                        <?= round($row['file_size'] / 1024, 2) ?>
                                        KB

                                    </small>

                                </div>

                                <!-- VISIBILITY -->
                                <div class="mb-3">

                                    <?php if ($row['visibility'] === 'public'): ?>

                                        <span class="badge bg-success">
                                            Public
                                        </span>

                                    <?php elseif ($row['visibility'] === 'institution'): ?>

                                        <span class="badge bg-primary">
                                            Institution
                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-secondary">
                                            Private
                                        </span>

                                    <?php endif; ?>

                                </div>

                                <!-- ACTIONS -->
                                <div class="mt-auto">

                                    <div class="d-flex gap-2 flex-wrap">

                                        <!-- VIEW -->
                                        <a
                                            href="view_file.php?id=<?= $row['id'] ?>"
                                            class="btn btn-outline-primary btn-sm action-btn">

                                            <i class="bi bi-eye"></i>

                                        </a>

                                        <!-- DOWNLOAD -->
                                        <a
                                            href="download.php?id=<?= $row['id'] ?>"
                                            class="btn btn-success btn-sm action-btn">

                                            <i class="bi bi-download"></i>

                                        </a>

                                        <!-- SAVE -->
                                        <button
                                            class="btn btn-sm <?= $isSaved ? 'btn-success' : 'btn-outline-secondary'; ?> save-btn action-btn"
                                            data-id="<?= $row['id']; ?>">

                                            <i class="bi <?= $isSaved ? 'bi-bookmark-check-fill' : 'bi-bookmark-plus-fill'; ?>"></i>

                                        </button>

                                    </div>

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

            button.addEventListener('click', function() {

                let btn = this;
                let uploadId = btn.dataset.id;

                btn.disabled = true;

                fetch('toggle_save.php', {

                        method: 'POST',

                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },

                        body: 'upload_id=' + uploadId

                    })

                    .then(res => res.text())

                    .then(data => {

                        if (data.trim() === "saved") {

                            btn.className = "btn btn-success btn-sm save-btn";

                            btn.innerHTML =
                                '<i class="bi bi-bookmark-check-fill"></i> Saved';

                        } else {

                            btn.className =
                                "btn btn-outline-secondary btn-sm save-btn";

                            btn.innerHTML =
                                '<i class="bi bi-bookmark-plus-fill"></i> Save';
                        }

                    })

                    .finally(() => {
                        btn.disabled = false;
                    });

            });

        });
    </script>

</body>

</html>