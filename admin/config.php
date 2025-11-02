<?php
session_start();

// Basic admin credentials. Change username/password as needed.
$ADMIN_USERNAME = 'admin';
$ADMIN_PASSWORD_HASH = '$2y$10$CfHDkndAnciCQiRzaLFcTeLS.RTW1aKpO/zvJiupx0jb0GocGGsG.'; // hash for 'admin123'

function is_logged_in(): bool {
  return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function require_login() {
  if (!is_logged_in()) {
    header('Location: /admin/login.php');
    exit;
  }
}

function products_path(): string {
  return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'products.json';
}

function read_products(): array {
  $path = products_path();
  if (!file_exists($path)) {
    return [];
  }
  $json = file_get_contents($path);
  $data = json_decode($json, true);
  return is_array($data) ? $data : [];
}

function write_products(array $products): bool {
  $path = products_path();
  if (!is_dir(dirname($path))) {
    mkdir(dirname($path), 0777, true);
  }
  $json = json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
  return file_put_contents($path, $json) !== false;
}