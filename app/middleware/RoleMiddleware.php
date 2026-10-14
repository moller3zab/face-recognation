<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\View;

class RoleMiddleware
{
    public static function handle(string $requiredRole): bool
    {
        AuthMiddleware::handle();

        $userRole = Auth::role();

        if ($requiredRole === 'admin' && $userRole !== 'admin') {
            http_response_code(403);
            View::render('errors.403', [
                'title' => '403 Access Denied'
            ], '');
            exit;
        }

        return true;
    }
}
