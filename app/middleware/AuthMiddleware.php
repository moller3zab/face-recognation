<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\View;

class AuthMiddleware
{
    public static function handle(): bool
    {
        if (!Auth::check()) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
        return true;
    }
}
