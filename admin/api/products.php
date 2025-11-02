<?php
require __DIR__ . '/../config.php';
require_login();

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
if ($method !== 'POST') {
  echo json_encode(['ok' => false, 'error' => 'Invalid method']);
  exit;
}

$action = $_POST['action'] ?? '';
$products = read_products();

if ($action === 'add') {
  $name = trim($_POST['name'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $image = trim($_POST['image'] ?? '');

  if ($name === '' || $description === '' || $image === '') {
    echo json_encode(['ok' => false, 'error' => 'Missing fields']);
    exit;
  }

  $products[] = [
    'name' => $name,
    'description' => $description,
    'image' => $image,
  ];

  write_products($products);
  header('Location: /admin/dashboard.php');
  exit;
}

if ($action === 'edit') {
  $index = intval($_POST['index'] ?? -1);
  $name = trim($_POST['name'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $image = trim($_POST['image'] ?? '');

  if ($index < 0 || $index >= count($products)) {
    echo json_encode(['ok' => false, 'error' => 'Invalid index']);
    exit;
  }

  if ($name === '' || $description === '' || $image === '') {
    echo json_encode(['ok' => false, 'error' => 'Missing fields']);
    exit;
  }

  $products[$index] = [
    'name' => $name,
    'description' => $description,
    'image' => $image,
  ];

  write_products($products);
  header('Location: /admin/dashboard.php');
  exit;
}

if ($action === 'delete') {
  $index = intval($_POST['index'] ?? -1);
  if ($index >= 0 && $index < count($products)) {
    array_splice($products, $index, 1);
    write_products($products);
  }
  header('Location: /admin/dashboard.php');
  exit;
}

echo json_encode(['ok' => false, 'error' => 'Unknown action']);