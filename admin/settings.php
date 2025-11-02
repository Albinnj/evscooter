<?php
require __DIR__ . '/config.php';
require_login();

$settingsPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'settings.json';
$settings = [
  'site_name' => 'Scootify-ev',
  'tagline' => 'Scoot Your Way to Freedom',
  'logo' => './assets/images/logo.svg',
  'footer_logo' => './assets/images/footer-logo.svg',
];
if (file_exists($settingsPath)) {
  $json = file_get_contents($settingsPath);
  $data = json_decode($json, true);
  if (is_array($data)) { $settings = array_merge($settings, $data); }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Settings • Volti Admin</title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/admin/admin.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700&family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <main class="admin-wrap">
    <!-- Header Navigation -->
    <div class="admin-nav">
      <div class="nav-left">
        <img src="/assets/images/logoev.png" alt="Volti Logo" style="height: 40px;" onerror="this.src='/assets/images/logo.svg'">
        <h1 class="h3">Site Settings</h1>
      </div>
      <div class="nav-right">
        <a class="btn btn-outline" href="/" target="_blank">
          <span class="span">View Site</span>
        </a>
        <a class="btn btn-secondary" href="/admin/dashboard.php">
          <span class="span">Dashboard</span>
        </a>
        <a class="btn btn-danger" href="/admin/logout.php">
          <span class="span">Logout</span>
        </a>
      </div>
    </div>

    <!-- Settings Form -->
    <div class="admin-card">
      <div class="card-header">
        <h2 class="card-title">Website Configuration</h2>
        <div class="card-actions">
          <span style="color: var(--gray-x-11-gray); font-size: var(--fs-11);">
            Configure your site's basic information
          </span>
        </div>
      </div>

      <form method="post" action="/admin/api/settings.php" id="settingsForm" enctype="multipart/form-data">
        <div class="form-row">
          <div class="form-group">
            <label for="site-name">Site Name</label>
            <input class="admin-field" type="text" id="site-name" name="site_name" value="<?= htmlspecialchars($settings['site_name']) ?>" required placeholder="Enter site name">
          </div>
          
          <div class="form-group">
            <label for="tagline">Tagline</label>
            <input class="admin-field" type="text" id="tagline" name="tagline" value="<?= htmlspecialchars($settings['tagline']) ?>" placeholder="Enter site tagline">
          </div>
        </div>
        
        <div class="form-group">
          <label for="logo">Header Logo Path</label>
          <input class="admin-field" type="text" id="logo" name="logo" value="<?= htmlspecialchars($settings['logo']) ?>" required placeholder="./assets/images/logoev.png">
          <p style="color: var(--gray-x-11-gray); font-size: var(--fs-11); margin-top: 5px;">
            Path to the logo displayed in the website header
          </p>
          <label for="logo_file" style="margin-top: 10px;">Upload New Header Logo (SVG/PNG/JPG)</label>
          <input class="admin-field" type="file" id="logo_file" name="logo_file" accept=".svg,.png,.jpg,.jpeg">
          <p style="color: var(--gray-x-11-gray); font-size: var(--fs-11); margin-top: 5px;">
            Uploading will save the file to <code>/assets/images</code> and update the path
          </p>
        </div>
        
        <div class="form-group">
          <label for="footer-logo">Footer Logo Path</label>
          <input class="admin-field" type="text" id="footer-logo" name="footer_logo" value="<?= htmlspecialchars($settings['footer_logo']) ?>" required placeholder="./assets/images/logoev.png">
          <p style="color: var(--gray-x-11-gray); font-size: var(--fs-11); margin-top: 5px;">
            Path to the logo displayed in the website footer
          </p>
          <label for="footer_logo_file" style="margin-top: 10px;">Upload New Footer Logo (SVG/PNG/JPG)</label>
          <input class="admin-field" type="file" id="footer_logo_file" name="footer_logo_file" accept=".svg,.png,.jpg,.jpeg">
          <p style="color: var(--gray-x-11-gray); font-size: var(--fs-11); margin-top: 5px;">
            Uploading will save the file to <code>/assets/images</code> and update the path
          </p>
        </div>
        
        <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 30px;">
          <button type="button" class="btn btn-outline" onclick="resetForm()">
            <span class="span">Reset</span>
          </button>
          <button class="btn btn-success" type="submit" style="min-width: 150px;">
            <span class="span">Save Settings</span>
          </button>
        </div>
      </form>
    </div>

    <!-- Preview Section -->
    <div class="admin-card" style="margin-top: 30px;">
      <div class="card-header">
        <h2 class="card-title">Logo Preview</h2>
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
        <div class="text-center">
          <h3 style="margin-bottom: 15px; color: var(--black-coral);">Header Logo</h3>
          <div style="padding: 20px; background: var(--cultured); border-radius: var(--radius-15); min-height: 100px; display: flex; align-items: center; justify-content: center;">
            <img id="header-logo-preview" src="<?= htmlspecialchars($settings['logo']) ?>" alt="Header Logo" style="max-height: 60px; max-width: 200px;" onerror="this.src='/assets/images/logo.svg'">
          </div>
        </div>
        <div class="text-center">
          <h3 style="margin-bottom: 15px; color: var(--black-coral);">Footer Logo</h3>
          <div style="padding: 20px; background: var(--rich-black-fogra-29); border-radius: var(--radius-15); min-height: 100px; display: flex; align-items: center; justify-content: center;">
            <img id="footer-logo-preview" src="<?= htmlspecialchars($settings['footer_logo']) ?>" alt="Footer Logo" style="max-height: 60px; max-width: 200px;" onerror="this.src='/assets/images/footer-logo.svg'">
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="admin-card" style="margin-top: 30px;">
      <div class="card-header">
        <h2 class="card-title">Quick Actions</h2>
      </div>
      <div style="display: flex; gap: 15px; flex-wrap: wrap;">
        <a class="btn btn-primary" href="/admin/dashboard.php">
          <span class="span">Back to Dashboard</span>
        </a>
        <a class="btn btn-secondary" href="/" target="_blank">
          <span class="span">Preview Website</span>
        </a>
        <button class="btn btn-outline" onclick="location.reload()">
          <span class="span">Refresh Settings</span>
        </button>
      </div>
    </div>
  </main>

  <script>
    // Add loading state to form submission
    document.getElementById('settingsForm').addEventListener('submit', function(e) {
      const button = this.querySelector('button[type="submit"]');
      const span = button.querySelector('.span');
      
      button.classList.add('loading');
      span.innerHTML = '<span class="spinner"></span> Saving...';
      button.disabled = true;
    });

    // Reset form function
    function resetForm() {
      if (confirm('Are you sure you want to reset all settings to their current saved values?')) {
        location.reload();
      }
    }

    // Live preview updates (path fields)
    document.getElementById('logo').addEventListener('input', function() {
      const preview = document.getElementById('header-logo-preview');
      preview.src = this.value || '/assets/images/logo.svg';
    });

    document.getElementById('footer-logo').addEventListener('input', function() {
      const preview = document.getElementById('footer-logo-preview');
      preview.src = this.value || '/assets/images/footer-logo.svg';
    });

    // Live preview updates (file inputs)
    const headerFileInput = document.getElementById('logo_file');
    const footerFileInput = document.getElementById('footer_logo_file');

    headerFileInput && headerFileInput.addEventListener('change', function() {
      const file = this.files && this.files[0];
      if (!file) return;
      const allowed = ['image/svg+xml','image/png','image/jpeg'];
      if (!allowed.includes(file.type)) {
        alert('Invalid file type. Allowed: SVG, PNG, JPG');
        this.value = '';
        return;
      }
      const url = URL.createObjectURL(file);
      document.getElementById('header-logo-preview').src = url;
    });

    footerFileInput && footerFileInput.addEventListener('change', function() {
      const file = this.files && this.files[0];
      if (!file) return;
      const allowed = ['image/svg+xml','image/png','image/jpeg'];
      if (!allowed.includes(file.type)) {
        alert('Invalid file type. Allowed: SVG, PNG, JPG');
        this.value = '';
        return;
      }
      const url = URL.createObjectURL(file);
      document.getElementById('footer-logo-preview').src = url;
    });
  </script>
</body>
</html>