<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    exit("Unauthorized");
}

$user_id = $_SESSION['user_id'];

if (!isset($_POST['upload_id'])) {
    exit("Invalid request");
}

$upload_id = intval($_POST['upload_id']);

/*
    CHECK IF ALREADY SAVED
*/
$stmt = $conn->prepare("SELECT id FROM saved WHERE user_id = ? AND upload_id = ?");
$stmt->bind_param("ii", $user_id, $upload_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {

    // UNSAVE
    $delete = $conn->prepare("DELETE FROM saved WHERE user_id = ? AND upload_id = ?");
    $delete->bind_param("ii", $user_id, $upload_id);
    $delete->execute();

    echo "removed";

} else {

    // SAVE
    $insert = $conn->prepare("INSERT INTO saved (user_id, upload_id) VALUES (?, ?)");
    $insert->bind_param("ii", $user_id, $upload_id);
    $insert->execute();

    echo "saved";
}