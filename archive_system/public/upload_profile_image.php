<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (!isset($_FILES['profile_image'])) {
    die("No image uploaded.");
}

$file = $_FILES['profile_image'];

$allowed_types = [
    'image/jpeg',
    'image/png',
    'image/webp'
];

if (!in_array($file['type'], $allowed_types)) {
    die("Invalid image type.");
}

if ($file['size'] > 5 * 1024 * 1024) {
    die("Image too large.");
}

// Create upload folder if needed
$upload_dir = "../uploads/profiles/";

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Get old image
$stmt = $conn->prepare("
    SELECT profile_image
    FROM users
    WHERE id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

$old_image = $user['profile_image'] ?? null;

// Generate filename
$new_filename = uniqid('profile_', true) . '.jpg';

$destination = $upload_dir . $new_filename;

// Load image based on type (requires PHP GD extension)
// If GD isn't enabled, fall back to storing the original upload (still enforces circle sizing via CSS)
if (!extension_loaded('gd') || !function_exists('imagecreatefromjpeg')) {
    $tempName = $file['tmp_name'];

    // Preserve original extension when possible
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
        $ext = 'jpg';
    }

    $new_filename = uniqid('profile_', true) . '.' . $ext;
    $destination = $upload_dir . $new_filename;

    if (!move_uploaded_file($tempName, $destination)) {
        die("Failed to upload image.");
    }

    // Delete old image
    if (!empty($old_image) && file_exists($upload_dir . $old_image)) {
        unlink($upload_dir . $old_image);
    }

    // Update database
    $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
    $stmt->bind_param("si", $new_filename, $user_id);
    $stmt->execute();

    header("Location: settings.php?photo_updated=1");
    exit();
}

$source = null;
switch ($file['type']) {

    case 'image/jpeg':
        $source = imagecreatefromjpeg($file['tmp_name']);
        break;

    case 'image/png':
        $source = imagecreatefrompng($file['tmp_name']);
        break;

    case 'image/webp':
        $source = imagecreatefromwebp($file['tmp_name']);
        break;

    default:
        die("Unsupported image.");
}


// Original dimensions
$width = imagesx($source);
$height = imagesy($source);

// Square crop
$size = min($width, $height);

$x = ($width - $size) / 2;
$y = ($height - $size) / 2;

// Final avatar size
$final_size = 300;

$final_image = imagecreatetruecolor($final_size, $final_size);

// Crop + resize
imagecopyresampled(
    $final_image,
    $source,
    0,
    0,
    $x,
    $y,
    $final_size,
    $final_size,
    $size,
    $size
);

// Save compressed JPEG
imagejpeg($final_image, $destination, 75);

// Free memory
imagedestroy($source);
imagedestroy($final_image);

// Delete old image
if (!empty($old_image) && file_exists($upload_dir . $old_image)) {
    unlink($upload_dir . $old_image);
}

// Update database
$stmt = $conn->prepare("
    UPDATE users
    SET profile_image = ?
    WHERE id = ?
");

$stmt->bind_param("si", $new_filename, $user_id);
$stmt->execute();

header("Location: settings.php?photo_updated=1");
exit();
?>