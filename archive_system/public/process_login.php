<?php
session_start();
require "../config/db.php";

/*
    INPUT VALIDATION
*/
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';

if (!$email || empty($password)) {
    $_SESSION['error'] = "Invalid input";
    header("Location: login.php");
    exit();
}

/*
    FETCH USER
*/
$stmt = $conn->prepare("SELECT id, password, institution_id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Invalid email or password";
    header("Location: login.php");
    exit();
}

$user = $result->fetch_assoc();

/*
    PASSWORD VERIFY
*/
if (!password_verify($password, $user['password'])) {
    $_SESSION['error'] = "Invalid email or password";
    header("Location: login.php");
    exit();
}

/*
    🔐 SESSION SECURITY 
*/
session_regenerate_id(true);

/*
    LOGIN SUCCESS
*/
$_SESSION['user_id'] = $user['id'];
$_SESSION['institution_id'] = $user['institution_id'];

/*
    CLEANUP
*/
unset($_SESSION['error']);
unset($_SESSION['old_email']);

header("Location: dashboard.php");
exit();