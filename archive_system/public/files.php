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
$themeClass = ($role === 'staff') ? 'theme-staff' : 'theme-student';

$activePage = 'files';

/* SEARCH */
$search = isset($_GET['search']) ? trim($_GET['search']) : "";

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
    <title>My Uploads</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
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
                <h4><i class="bi bi-folder-fill"></i> My Uploads</h4>

                <div class="d-flex align-items-center gap-3">
                    <strong><?php echo $name; ?></strong>
                    <img src="https://via.placeholder.com/35" class="rounded-circle">
                </div>
            </div>

            <!-- SEARCH -->
            <form method="GET" class="mb-4">
                <input
                    type="text"
                    name="search"
                    class="form-control search-bar"
                    placeholder="Search files by title..."
                    value="<?= htmlspecialchars($search) ?>">
            </form>

            <!-- CONTENT -->
            <?php if ($result->num_rows === 0): ?>

                <div class="alert alert-info">No files found.</div>

            <?php else: ?>

                <div class="row">

                    <?php while ($row = $result->fetch_assoc()): ?>

                        <div class="col-md-6 mb-3">
                            <div class="card p-3 file-card h-100 d-flex flex-column">

                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="mb-1">
                                            <?= htmlspecialchars($row['title']) ?>
                                        </h6>

                                        <small class="text-muted">
                                            <?= htmlspecialchars($row['description']) ?>
                                        </small>
                                    </div>

                                    <!-- META BADGES (FILE TYPE + VISIBILITY) -->
                                    <div class="d-flex flex-wrap gap-2 mt-2">

                                        <!-- FILE TYPE -->
                                        <span class="badge rounded-pill bg-light text-dark border d-flex align-items-center gap-1">
                                            <i class="bi bi-file-earmark-fill"></i>
                                            <?= strtoupper($row['file_type']) ?>
                                        </span>

                                        <!-- VISIBILITY -->
                                        <?php if ($row['visibility'] == 'public'): ?>
                                            <span class="badge rounded-pill bg-success d-flex align-items-center gap-1">
                                                <i class="bi bi-globe-fill"></i>
                                                Public
                                            </span>

                                        <?php elseif ($row['visibility'] == 'institution'): ?>
                                            <span class="badge rounded-pill bg-primary d-flex align-items-center gap-1">
                                                <i class="bi bi-building-fill"></i>
                                                Institution
                                            </span>

                                        <?php else: ?>
                                            <span class="badge rounded-pill bg-secondary d-flex align-items-center gap-1">
                                                <i class="bi bi-lock-fill"></i>
                                                Private
                                            </span>
                                        <?php endif; ?>

                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-3">

                                    <small class="text-muted">
                                        <?= round($row['file_size'] / 1024, 2) ?> KB •
                                        <?= date("M d, Y", strtotime($row['created_at'])) ?>
                                    </small>

                                    <div class="mt-auto d-flex gap-2">

                                        <a href="view_file.php?id=<?= $row['id'] ?>"
                                            class="btn btn-outline-primary btn-sm w-100">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>

                                        <a href="download.php?id=<?= $row['id'] ?>"
                                            class="btn btn-success btn-sm w-100">
                                            <i class="bi bi-download"></i>
                                        </a>

                                        <a href="edit_file.php?id=<?= $row['id'] ?>"
                                            class="btn btn-warning btn-sm w-100">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>

                                        <a href="delete_file.php?id=<?= $row['id'] ?>"
                                            class="btn btn-outline-danger btn-sm w-100"
                                            onclick="return confirm('Delete this file?')">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>

                                    </div>

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