<?php

declare(strict_types=1);

// Directory Separator Constant Shortcut
define('DS', DIRECTORY_SEPARATOR);

// Root System Directory Paths
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . DS . 'app');
define('CONFIG_PATH', ROOT_PATH . DS . 'config');
define('PUBLIC_PATH', ROOT_PATH . DS . 'public');
define('STORAGE_PATH', ROOT_PATH . DS . 'storage');
define('LOGS_PATH', STORAGE_PATH . DS . 'logs');
define('UPLOADS_PATH', STORAGE_PATH . DS . 'uploads');

// Application URL Subdirectory Base (Dynamic Resolution)
$scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
$baseUrl = rtrim(str_replace('\\', '/', $scriptDir), '/');
define('BASE_URL', $baseUrl);

// Global HTML Escape Helper Function
if (!function_exists('e')) {
    function e(mixed $value): string {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

