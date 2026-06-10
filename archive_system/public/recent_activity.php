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

$activePage = 'recent';

// Profile image (match dashboard fallback)
$stmt = $conn->prepare("SELECT profile_image FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$userData = $res->fetch_assoc();

$profile_image = !empty($userData['profile_image'])
    ? '../uploads/profiles/' . $userData['profile_image']
    : '../assests/images/default-avatar.png';

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
| FETCH RECENTS
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT 
        uploads.*,
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

            <!-- HEADER -->
            <div class="d-flex justify-content-between align-items-center page-header">

                <div>

                    <h4 class="mb-1">

                        <i class="bi bi-clock-history"></i>
                        Recent Activity

                    </h4>

                    <small class="text-muted">
                        Files you've recently viewed
                    </small>

                </div>

                <div class="d-flex align-items-center gap-3">

                    <strong>
                        <?= htmlspecialchars($name) ?>
                    </strong>
<a href="settings.php" title="Profile Settings">
<img
                        src="<?= $profile_image ?>"
                        class="rounded-circle dashboard-avatar"
                        width="40"
                        height="40"
                        alt="Profile">
</a>

                </div>

            </div>

            <!-- EMPTY STATE -->
            <?php if ($result->num_rows === 0): ?>

                <div class="empty-state">

                    <div class="empty-state-icon">
                        <i class="bi bi-rocket-takeoff-fill"></i>
                    </div>

                    <h5>
                        No Recent Activity
                    </h5>

                    <p>
                        Files you open will appear here.
                    </p>

                </div>

            <?php else: ?>

                <?php
                $lastDate = '';
                ?>

                <?php while ($row = $result->fetch_assoc()): ?>

                    <?php
                    $date = date('Y-m-d', strtotime($row['viewed_at']));
                    ?>

                    <!-- DATE HEADERS -->
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

                        <h5 class="mt-4 mb-3 fw-bold">

                            <?= $heading ?>

                        </h5>

                    <?php endif; ?>

                    <!-- CARD -->
                    <div class="card border-0 shadow-sm p-3 mb-3 file-card">

                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

                            <!-- LEFT -->
                            <div class="d-flex align-items-center">

                                <!-- ICON -->
                                <div class="file-icon">

                                    <?= getFileIcon($row['file_type']) ?>

                                </div>

                                <!-- FILE INFO -->
                                <div>

                                    <div class="file-title">

                                        <?= htmlspecialchars($row['title']) ?>

                                    </div>

                                    <div class="meta-text">

                                        <?= strtoupper($row['file_type']) ?>

                                        •

                                        <?= round($row['file_size'] / 1024, 2) ?> KB

                                    </div>

                                </div>

                            </div>

                            <!-- RIGHT -->
                            <div class="text-end">

                                <div class="activity-time mb-2">

                                    <i class="bi bi-clock"></i>

                                    <?= date('g:i A', strtotime($row['viewed_at'])) ?>

                                </div>

                                <a
                                    href="view_file.php?id=<?= $row['id'] ?>"
                                    class="btn btn-outline-primary btn-sm">

                                    <i class="bi bi-arrow-repeat"></i>
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