<?php

declare(strict_types=1);

namespace App\Core;

class Autoloader
{
    public static function register(): void
    {
        spl_autoload_register(function (string $class) {
            $prefix = 'App\\';
            $baseDir = ROOT_PATH . DS . 'app' . DS;

            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                return;
            }

            $relativeClass = substr($class, $len);
            $parts = explode('\\', $relativeClass);
            $className = array_pop($parts);
            
            // Try lowercase directory path first (Linux compatibility)
            $dirPath = $baseDir . strtolower(implode('/', $parts));
            $file = rtrim($dirPath, '/') . '/' . $className . '.php';

            if (!file_exists($file)) {
                // Fallback to exact path
                $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            }

            if (file_exists($file)) {
                require_once $file;
            }
        });
    }
}
