<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("Invalid request");
}

$user_id = $_SESSION['user_id'];
$file_id = $_GET['id'];

// get file info (to delete physical file too)
$stmt = $conn->prepare("SELECT file_name FROM uploads WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $file_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("File not found or unauthorized");
}

$file = $result->fetch_assoc();
$filePath = "../uploads/" . $file['file_name'];

// delete from DB
$stmt = $conn->prepare("DELETE FROM uploads WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $file_id, $user_id);
$stmt->execute();

// delete physical file
if (file_exists($filePath)) {
    unlink($filePath);
}

header("Location: files.php");
exit();