<?php

declare(strict_types=1);

namespace App\Core;

class Config
{
    private static array $configs = [];

    public static function load(string $file): array
    {
        if (!isset(self::$configs[$file])) {
            $filePath = CONFIG_PATH . DS . $file . '.php';
            if (file_exists($filePath)) {
                self::$configs[$file] = require $filePath;
            } else {
                self::$configs[$file] = [];
            }
        }

        return self::$configs[$file];
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $parts = explode('.', $key);
        $file = array_shift($parts);

        $config = self::load($file);

        foreach ($parts as $part) {
            if (is_array($config) && isset($config[$part])) {
                $config = $config[$part];
            } else {
                return $default;
            }
        }

        return $config;
    }
}
