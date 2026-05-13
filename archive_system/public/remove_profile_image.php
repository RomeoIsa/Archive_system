<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get current image
$stmt = $conn->prepare("
    SELECT profile_image
    FROM users
    WHERE id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

$image = $user['profile_image'] ?? null;

// Delete file
if (!empty($image) && file_exists("../uploads/profiles/" . $image)) {
    unlink("../uploads/profiles/" . $image);
}

// Remove from DB
$stmt = $conn->prepare("
    UPDATE users
    SET profile_image = NULL
    WHERE id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

header("Location: settings.php?photo_removed=1");
exit();
?>