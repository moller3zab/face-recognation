<?php

declare(strict_types=1);

namespace App\Core;

class View
{
    public static function render(string $viewPath, array $data = [], string $layout = 'app'): void
    {
        extract($data);

        // Sanitize helper function available in all views
        if (!function_exists('e')) {
            function e(mixed $value): string {
                return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
            }
        }

        ob_start();
        $viewFile = APP_PATH . DS . 'views' . DS . str_replace('.', DS, $viewPath) . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            throw new \RuntimeException("View file not found: {$viewPath}");
        }
        $content = ob_get_clean();

        if ($layout === '' || $layout === null) {
            echo $content;
            return;
        }

        $layoutFile = APP_PATH . DS . 'views' . DS . 'layouts' . DS . $layout . '.php';
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }
}
