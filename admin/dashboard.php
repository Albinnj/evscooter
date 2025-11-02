<?php
require __DIR__ . '/config.php';
require_login();

$products = read_products();
$total_products = count($products);
// Load blogs
$blogs = [];
$blogFile = __DIR__ . '/../data/blog.json';
if (file_exists($blogFile)) {
  $parsed = json_decode(file_get_contents($blogFile), true);
  $blogs = is_array($parsed) ? $parsed : [];
}
$total_blogs = count($blogs);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard • Volti</title>
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
        <h1 class="h3">Admin Dashboard</h1>
      </div>
      <div class="nav-right">
        <a class="btn btn-outline" href="/" target="_blank">
          <span class="span">View Site</span>
        </a>
        <a class="btn btn-secondary" href="/admin/settings.php">
          <span class="span">Settings</span>
        </a>
        <a class="btn btn-danger" href="/admin/logout.php">
          <span class="span">Logout</span>
        </a>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-number"><?= $total_products ?></div>
        <div class="stat-label">Total Products</div>
      </div>
      <div class="stat-card" style="background: linear-gradient(135deg, var(--rich-black-fogra-29), var(--smoky-black));">
        <div class="stat-number">Active</div>
        <div class="stat-label">System Status</div>
      </div>
      <div class="stat-card" style="background: linear-gradient(135deg, var(--green), hsl(142, 76%, 32%));">
        <div class="stat-number">Online</div>
        <div class="stat-label">Website Status</div>
      </div>
    </div>

    <!-- Main Content Grid -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-top: 30px;">
      
      <!-- Products Section -->
      <section class="admin-card">
        <div class="card-header">
          <h2 class="card-title">Product Management</h2>
          <div class="card-actions">
            <span style="color: var(--gray-x-11-gray); font-size: var(--fs-11);">
              <?= $total_products ?> product<?= $total_products !== 1 ? 's' : '' ?>
            </span>
          </div>
        </div>
        
        <?php if (empty($products)): ?>
          <div class="text-center" style="padding: 40px 20px;">
            <p style="color: var(--gray-x-11-gray); margin-bottom: 20px;">No products found. Add your first product to get started!</p>
          </div>
        <?php else: ?>
          <div class="product-grid" style="grid-template-columns: 1fr;">
            <?php foreach ($products as $idx => $p): ?>
              <div class="product-item">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                  <div style="flex: 1;">
                    <h3 class="product-name"><?= htmlspecialchars($p['name'] ?? 'Unnamed Product') ?></h3>
                    <p style="color: var(--black-coral); font-size: var(--fs-10); margin-bottom: 10px;">
                      <?= htmlspecialchars($p['description'] ?? 'No description') ?>
                    </p>
                    <p style="color: var(--gray-x-11-gray); font-size: var(--fs-11);">
                      <strong>Image:</strong> <?= htmlspecialchars($p['image'] ?? 'No image') ?>
                    </p>
                  </div>
                  <div style="display:flex; gap:10px; margin-left: 15px;">
                    <button class="btn btn-secondary" type="button" onclick="toggleEditForm(<?= $idx ?>)">
                      <span class="span">Edit</span>
                    </button>
                    <form method="post" action="/admin/api/products.php">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="index" value="<?= $idx ?>">
                      <button class="btn btn-danger" type="submit" onclick="return confirm('Are you sure you want to delete this product?')">
                        <span class="span">Delete</span>
                      </button>
                    </form>
                  </div>
                </div>

                <!-- Inline Edit Form -->
                <form method="post" action="/admin/api/products.php" id="edit-form-<?= $idx ?>" class="hidden">
                  <input type="hidden" name="action" value="edit">
                  <input type="hidden" name="index" value="<?= $idx ?>">

                  <div class="form-group">
                    <label for="edit-name-<?= $idx ?>">Product Name</label>
                    <input class="admin-field" type="text" id="edit-name-<?= $idx ?>" name="name" value="<?= htmlspecialchars($p['name'] ?? '') ?>" required>
                  </div>
                  
                  <div class="form-group">
                    <label for="edit-description-<?= $idx ?>">Description</label>
                    <textarea class="admin-field" id="edit-description-<?= $idx ?>" name="description" rows="4" required style="resize: vertical; min-height: 100px;"><?= htmlspecialchars($p['description'] ?? '') ?></textarea>
                  </div>
                  
                  <div class="form-group">
                    <label for="edit-image-<?= $idx ?>">Image Path</label>
                    <input class="admin-field" type="text" id="edit-image-<?= $idx ?>" name="image" value="<?= htmlspecialchars($p['image'] ?? '') ?>" required>
                  </div>

                  <div style="display:flex; gap:10px;">
                    <button class="btn btn-success" type="submit">
                      <span class="span">Save Changes</span>
                    </button>
                    <button class="btn btn-outline" type="button" onclick="toggleEditForm(<?= $idx ?>)">
                      <span class="span">Cancel</span>
                    </button>
                  </div>
                </form>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>

      <!-- Add Product Section -->
      <section class="admin-card">
        <div class="card-header">
          <h2 class="card-title">Add New Product</h2>
        </div>
        
        <form method="post" action="/admin/api/products.php" id="addProductForm">
          <input type="hidden" name="action" value="add">
          
          <div class="form-group">
            <label for="product-name">Product Name</label>
            <input class="admin-field" type="text" id="product-name" name="name" required placeholder="Enter product name">
          </div>
          
          <div class="form-group">
            <label for="product-description">Description</label>
            <textarea class="admin-field" id="product-description" name="description" rows="4" required placeholder="Enter product description" style="resize: vertical; min-height: 100px;"></textarea>
          </div>
          
          <div class="form-group">
            <label for="product-image">Image Path</label>
            <input class="admin-field" type="text" id="product-image" name="image" required placeholder="./assets/images/product.jpg">
            <p style="color: var(--gray-x-11-gray); font-size: var(--fs-11); margin-top: 5px;">
              Enter the relative path to the product image
            </p>
          </div>
          
          <button class="btn btn-success" type="submit" style="width: 100%; justify-content: center;">
            <span class="span">Add Product</span>
          </button>
        </form>
      </section>
    </div>

    <!-- Blog Management Grid -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-top: 30px;">
      <!-- Blogs Section -->
      <section class="admin-card">
        <div class="card-header">
          <h2 class="card-title">Blog Management</h2>
          <div class="card-actions">
            <span style="color: var(--gray-x-11-gray); font-size: var(--fs-11);">
              <?= $total_blogs ?> blog<?= $total_blogs !== 1 ? 's' : '' ?>
            </span>
          </div>
        </div>

        <?php if (empty($blogs)): ?>
          <div class="text-center" style="padding: 40px 20px;">
            <p style="color: var(--gray-x-11-gray); margin-bottom: 20px;">No blog posts found. Add your first post to get started!</p>
          </div>
        <?php else: ?>
          <div class="product-grid" style="grid-template-columns: 1fr;">
            <?php foreach ($blogs as $idx => $b): ?>
              <div class="product-item">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                  <div style="flex: 1;">
                    <h3 class="product-name"><?= htmlspecialchars($b['title'] ?? 'Untitled') ?></h3>
                    <p style="color: var(--gray-x-11-gray); font-size: var(--fs-11); margin-bottom: 8px;">
                      <strong>Date:</strong> <?= htmlspecialchars($b['date'] ?? '') ?>
                      &nbsp;•&nbsp; <strong>Author:</strong> <?= htmlspecialchars($b['author'] ?? 'Admin') ?>
                      &nbsp;•&nbsp; <strong>Comments:</strong> <?= (int)($b['comments'] ?? 0) ?>
                    </p>
                    <p style="color: var(--black-coral); font-size: var(--fs-10); margin-bottom: 10px;">
                      <strong>Image:</strong> <?= htmlspecialchars($b['image'] ?? '') ?>
                    </p>
                  </div>
                  <div style="display:flex; gap:10px; margin-left: 15px;">
                    <button class="btn btn-secondary" type="button" onclick="toggleBlogEditForm(<?= $idx ?>)">
                      <span class="span">Edit</span>
                    </button>
                    <form method="post" action="/admin/api/blog.php">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="index" value="<?= $idx ?>">
                      <button class="btn btn-danger" type="submit" onclick="return confirm('Delete this blog post?')">
                        <span class="span">Delete</span>
                      </button>
                    </form>
                  </div>
                </div>

                <!-- Inline Blog Edit Form -->
                <form method="post" action="/admin/api/blog.php" id="blog-edit-form-<?= $idx ?>" class="hidden">
                  <input type="hidden" name="action" value="edit">
                  <input type="hidden" name="index" value="<?= $idx ?>">

                  <div class="form-group">
                    <label for="blog-title-<?= $idx ?>">Title</label>
                    <input class="admin-field" type="text" id="blog-title-<?= $idx ?>" name="title" value="<?= htmlspecialchars($b['title'] ?? '') ?>" required>
                  </div>

                  <div class="form-group">
                    <label for="blog-date-<?= $idx ?>">Date</label>
                    <input class="admin-field" type="date" id="blog-date-<?= $idx ?>" name="date" value="<?= htmlspecialchars($b['date'] ?? '') ?>" required>
                  </div>

                  <div class="form-group">
                    <label for="blog-author-<?= $idx ?>">Author</label>
                    <input class="admin-field" type="text" id="blog-author-<?= $idx ?>" name="author" value="<?= htmlspecialchars($b['author'] ?? 'Admin') ?>">
                  </div>

                  <div class="form-group">
                    <label for="blog-comments-<?= $idx ?>">Comments</label>
                    <input class="admin-field" type="number" id="blog-comments-<?= $idx ?>" name="comments" value="<?= (int)($b['comments'] ?? 0) ?>" min="0">
                  </div>

                  <div class="form-group">
                    <label for="blog-image-<?= $idx ?>">Image Path</label>
                    <input class="admin-field" type="text" id="blog-image-<?= $idx ?>" name="image" value="<?= htmlspecialchars($b['image'] ?? '') ?>" required>
                  </div>

                  <div class="form-group">
                    <label for="blog-alt-<?= $idx ?>">Image Alt Text</label>
                    <input class="admin-field" type="text" id="blog-alt-<?= $idx ?>" name="alt" value="<?= htmlspecialchars($b['alt'] ?? '') ?>">
                  </div>

                  <div class="form-group">
                    <label for="blog-link-<?= $idx ?>">Link URL</label>
                    <input class="admin-field" type="text" id="blog-link-<?= $idx ?>" name="link" value="<?= htmlspecialchars($b['link'] ?? '#blog') ?>">
                  </div>

                  <div style="display:flex; gap:10px;">
                    <button class="btn btn-success" type="submit">
                      <span class="span">Save Changes</span>
                    </button>
                    <button class="btn btn-outline" type="button" onclick="toggleBlogEditForm(<?= $idx ?>)">
                      <span class="span">Cancel</span>
                    </button>
                  </div>
                </form>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>

      <!-- Add Blog Section -->
      <section class="admin-card">
        <div class="card-header">
          <h2 class="card-title">Add New Blog Post</h2>
        </div>

        <form method="post" action="/admin/api/blog.php" id="addBlogForm">
          <input type="hidden" name="action" value="add">

          <div class="form-group">
            <label for="new-blog-title">Title</label>
            <input class="admin-field" type="text" id="new-blog-title" name="title" required placeholder="Enter blog title">
          </div>

          <div class="form-group">
            <label for="new-blog-date">Date</label>
            <input class="admin-field" type="date" id="new-blog-date" name="date" required>
          </div>

          <div class="form-group">
            <label for="new-blog-author">Author</label>
            <input class="admin-field" type="text" id="new-blog-author" name="author" placeholder="Admin">
          </div>

          <div class="form-group">
            <label for="new-blog-comments">Comments</label>
            <input class="admin-field" type="number" id="new-blog-comments" name="comments" min="0" value="0">
          </div>

          <div class="form-group">
            <label for="new-blog-image">Image Path</label>
            <input class="admin-field" type="text" id="new-blog-image" name="image" required placeholder="./assets/images/blog-4.jpg">
          </div>

          <div class="form-group">
            <label for="new-blog-alt">Image Alt Text</label>
            <input class="admin-field" type="text" id="new-blog-alt" name="alt" placeholder="Descriptive alt text">
          </div>

          <div class="form-group">
            <label for="new-blog-link">Link URL</label>
            <input class="admin-field" type="text" id="new-blog-link" name="link" placeholder="#blog">
          </div>

          <button class="btn btn-success" type="submit" style="width: 100%; justify-content: center;">
            <span class="span">Add Blog Post</span>
          </button>
        </form>
      </section>
    </div>

    <!-- Content Management Section -->
    <div class="admin-card" style="margin-top: 30px;">
      <div class="card-header">
        <h2 class="card-title">Website Content Management</h2>
        <p style="color: var(--gray-x-11-gray); font-size: var(--fs-11); margin-top: 5px;">
          Edit the main content displayed on your website's homepage
        </p>
      </div>
      
      <form id="contentForm">
        <!-- Hero Section -->
        <div class="form-section">
          <h3 class="section-title">Hero Section</h3>
          <div class="form-group">
            <label for="hero-title">Main Title</label>
            <input class="admin-field" type="text" id="hero-title" name="hero_title" placeholder="Book Your Eco-Friendly Ride">
          </div>
          <div class="form-group">
            <label for="hero-subtitle">Subtitle/Description</label>
            <textarea class="admin-field" id="hero-subtitle" name="hero_subtitle" rows="3" placeholder="Charge your electric scooter effortlessly..."></textarea>
          </div>
          <div class="form-group">
            <label for="hero-button">Button Text</label>
            <input class="admin-field" type="text" id="hero-button" name="hero_button" placeholder="Book Now">
          </div>
        </div>

        <!-- About Section -->
        <div class="form-section">
          <h3 class="section-title">About Section</h3>
          <div class="form-group">
            <label for="about-title">About Title</label>
            <input class="admin-field" type="text" id="about-title" name="about_title" placeholder="Our mission is to build a cleaner world...">
          </div>
          <div class="form-group">
            <label for="about-description">About Description</label>
            <textarea class="admin-field" id="about-description" name="about_description" rows="3" placeholder="Charge your electric scooter effortlessly..."></textarea>
          </div>
        </div>

        <!-- Contact Information -->
        <div class="form-section">
          <h3 class="section-title">Contact Information</h3>
          <div class="form-group">
            <label for="contact-phone">Phone Number</label>
            <input class="admin-field" type="text" id="contact-phone" name="contact_phone" placeholder="+(880) 1812487092">
          </div>
          <div class="form-group">
            <label for="contact-email">Email Address</label>
            <input class="admin-field" type="email" id="contact-email" name="contact_email" placeholder="info.volti@gmail.com">
          </div>
          <div class="form-group">
            <label for="contact-address">Address</label>
            <input class="admin-field" type="text" id="contact-address" name="contact_address" placeholder="Chittagong, Bangladesh-1000">
          </div>
        </div>

        <!-- Company Information -->
        <div class="form-section">
          <h3 class="section-title">Company Information</h3>
          <div class="form-group">
            <label for="company-name">Company Name</label>
            <input class="admin-field" type="text" id="company-name" name="company_name" placeholder="Volti">
          </div>
          <div class="form-group">
            <label for="company-description">Company Description</label>
            <textarea class="admin-field" id="company-description" name="company_description" rows="4" placeholder="A leading developer of top-tier commercial electric car..."></textarea>
          </div>
        </div>

        <!-- SEO Meta Information -->
        <div class="form-section">
          <h3 class="section-title">SEO & Meta Information</h3>
          <div class="form-group">
            <label for="meta-title">Page Title</label>
            <input class="admin-field" type="text" id="meta-title" name="meta_title" placeholder="Shoham mallick || Volti - Book Your Eco-Friendly Ride">
          </div>
          <div class="form-group">
            <label for="meta-description">Meta Description</label>
            <textarea class="admin-field" id="meta-description" name="meta_description" rows="2" placeholder="This is a vehicle charging html template..."></textarea>
          </div>
        </div>

        <!-- Section Titles -->
        <div class="form-section">
          <h3 class="section-title">Section Titles</h3>
          <div class="form-group">
            <label for="services-title">Services Section Title</label>
            <input class="admin-field" type="text" id="services-title" name="services_title" placeholder="What Advantages Will You Get Using An E-Scooter?">
          </div>
          <div class="form-group">
            <label for="services-subtitle">Services Section Subtitle</label>
            <input class="admin-field" type="text" id="services-subtitle" name="services_subtitle" placeholder="What We Do!">
          </div>
          <div class="form-group">
            <label for="blog-title">Blog Section Title</label>
            <input class="admin-field" type="text" id="blog-title" name="blog_title" placeholder="Stay updated with the latest insights...">
          </div>
          <div class="form-group">
            <label for="blog-subtitle">Blog Section Subtitle</label>
            <input class="admin-field" type="text" id="blog-subtitle" name="blog_subtitle" placeholder="Fresh News">
          </div>
        </div>

        <!-- CTA Section -->
        <div class="form-section">
          <h3 class="section-title">Call-to-Action Section</h3>
          <div class="form-group">
            <label for="cta-title">CTA Title</label>
            <input class="admin-field" type="text" id="cta-title" name="cta_title" placeholder="Designed for Our Roads">
          </div>
          <div class="form-group">
            <label for="cta-description">CTA Description</label>
            <textarea class="admin-field" id="cta-description" name="cta_description" rows="3" placeholder="Built to handle local conditions with durability..."></textarea>
          </div>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
          <button class="btn btn-success" type="submit" style="flex: 1;">
            <span class="span">Save All Changes</span>
          </button>
          <button class="btn btn-outline" type="button" onclick="loadContent()" style="flex: 1;">
            <span class="span">Reset to Current</span>
          </button>
        </div>
      </form>
    </div>

    <!-- Quick Actions -->
    <div class="admin-card" style="margin-top: 30px;">
      <div class="card-header">
        <h2 class="card-title">Quick Actions</h2>
      </div>
      <div style="display: flex; gap: 15px; flex-wrap: wrap;">
        <a class="btn btn-primary" href="/admin/settings.php">
          <span class="span">Site Settings</span>
        </a>
        <a class="btn btn-secondary" href="/" target="_blank">
          <span class="span">Preview Website</span>
        </a>
        <button class="btn btn-outline" onclick="location.reload()">
          <span class="span">Refresh Dashboard</span>
        </button>
      </div>
    </div>
  </main>

  <script>
    // Add loading state to form submission
    document.getElementById('addProductForm').addEventListener('submit', function(e) {
      const button = this.querySelector('button[type="submit"]');
      const span = button.querySelector('.span');
      
      button.classList.add('loading');
      span.innerHTML = '<span class="spinner"></span> Adding Product...';
      button.disabled = true;
    });

    // Auto-resize textarea
    const textarea = document.getElementById('product-description');
    textarea.addEventListener('input', function() {
      this.style.height = 'auto';
      this.style.height = this.scrollHeight + 'px';
    });

    // Toggle edit forms
    function toggleEditForm(index) {
      const form = document.getElementById('edit-form-' + index);
      if (form) {
        form.classList.toggle('hidden');
      }
    }

    // Toggle blog edit forms
    function toggleBlogEditForm(index) {
      const form = document.getElementById('blog-edit-form-' + index);
      if (form) {
        form.classList.toggle('hidden');
      }
    }

    // Content Management Functions
    async function loadContent() {
      try {
        const response = await fetch('/admin/api/content.php');
        if (response.ok) {
          const content = await response.json();
          
          // Populate form fields
          document.getElementById('hero-title').value = content.hero?.title || '';
          document.getElementById('hero-subtitle').value = content.hero?.subtitle || '';
          document.getElementById('hero-button').value = content.hero?.button_text || '';
          
          document.getElementById('about-title').value = content.about?.title || '';
          document.getElementById('about-description').value = content.about?.description || '';
          
          document.getElementById('contact-phone').value = content.contact?.phone || '';
          document.getElementById('contact-email').value = content.contact?.email || '';
          document.getElementById('contact-address').value = content.contact?.address || '';
          
          document.getElementById('company-name').value = content.company?.name || '';
          document.getElementById('company-description').value = content.company?.description || '';
          
          document.getElementById('meta-title').value = content.meta?.title || '';
          document.getElementById('meta-description').value = content.meta?.description || '';
          
          document.getElementById('services-title').value = content.services?.section_title || '';
          document.getElementById('services-subtitle').value = content.services?.section_subtitle || '';
          document.getElementById('blog-title').value = content.blog?.section_title || '';
          document.getElementById('blog-subtitle').value = content.blog?.section_subtitle || '';
          
          document.getElementById('cta-title').value = content.cta?.title || '';
          document.getElementById('cta-description').value = content.cta?.description || '';
        }
      } catch (error) {
        console.error('Error loading content:', error);
        alert('Failed to load content. Please try again.');
      }
    }

    // Handle content form submission
    document.getElementById('contentForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      
      const button = this.querySelector('button[type="submit"]');
      const span = button.querySelector('.span');
      const originalText = span.textContent;
      
      // Show loading state
      button.classList.add('loading');
      span.innerHTML = '<span class="spinner"></span> Saving Changes...';
      button.disabled = true;
      
      try {
        // Collect form data
        const formData = {
          hero: {
            title: document.getElementById('hero-title').value,
            subtitle: document.getElementById('hero-subtitle').value,
            button_text: document.getElementById('hero-button').value
          },
          about: {
            title: document.getElementById('about-title').value,
            description: document.getElementById('about-description').value
          },
          contact: {
            phone: document.getElementById('contact-phone').value,
            email: document.getElementById('contact-email').value,
            address: document.getElementById('contact-address').value
          },
          company: {
            name: document.getElementById('company-name').value,
            description: document.getElementById('company-description').value
          },
          meta: {
            title: document.getElementById('meta-title').value,
            description: document.getElementById('meta-description').value
          },
          services: {
            section_title: document.getElementById('services-title').value,
            section_subtitle: document.getElementById('services-subtitle').value
          },
          blog: {
            section_title: document.getElementById('blog-title').value,
            section_subtitle: document.getElementById('blog-subtitle').value
          },
          cta: {
            title: document.getElementById('cta-title').value,
            description: document.getElementById('cta-description').value
          }
        };
        
        const response = await fetch('/admin/api/content.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        if (response.ok) {
          alert('Content updated successfully!');
        } else {
          throw new Error(result.error || 'Failed to save content');
        }
      } catch (error) {
        console.error('Error saving content:', error);
        alert('Failed to save content: ' + error.message);
      } finally {
        // Reset button state
        button.classList.remove('loading');
        span.textContent = originalText;
        button.disabled = false;
      }
    });

    // Load content when page loads
    document.addEventListener('DOMContentLoaded', function() {
      loadContent();
    });

    // Add loading state to Add Blog form
    const addBlogForm = document.getElementById('addBlogForm');
    if (addBlogForm) {
      addBlogForm.addEventListener('submit', function() {
        const button = this.querySelector('button[type="submit"]');
        const span = button.querySelector('.span');
        button.classList.add('loading');
        span.innerHTML = '<span class="spinner"></span> Adding Blog...';
        button.disabled = true;
      });
    }
  </script>
</body>
</html>