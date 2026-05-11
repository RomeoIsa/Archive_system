<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'student';

$themeClass = ($role === 'staff')
    ? 'theme-staff'
    : 'theme-student';

$activePage = 'recent';

/*
    FETCH RECENTS
*/
$stmt = $conn->prepare("
    SELECT uploads.*,
           recent_views.viewed_at

    FROM recent_views

    JOIN uploads
    ON recent_views.upload_id = uploads.id

    WHERE recent_views.user_id = ?

    ORDER BY recent_views.viewed_at DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Recent Activity</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../assests/style.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="<?= $themeClass; ?>">

<div class="d-flex">

    <?php include "../includes/sidebar.php"; ?>

    <div class="main-content flex-grow-1 p-4">

        <div class="mb-4">

            <h4>
                <i class="bi bi-clock-history"></i>
                Recent Activity
            </h4>

            <small class="text-muted">
                Files you've recently viewed
            </small>

        </div>

        <?php if ($result->num_rows === 0): ?>

            <div class="alert alert-info">
                No recent activity yet.
            </div>

        <?php else: ?>

            <?php
            $lastDate = '';
            ?>

            <?php while ($row = $result->fetch_assoc()): ?>

                <?php
                $date = date('Y-m-d', strtotime($row['viewed_at']));
                ?>

                <?php if ($date !== $lastDate): ?>

                    <?php
                    if ($date == date('Y-m-d')) {
                        $heading = "Today";
                    } elseif ($date == date('Y-m-d', strtotime('-1 day'))) {
                        $heading = "Yesterday";
                    } else {
                        $heading = date('F j, Y', strtotime($date));
                    }

                    $lastDate = $date;
                    ?>

                    <h5 class="mt-4 mb-3">
                        <?= $heading ?>
                    </h5>

                <?php endif; ?>

                <div class="card border-0 shadow-sm p-3 mb-3 file-card">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <h6 class="mb-1">
                                <?= htmlspecialchars($row['title']) ?>
                            </h6>

                            <small class="text-muted">
                                <?= strtoupper($row['file_type']) ?>
                            </small>

                        </div>

                        <div class="text-end">

                            <small class="text-muted d-block">
                                <?= date('g:i A', strtotime($row['viewed_at'])) ?>
                            </small>

                            <a href="view_file.php?id=<?= $row['id'] ?>"
                               class="btn btn-sm btn-outline-primary mt-1">

                                Open Again

                            </a>

                        </div>

                    </div>

                </div>

            <?php endwhile; ?>

        <?php endif; ?>

    </div>

</div>

</body>
</html>