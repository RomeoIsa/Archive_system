<?php
session_start();
include("../config/db.php");

$email = $_POST['email'];
$password = $_POST['password'];

// Store email for repopulating form
$_SESSION['old_email'] = $email;

// Check if user exists
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Invalid email or password";
    header("Location: login.php");
    exit();
}

$user = $result->fetch_assoc();

// Verify password
if (!password_verify($password, $user['password'])) {
    $_SESSION['error'] = "Invalid email or password";
    header("Location: login.php");
    exit();
}

// SUCCESS → login user
$_SESSION['user_id'] = $user['id'];

// Clean up
unset($_SESSION['error']);
unset($_SESSION['old_email']);

// Redirect to dashboard
header("Location: dashboard.php");
exit();
?>