<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$contentFile = '../../data/content.json';

// Handle different HTTP methods
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Get current content
        if (file_exists($contentFile)) {
            $content = json_decode(file_get_contents($contentFile), true);
            header('Content-Type: application/json');
            echo json_encode($content);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Content file not found']);
        }
        break;
        
    case 'POST':
        // Update content
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON data']);
            exit;
        }
        
        // Validate required fields
        $requiredSections = ['hero', 'about', 'contact', 'company', 'meta'];
        foreach ($requiredSections as $section) {
            if (!isset($input[$section])) {
                http_response_code(400);
                echo json_encode(['error' => "Missing required section: $section"]);
                exit;
            }
        }
        
        // Validate contact fields
        if (!isset($input['contact']['phone']) || !isset($input['contact']['email']) || !isset($input['contact']['address'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required contact fields']);
            exit;
        }
        
        // Save content
        if (file_put_contents($contentFile, json_encode($input, JSON_PRETTY_PRINT))) {
            // Include and run the index update script
            include_once '../update-index.php';
            $updateResult = updateIndexContent();
            
            if (isset($updateResult['error'])) {
                // Content saved but index update failed
                echo json_encode([
                    'success' => true, 
                    'message' => 'Content updated successfully, but failed to update index page: ' . $updateResult['error']
                ]);
            } else {
                echo json_encode(['success' => true, 'message' => 'Content and index page updated successfully']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to save content']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>