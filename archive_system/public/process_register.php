<?php
session_start();
require "../config/db.php";

/*
    SANITIZE INPUT
*/
$name = trim($_POST['name'] ?? '');
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password_raw = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';
$institution = intval($_POST['institution'] ?? 0);
$level = intval($_POST['level'] ?? 0);

/*
    VALIDATION
*/
if (empty($name) || !$email || empty($password_raw)) {
    die("Invalid input");
}

if (strlen($password_raw) < 6) {
    die("Password must be at least 6 characters");
}

/*
    ROLE VALIDATION (IMPORTANT)
    Prevent tampering
*/
$allowed_roles = ['student', 'staff'];

if (!in_array($role, $allowed_roles)) {
    die("Invalid role");
}

/*
    HASH PASSWORD
*/
$password = password_hash($password_raw, PASSWORD_BCRYPT);

/*
    CHECK EMAIL EXISTS
*/
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();

$result = $check->get_result();

if ($result->num_rows > 0) {
    die("Email already exists");
}

/*
    INSERT USER
*/
$stmt = $conn->prepare("
    INSERT INTO users (name, email, password, role, institution_id, level) 
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->bind_param("ssssii", $name, $email, $password, $role, $institution, $level);

$stmt->execute();

/*
    LOGIN USER
*/
session_regenerate_id(true);

$_SESSION['user_id'] = $stmt->insert_id;
$_SESSION['institution_id'] = $institution;

/*
    REDIRECT
*/
header("Location: dashboard.php");
exit();