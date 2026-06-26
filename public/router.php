<?php
/**
 * PHP Built-in Server Router
 * Handles static files, uploaded storage files, and routes everything else to index.php
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve static files from public/ directly
$staticFile = __DIR__ . $uri;
if ($uri !== '/' && file_exists($staticFile) && !is_dir($staticFile)) {
    return false;
}

// Serve uploaded files from storage/uploads/ when URL starts with /storage/
if (str_starts_with($uri, '/storage/')) {
    $relativePath = substr($uri, strlen('/storage/'));
    $storageFile  = dirname(__DIR__) . '/storage/uploads/' . $relativePath;
    if (file_exists($storageFile) && !is_dir($storageFile)) {
        $ext  = strtolower(pathinfo($storageFile, PATHINFO_EXTENSION));
        $mime = match($ext) {
            'jpg','jpeg' => 'image/jpeg',
            'png'        => 'image/png',
            'webp'       => 'image/webp',
            'gif'        => 'image/gif',
            default      => 'application/octet-stream',
        };
        header('Content-Type: ' . $mime);
        readfile($storageFile);
        exit;
    }
}

// Everything else → front controller
require __DIR__ . '/index.php';
