<?php
require __DIR__ . '/../config.php';
require_login();

$settingsPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'settings.json';
$imagesDir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images';
if (!is_dir($imagesDir)) {
  mkdir($imagesDir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: /admin/settings.php');
  exit;
}

$site_name = trim($_POST['site_name'] ?? '');
$tagline = trim($_POST['tagline'] ?? '');
$logo = trim($_POST['logo'] ?? '');
$footer_logo = trim($_POST['footer_logo'] ?? '');

if ($site_name === '' || $logo === '' || $footer_logo === '') {
  header('Location: /admin/settings.php');
  exit;
}

// Handle optional logo file uploads
$allowedExt = ['svg', 'png', 'jpg', 'jpeg'];
$maxSize = 5 * 1024 * 1024; // 5MB

// Helper to save an uploaded file
$saveUpload = function($fileKey, $prefix) use ($imagesDir, $allowedExt, $maxSize) {
  if (!isset($_FILES[$fileKey]) || !is_array($_FILES[$fileKey])) return null;
  $file = $_FILES[$fileKey];
  if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) return null;
  if ($file['size'] <= 0 || $file['size'] > $maxSize) return null;

  $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
  if (!in_array($ext, $allowedExt, true)) return null;

  // Generate a safe unique filename
  try { $rand = bin2hex(random_bytes(4)); } catch (Throwable $e) { $rand = uniqid(); }
  $basename = $prefix . '_' . date('Ymd_His') . '_' . $rand . '.' . $ext;
  $target = $imagesDir . DIRECTORY_SEPARATOR . $basename;
  if (move_uploaded_file($file['tmp_name'], $target)) {
    // Return web path (root-relative)
    return '/assets/images/' . $basename;
  }
  return null;
};

// Save header logo upload
$newLogoPath = $saveUpload('logo_file', 'logo');
if ($newLogoPath) { $logo = $newLogoPath; }

// Save footer logo upload
$newFooterLogoPath = $saveUpload('footer_logo_file', 'footer_logo');
if ($newFooterLogoPath) { $footer_logo = $newFooterLogoPath; }

$settings = [
  'site_name' => $site_name,
  'tagline' => $tagline,
  'logo' => $logo,
  'footer_logo' => $footer_logo,
];

if (!is_dir(dirname($settingsPath))) {
  mkdir(dirname($settingsPath), 0777, true);
}

file_put_contents($settingsPath, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

header('Location: /admin/settings.php');
exit;