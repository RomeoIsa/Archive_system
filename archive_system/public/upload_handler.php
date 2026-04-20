<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$title = $_POST['title'];
$description = $_POST['description'];

// NEW: visibility (default = private if not set)
$visibility = $_POST['visibility'] ?? 'private';

$file = $_FILES['file'];

$fileName = $file['name'];
$fileTmp = $file['tmp_name'];
$fileSize = $file['size'];
$fileError = $file['error'];

// =====================
// VALIDATION
// =====================

if ($fileError !== 0) {
    $_SESSION['upload_error'] = "File upload error";
    header("Location: dashboard.php");
    exit();
}

// allowed file types
$allowed = ['pdf', 'docx', 'jpg', 'png', 'txt'];
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

if (!in_array($fileExt, $allowed)) {
    $_SESSION['upload_error'] = "Invalid file type";
    header("Location: dashboard.php");
    exit();
}

// file size limit (5MB)
if ($fileSize > 5 * 1024 * 1024) {
    $_SESSION['upload_error'] = "File too large (max 5MB)";
    header("Location: dashboard.php");
    exit();
}

// =====================
// FILE PROCESSING
// =====================

// generate safe filename
$newFileName = uniqid("file_", true) . "." . $fileExt;

// correct path
$uploadPath = "../uploads/" . $newFileName;

// =====================
// SAVE FILE + DATABASE
// =====================

if (move_uploaded_file($fileTmp, $uploadPath)) {

    $stmt = $conn->prepare("
        INSERT INTO uploads 
        (user_id, title, description, file_name, file_type, file_size, visibility)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "issssis",
        $user_id,
        $title,
        $description,
        $newFileName,
        $fileExt,
        $fileSize,
        $visibility
    );

    if ($stmt->execute()) {
        $_SESSION['upload_success'] = "File uploaded successfully";
    } else {
        $_SESSION['upload_error'] = "Database error while saving file";
    }

} else {
    $_SESSION['upload_error'] = "Failed to save file";
}

header("Location: dashboard.php");
exit();