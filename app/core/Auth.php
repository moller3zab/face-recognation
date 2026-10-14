<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;

class Auth
{
    public static function init(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $sessConfig = Config::get('app.session', []);
            session_name($sessConfig['name'] ?? 'FACE_ATTENDANCE_SESS');
            
            session_set_cookie_params([
                'lifetime' => $sessConfig['lifetime'] ?? 1800,
                'path' => '/',
                'domain' => '',
                'secure' => $sessConfig['secure'] ?? false,
                'httponly' => $sessConfig['httponly'] ?? true,
                'samesite' => $sessConfig['samesite'] ?? 'Lax'
            ]);

            session_start();
        }

        // Idle Timeout Check
        if (isset($_SESSION['user_id'], $_SESSION['last_activity'])) {
            $timeout = Config::get('app.session.lifetime', 1800);
            if (time() - $_SESSION['last_activity'] > $timeout) {
                self::logout();
                return;
            }
        }

        $_SESSION['last_activity'] = time();
    }

    public static function login(User $user): void
    {
        self::init();
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_role'] = $user->role;
        $_SESSION['user_identifier'] = $user->identifier;
        $_SESSION['user_photo'] = $user->photo;
        $_SESSION['last_activity'] = time();
    }

    public static function logout(): void
    {
        self::init();
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
    }

    public static function check(): bool
    {
        self::init();
        return isset($_SESSION['user_id']);
    }

    public static function id(): ?int
    {
        self::init();
        return $_SESSION['user_id'] ?? null;
    }

    public static function role(): ?string
    {
        self::init();
        return $_SESSION['user_role'] ?? null;
    }

    public static function isAdmin(): bool
    {
        return self::role() === 'admin';
    }

    public static function user(): ?array
    {
        self::init();
        if (!self::check()) return null;

        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role'],
            'identifier' => $_SESSION['user_identifier'],
            'photo' => $_SESSION['user_photo'] ?? null
        ];
    }
}
