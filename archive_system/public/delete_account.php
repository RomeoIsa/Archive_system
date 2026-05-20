<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $conn->begin_transaction();

    // 1. Delete saved files
    $stmt = $conn->prepare("DELETE FROM saved WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // 2. Delete recent activity
    $stmt = $conn->prepare("DELETE FROM recent_views WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // 3. Delete downloads (if exists)
    $stmt = $conn->prepare("DELETE FROM downloads WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // 4. Get uploads for file deletion
    $stmt = $conn->prepare("SELECT file_name FROM uploads WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        if (!empty($row['file_name']) && file_exists($row['file_name'])) {
            unlink($row['file_name']);
        }
    }

    // 5. Delete uploads
    $stmt = $conn->prepare("DELETE FROM uploads WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // 6. Delete user
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $conn->commit();

    session_unset();
    session_destroy();

    header("Location: ../login.php?msg=account_deleted");
    exit();

} catch (Exception $e) {
    $conn->rollback();
    die("Account deletion failed: " . $e->getMessage());
}