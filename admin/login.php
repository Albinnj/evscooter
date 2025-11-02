<?php
require __DIR__ . '/config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';

  global $ADMIN_USERNAME, $ADMIN_PASSWORD_HASH;
  if ($username === $ADMIN_USERNAME && password_verify($password, $ADMIN_PASSWORD_HASH)) {
    $_SESSION['admin_logged_in'] = true;
    header('Location: /admin/dashboard.php');
    exit;
  } else {
    $error = 'Invalid credentials';
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login • Volti</title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/admin/admin.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700&family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <main>
    <div class="admin-container">
      <div class="text-center mb-20">
        <img src="/assets/images/logoev.png" alt="Volti Logo" style="height: 60px; margin-bottom: 20px;" onerror="this.src='/assets/images/logo.svg'">
      </div>
      
      <h1 class="admin-title">Admin Login</h1>
      
      <?php if ($error): ?>
        <div class="alert alert-error">
          <strong>Error:</strong> <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>
      
      <form method="post" id="loginForm">
        <div class="form-group">
          <label for="username">Username</label>
          <input class="admin-field" type="text" id="username" name="username" required placeholder="Enter your username">
        </div>
        
        <div class="form-group">
          <label for="password">Password</label>
          <input class="admin-field" type="password" id="password" name="password" required placeholder="Enter your password">
        </div>
        
        <button class="btn btn-primary" type="submit" style="width: 100%; justify-content: center;">
          <span class="span">Login to Dashboard</span>
        </button>
      </form>
      
      <div class="text-center mt-20">
        <p style="color: var(--gray-x-11-gray); font-size: var(--fs-11);">
          Secure admin access for Volti Electric Scooters
        </p>
      </div>
    </div>
  </main>

  <script>
    // Add loading state to form submission
    document.getElementById('loginForm').addEventListener('submit', function(e) {
      const button = this.querySelector('button[type="submit"]');
      const span = button.querySelector('.span');
      
      button.classList.add('loading');
      span.innerHTML = '<span class="spinner"></span> Logging in...';
      button.disabled = true;
    });
  </script>
</body>
</html>