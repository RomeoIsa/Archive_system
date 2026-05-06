<?php
session_start();
require "../config/db.php";

$role = $_SESSION['role'] ?? 'student';
$themeClass = ($role === 'staff') ? 'theme-staff' : 'theme-student';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$activePage = 'saved';

$stmt = $conn->prepare("
    SELECT uploads.* 
    FROM saved 
    JOIN uploads ON saved.upload_id = uploads.id
    WHERE saved.user_id = ?
    ORDER BY saved.created_at DESC
");
$stmt->bind_param("i", $user_id);
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


            <h4><i class="bi bi-bookmarks-fill"></i>Your Saved Items</h4>

            <?php if ($result->num_rows == 0): ?>
                <p class="text-muted">No saved items yet.</p>
            <?php else: ?>

                <div class="row">
            <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm border-0 h-100 p-3 d-flex flex-column file-card">
                                <h6 class="fw-semibold mb-1"><?= htmlspecialchars($row['title']); ?></h6>
                                <p class="small text-muted mb-2">
                                    <?= htmlspecialchars(mb_strimwidth($row['description'] ?? '', 0, 120, '...')); ?>
                                </p>


                                <a href="download.php?id=<?= $row['id']; ?>" class="btn btn-primary btn-sm">
                                    Download
                                </a>

                                <button
                                    class="btn btn-danger btn-sm save-btn mt-2"
                                    data-id="<?= $row['id']; ?>">
                                    Remove
                                </button>
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
                let uploadId = btn.getAttribute('data-id');

                fetch('toggle_save.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'upload_id=' + uploadId
                    })
                    .then(() => location.reload());
            });
        });
    </script>

</body>

</html>