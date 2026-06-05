<?php
session_start();
require "../config/db.php";

header('Content-Type: application/json; charset=utf-8');

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    echo json_encode(['exists' => false, 'error' => 'Forbidden']);
    exit();
}

if (!$email) {
    echo json_encode(['exists' => false, 'error' => 'Invalid email']);
    exit();
}

$check = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$check->bind_param("s", $email);
$check->execute();
$result = $check->get_result();

echo json_encode(['exists' => $result->num_rows > 0]);
exit();
