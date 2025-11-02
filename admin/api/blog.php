<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

$blogPath = dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'blog.json';

function read_blogs(string $path): array {
  if (!file_exists($path)) { return []; }
  $json = file_get_contents($path);
  $data = json_decode($json, true);
  return is_array($data) ? $data : [];
}

function write_blogs(string $path, array $blogs): bool {
  if (!is_dir(dirname($path))) { mkdir(dirname($path), 0777, true); }
  $json = json_encode($blogs, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
  return file_put_contents($path, $json) !== false;
}

function sanitize_str($v) { return is_string($v) ? trim($v) : ''; }
function sanitize_int($v) { return is_numeric($v) ? (int)$v : 0; }

// Ensure only logged-in admins can mutate
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $blogs = read_blogs($blogPath);
  echo json_encode(['success' => true, 'blogs' => $blogs]);
  exit;
}

require_login();

$action = isset($_POST['action']) ? $_POST['action'] : '';
$blogs = read_blogs($blogPath);

if ($action === 'add') {
  $title = sanitize_str($_POST['title'] ?? '');
  $image = sanitize_str($_POST['image'] ?? '');
  $alt = sanitize_str($_POST['alt'] ?? '');
  $date = sanitize_str($_POST['date'] ?? ''); // YYYY-MM-DD
  $author = sanitize_str($_POST['author'] ?? '');
  $comments = sanitize_int($_POST['comments'] ?? 0);
  $link = sanitize_str($_POST['link'] ?? '#blog');

  if ($title === '' || $image === '' || $date === '') {
    echo json_encode(['success' => false, 'error' => 'Missing required fields: title, image, date']);
    exit;
  }

  $blogs[] = [
    'title' => $title,
    'image' => $image,
    'alt' => $alt ?: $title,
    'date' => $date,
    'author' => $author ?: 'Admin',
    'comments' => $comments,
    'link' => $link ?: '#blog'
  ];

  if (!write_blogs($blogPath, $blogs)) {
    echo json_encode(['success' => false, 'error' => 'Failed to write blog data']);
    exit;
  }

  include_once __DIR__ . '/../update-index.php';
  updateIndexContent();
  echo json_encode(['success' => true, 'blogs' => $blogs]);
  exit;
}

if ($action === 'edit') {
  $index = isset($_POST['index']) ? (int)$_POST['index'] : -1;
  if ($index < 0 || $index >= count($blogs)) {
    echo json_encode(['success' => false, 'error' => 'Invalid blog index']);
    exit;
  }

  $title = sanitize_str($_POST['title'] ?? $blogs[$index]['title']);
  $image = sanitize_str($_POST['image'] ?? $blogs[$index]['image']);
  $alt = sanitize_str($_POST['alt'] ?? $blogs[$index]['alt']);
  $date = sanitize_str($_POST['date'] ?? $blogs[$index]['date']);
  $author = sanitize_str($_POST['author'] ?? $blogs[$index]['author']);
  $comments = sanitize_int($_POST['comments'] ?? $blogs[$index]['comments']);
  $link = sanitize_str($_POST['link'] ?? $blogs[$index]['link']);

  $blogs[$index] = [
    'title' => $title,
    'image' => $image,
    'alt' => $alt ?: $title,
    'date' => $date,
    'author' => $author ?: 'Admin',
    'comments' => $comments,
    'link' => $link ?: '#blog'
  ];

  if (!write_blogs($blogPath, $blogs)) {
    echo json_encode(['success' => false, 'error' => 'Failed to write blog data']);
    exit;
  }

  include_once __DIR__ . '/../update-index.php';
  updateIndexContent();
  echo json_encode(['success' => true, 'blogs' => $blogs]);
  exit;
}

if ($action === 'delete') {
  $index = isset($_POST['index']) ? (int)$_POST['index'] : -1;
  if ($index < 0 || $index >= count($blogs)) {
    echo json_encode(['success' => false, 'error' => 'Invalid blog index']);
    exit;
  }

  array_splice($blogs, $index, 1);

  if (!write_blogs($blogPath, $blogs)) {
    echo json_encode(['success' => false, 'error' => 'Failed to write blog data']);
    exit;
  }

  include_once __DIR__ . '/../update-index.php';
  updateIndexContent();
  echo json_encode(['success' => true, 'blogs' => $blogs]);
  exit;
}

echo json_encode(['success' => false, 'error' => 'Unsupported action']);
exit;
?>