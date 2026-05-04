<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$id = intval($_POST['id']);
$title = trim($_POST['title']);
$description = trim($_POST['description']);
$visibility = $_POST['visibility'];

/* VERIFY OWNERSHIP */
$stmt = $conn->prepare("SELECT id FROM uploads WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Unauthorized action");
}

/* UPDATE */
$stmt = $conn->prepare("
    UPDATE uploads 
    SET title = ?, description = ?, visibility = ?
    WHERE id = ? AND user_id = ?
");

$stmt->bind_param("sssii", $title, $description, $visibility, $id, $user_id);

if ($stmt->execute()) {
    $_SESSION['upload_success'] = "File updated successfully";
} else {
    $_SESSION['upload_error'] = "Update failed";
}

header("Location: files.php");
exit();