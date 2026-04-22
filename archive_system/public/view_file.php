<?php
session_start();
require "../config/db.php";

/*
    SAFE SESSION
*/
$user_id = $_SESSION['user_id'] ?? null;
$institution_id = $_SESSION['institution_id'] ?? null;

/*
    VALIDATE REQUEST
*/
if (!isset($_GET['id'])) {
    die("Invalid request");
}

$file_id = intval($_GET['id']);

/*
    FETCH FILE
*/
$stmt = $conn->prepare("SELECT * FROM uploads WHERE id = ?");
$stmt->bind_param("i", $file_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("File not found");
}

$file = $result->fetch_assoc();

/*
    🔐 ACCESS CONTROL FUNCTION
*/
function canAccessFile($file, $user_id, $institution_id) {

    // PRIVATE → only owner
    if ($file['visibility'] === 'private') {
        return $user_id && $file['user_id'] == $user_id;
    }

    // INSTITUTION → same institution
    if ($file['visibility'] === 'institution') {
        return $institution_id && $file['institution_id'] == $institution_id;
    }

    // PUBLIC → everyone
    if ($file['visibility'] === 'public') {
        return true;
    }

    return false;
}

/*
    CHECK ACCESS
*/
if (!canAccessFile($file, $user_id, $institution_id)) {
    http_response_code(403);
    die("Access denied");
}

/*
    FILE INFO
*/
$filePath = "../uploads/" . $file['file_name'];
$fileType = strtolower($file['file_type']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View File</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-4">

    <a href="library.php" class="btn btn-secondary mb-3">Back</a>

    <h3><?= htmlspecialchars($file['title']) ?></h3>
    <p><?= htmlspecialchars($file['description']) ?></p>

    <hr>

    <?php if (in_array($fileType, ['jpg', 'jpeg', 'png', 'gif', ])): ?>

        <!-- IMAGE PREVIEW -->
        <img src="<?= $filePath ?>" class="img-fluid">

    <?php elseif ($fileType === 'pdf'): ?>

        <!-- PDF PREVIEW -->
        <iframe 
            src="<?= $filePath ?>" 
            width="100%" 
            height="600px">
        </iframe>

    <?php else: ?>

        <!-- FALLBACK -->
        <div class="alert alert-warning">
            Preview not available for this file type.
        </div>

        <a class="btn btn-success"
           href="download.php?file=<?= $file['file_name'] ?>">
           Download File
        </a>

    <?php endif; ?>

</div>

</body>
</html>