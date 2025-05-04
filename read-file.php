<?php
// read-file.php - Secure file reader for the transfer tool
// This file reads content from the workspace and returns it

// Define workspace path - must match the path in nb-transfer.php
define('WORKSPACE_PATH', 'e:/OrangeJeff');

// Only accept POST requests with JSON content
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get JSON input
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validate input
if (!isset($data['path'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing path parameter']);
    exit;
}

$filePath = $data['path'];

// Security check: ensure the path is within the workspace
$realPath = realpath($filePath);
$workspacePath = realpath(WORKSPACE_PATH);

if (!$realPath || strpos($realPath, $workspacePath) !== 0) {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit;
}

// Check if file exists and is readable
if (!file_exists($realPath) || !is_file($realPath) || !is_readable($realPath)) {
    http_response_code(404);
    echo json_encode(['error' => 'File not found or not readable']);
    exit;
}

// Set proper content type based on file extension
$extension = strtolower(pathinfo($realPath, PATHINFO_EXTENSION));
$contentTypes = [
    'php' => 'application/x-httpd-php',
    'js' => 'application/javascript',
    'css' => 'text/css',
    'html' => 'text/html',
    'htm' => 'text/html',
    'txt' => 'text/plain',
    'json' => 'application/json',
];

$contentType = isset($contentTypes[$extension]) ? $contentTypes[$extension] : 'application/octet-stream';

// Output the file content
header('Content-Type: ' . $contentType);
readfile($realPath);
exit;
?>
