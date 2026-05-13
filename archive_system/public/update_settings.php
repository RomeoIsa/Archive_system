<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$name = trim($_POST['name']);
$matric_number = trim($_POST['matric_number']);
$department = trim($_POST['department']);
$level = trim($_POST['level']);
$theme = trim($_POST['theme']);
$language = trim($_POST['language']);

// Basic validation
if (empty($name)) {
    die("Name cannot be empty.");
}

$stmt = $conn->prepare("
    UPDATE users 
    SET 
        name = ?, 
        matric_number = ?, 
        department = ?, 
        level = ?, 
        theme = ?, 
        language = ?
    WHERE id = ?
");

$stmt->bind_param(
    "ssssssi",
    $name,
    $matric_number,
    $department,
    $level,
    $theme,
    $language,
    $user_id
);

if ($stmt->execute()) {

    // Update session name immediately
    $_SESSION['name'] = $name;

    header("Location: settings.php?success=1");
    exit();

} else {
    die("Error updating settings.");
}
?>