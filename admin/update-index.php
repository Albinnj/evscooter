<?php
// This script updates the index.html file with content from content.json
// It should be called after content updates

function updateIndexContent() {
    $contentFile = '../data/content.json';
    $indexFile = '../index.html';
    $blogFile = '../data/blog.json';
    
    if (!file_exists($contentFile)) {
        return ['error' => 'Content file not found'];
    }
    
    if (!file_exists($indexFile)) {
        return ['error' => 'Index file not found'];
    }
    
    $content = json_decode(file_get_contents($contentFile), true);
    if (!$content) {
        return ['error' => 'Invalid content data'];
    }
    
    $html = file_get_contents($indexFile);
    
    // Update meta title
    if (isset($content['meta']['title'])) {
        $html = preg_replace(
            '/<title>.*?<\/title>/',
            '<title>' . htmlspecialchars($content['meta']['title']) . '</title>',
            $html
        );
    }
    
    // Update meta description
    if (isset($content['meta']['description'])) {
        $html = preg_replace(
            '/<meta name="description" content=".*?">/',
            '<meta name="description" content="' . htmlspecialchars($content['meta']['description']) . '">',
            $html
        );
    }
    
    // Update contact phone numbers (multiple instances)
    if (isset($content['contact']['phone'])) {
        $phone = htmlspecialchars($content['contact']['phone']);
        // Update header phone
        $html = preg_replace(
            '/(<span class="span">)\+\(880\) 1812487092(<\/span>)/',
            '$1' . $phone . '$2',
            $html
        );
        // Update contact section phone
        $html = preg_replace(
            '/(<a href="tel:[^"]*" class="card-subtitle">)[^<]*(<\/a>)/',
            '$1' . $phone . '$2',
            $html
        );
        // Update footer phone
        $html = preg_replace(
            '/(<a href="tel:[^"]*" class="contact-link">)📱 [^<]*(<\/a>)/',
            '$1📱 ' . $phone . '$2',
            $html
        );
    }
    
    // Update email addresses
    if (isset($content['contact']['email'])) {
        $email = htmlspecialchars($content['contact']['email']);
        $html = preg_replace(
            '/(<a href="mailto:[^"]*" class="card-subtitle">)[^<]*(<\/a>)/',
            '$1' . $email . '$2',
            $html
        );
    }
    
    // Update address
    if (isset($content['contact']['address'])) {
        $address = htmlspecialchars($content['contact']['address']);
        $html = preg_replace(
            '/(<address class="card-subtitle">)[^<]*(<\/address>)/',
            '$1' . $address . '$2',
            $html
        );
        // Update footer address
        $html = preg_replace(
            '/(<address class="footer-text">\s*📍\s*)[^<]*(<\/address>)/',
            '$1' . $address . '.$2',
            $html
        );
    }
    
    // Update hero section title (in meta title, we already did this above)
    
    // Update about section title
    if (isset($content['about']['title'])) {
        $html = preg_replace(
            '/(<h2 class="h2 section-title">\s*Our mission is to build a cleaner world with easy access to electric mobility\s*<\/h2>)/',
            '<h2 class="h2 section-title">' . htmlspecialchars($content['about']['title']) . '</h2>',
            $html
        );
    }
    
    // Update about section description
    if (isset($content['about']['description'])) {
        $html = preg_replace(
            '/(<p class="section-text">\s*Charge your electric scooter effortlessly at home using our smart charging solutions[^<]*<\/p>)/',
            '<p class="section-text">' . htmlspecialchars($content['about']['description']) . '</p>',
            $html
        );
    }
    
    // Update services section titles
    if (isset($content['services']['section_subtitle'])) {
        $html = preg_replace(
            '/(<p class="section-subtitle has-before" id="service-label" data-reveal>)What We Do!(<\/p>)/',
            '$1' . htmlspecialchars($content['services']['section_subtitle']) . '$2',
            $html
        );
    }
    
    if (isset($content['services']['section_title'])) {
        $html = preg_replace(
            '/(<h2 class="h2 section-title" data-reveal>\s*What Advantages Will You Get Using An E-Scooter\?\s*<\/h2>)/',
            '<h2 class="h2 section-title" data-reveal>' . htmlspecialchars($content['services']['section_title']) . '</h2>',
            $html
        );
    }
    
    // Update CTA section
    if (isset($content['cta']['title'])) {
        $html = preg_replace(
            '/(<h2 class="h1 card-title">)Designed for Our Roads(<\/h2>)/',
            '$1' . htmlspecialchars($content['cta']['title']) . '$2',
            $html
        );
    }
    
    if (isset($content['cta']['description'])) {
        $html = preg_replace(
            '/(<p class="card-text">\s*Built to handle local conditions with durability and comfort\.<br>\s*Experience smooth rides tailored to your environment\.\s*<\/p>)/',
            '<p class="card-text">' . $content['cta']['description'] . '</p>',
            $html
        );
    }
    
    // Update blog section
    if (isset($content['blog']['section_subtitle'])) {
        $html = preg_replace(
            '/(<p class="section-subtitle has-before" id="blog-label">)Fresh News(<\/p>)/',
            '$1' . htmlspecialchars($content['blog']['section_subtitle']) . '$2',
            $html
        );
    }
    
    if (isset($content['blog']['section_title'])) {
        $html = preg_replace(
            '/(<h2 class="h2 section-title">)Stay updated with the latest insights and innovations in electric vehicle systems\.(<\/h2>)/',
            '$1' . htmlspecialchars($content['blog']['section_title']) . '$2',
            $html
        );
    }

    // Rebuild blog list from data/blog.json if present
    if (file_exists($blogFile)) {
        $blogs = json_decode(file_get_contents($blogFile), true);
        if (is_array($blogs)) {
            $listHtml = '<ul class="blog-list" data-reveal>';
            foreach ($blogs as $post) {
                $title = htmlspecialchars($post['title'] ?? '');
                $image = htmlspecialchars($post['image'] ?? '');
                $alt = htmlspecialchars(($post['alt'] ?? $title));
                $dateStr = htmlspecialchars($post['date'] ?? '');
                $author = htmlspecialchars($post['author'] ?? 'Admin');
                $comments = intval($post['comments'] ?? 0);
                $link = htmlspecialchars($post['link'] ?? '#blog');

                // Format display date: "DD Mon YYYY"
                $displayDate = $dateStr;
                $datetimeAttr = $dateStr;
                $dt = date_create($dateStr);
                if ($dt) {
                    $displayDate = date_format($dt, 'd M Y');
                    $datetimeAttr = date_format($dt, 'Y-m-d');
                }

                $listHtml .= '\n\n            <li>\n              <div class="blog-card">\n\n                <figure class="card-banner img-holder" style="--width: 770; --height: 550;">\n                  <img src="' . $image . '" width="770" height="550" loading="lazy"\n                    alt="' . $alt . '" class="img-cover">\n                </figure>\n\n                <div class="card-content">\n\n                  <time class="publish-date" datetime="' . $datetimeAttr . '">' . $displayDate . '</time>\n\n                  <ul class="card-meta-list">\n\n                    <li class="card-meta-item">\n                      <ion-icon name="person-outline" aria-hidden="true"></ion-icon>\n\n                      <span class="span">' . $author . '</span>\n                    </li>\n\n                    <li class="card-meta-item">\n                      <ion-icon name="chatbubble-outline" aria-hidden="true"></ion-icon>\n\n                      <span class="span">' . $comments . ' Comments</span>\n                    </li>\n\n                  </ul>\n\n                  <h3 class="h5">\n                    <a href="' . $link . '" class="card-title">\n                      ' . $title . '\n                    </a>\n                  </h3>\n\n                </div>\n\n              </div>\n            </li>';
            }
            $listHtml .= "\n\n          </ul>";

            // Replace the existing blog list block
            $html = preg_replace('/<ul class=\"blog-list\"[^>]*>.*?<\\/ul>/s', $listHtml, $html);
        }
    }
    
    // Update company information in footer
    if (isset($content['company']['name'])) {
        $html = preg_replace(
            '/(<p class="h6 has-after">)Volti\.(<\/p>)/',
            '$1' . htmlspecialchars($content['company']['name']) . '.$2',
            $html
        );
    }
    
    if (isset($content['company']['description'])) {
        $html = preg_replace(
            '/(<p class="footer-text">\s*A leading developer of top-tier commercial electric car and bike projects in the Bangladesh\.[^<]*<\/p>)/',
            '<p class="footer-text">' . htmlspecialchars($content['company']['description']) . '</p>',
            $html
        );
    }
    
    // Save the updated HTML
    if (file_put_contents($indexFile, $html)) {
        return ['success' => true, 'message' => 'Index page updated successfully'];
    } else {
        return ['error' => 'Failed to save updated index file'];
    }
}

// If called directly (not included)
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    session_start();
    
    // Check if user is logged in
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
    
    header('Content-Type: application/json');
    echo json_encode(updateIndexContent());
}
?>