<?php

/**
 * Front Controller – single entry point for the application.
 *
 * URL format: /controller/action/param1/param2
 */

declare(strict_types=1);

// ── Bootstrap ────────────────────────────────────────────────────────────────

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/config/config.php';
require BASE_PATH . '/helpers/functions.php';

// ── Autoloader ───────────────────────────────────────────────────────────────

spl_autoload_register(function (string $class): void {
    // Map namespace prefixes to directories
    $map = [
        'Core\\'        => BASE_PATH . '/core/',
        'App\\Models\\'      => BASE_PATH . '/app/Models/',
        'App\\Controllers\\' => BASE_PATH . '/app/Controllers/',
    ];

    foreach ($map as $prefix => $dir) {
        if (str_starts_with($class, $prefix)) {
            $relative = substr($class, strlen($prefix));
            $file     = $dir . str_replace('\\', '/', $relative) . '.php';
            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
});

// ── Storage symlink (serve uploaded files) ───────────────────────────────────

// Serve uploaded files at /storage via a symlink created once at deploy time.
// Example: ln -s ../storage/uploads public/storage

// ── Dispatch ─────────────────────────────────────────────────────────────────

$router = new \Core\Router();
$router->dispatch();
