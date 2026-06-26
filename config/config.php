<?php

/**
 * Application Configuration
 *
 * NOTE: BASE_PATH is already defined in public/index.php before this file is loaded.
 */

define('APP_NAME',    'ShopZone');
define('APP_VERSION', '1.0.0');

// Auto-detect APP_URL so it works in any sub-directory install
(function () {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/\\');
    define('APP_URL', $scheme . '://' . $host . $script);
})();

define('APP_PATH',    BASE_PATH . '/app');
define('CORE_PATH',   BASE_PATH . '/core');
define('VIEW_PATH',   APP_PATH  . '/Views');
define('STORAGE_PATH', BASE_PATH . '/storage/uploads');

define('DEFAULT_CONTROLLER', 'Home');
define('DEFAULT_ACTION',     'index');

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
