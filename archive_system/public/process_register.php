<?php
session_start();
include("../config/db.php");

$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
$role = $_POST['role'];
$institution = $_POST['institution'];
$level = $_POST['level'];

// Check if email exists
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    die("Email already exists");
}

// Insert user
$stmt = $conn->prepare("INSERT INTO users (name, email, password, role, institution_id, level) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssii", $name, $email, $password, $role, $institution, $level);

$stmt->execute();

// Login user
$_SESSION['user_id'] = $stmt->insert_id;

// Redirect
header("Location: dashboard.php");
exit();
?>