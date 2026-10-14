<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Logger;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryInterface;
use PDO;

class AuthService
{
    private UserRepositoryInterface $userRepo;
    private PDO $pdo;

    public function __construct(?UserRepositoryInterface $userRepo = null)
    {
        $this->userRepo = $userRepo ?? new UserRepository();
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function authenticate(string $email, string $password, string $ip): array
    {
        if ($this->isRateLimited($ip)) {
            return [
                'success' => false,
                'message' => 'Too many failed login attempts. Access temporarily locked for 15 minutes.'
            ];
        }

        $user = $this->userRepo->findByEmail($email);

        if (!$user || !password_verify($password, $user->password)) {
            $this->recordFailedAttempt($email, $ip);
            return [
                'success' => false,
                'message' => 'Invalid email address or password.'
            ];
        }

        if (!$user->isActive()) {
            return [
                'success' => false,
                'message' => 'Your account is currently inactive. Please contact system administrator.'
            ];
        }

        $this->clearFailedAttempts($email, $ip);
        Auth::login($user);

        return [
            'success' => true,
            'user' => $user,
            'redirect' => $user->isAdmin() ? BASE_URL . '/admin/dashboard' : BASE_URL . '/user/dashboard'
        ];
    }

    public function registerUser(array $data, array $files = []): array
    {
        // Enforce public registration forces role 'user' strictly on server
        $data['role'] = 'user';
        $data['status'] = 'active';

        // Hash Password
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        // Handle Optional Profile Photo Upload Safely
        if (!empty($files['photo']['name']) && $files['photo']['error'] === UPLOAD_ERR_OK) {
            $photoPath = $this->handleFileUpload($files['photo']);
            if ($photoPath) {
                $data['photo'] = $photoPath;
            }
        }

        $userId = $this->userRepo->create($data);
        $user = $this->userRepo->findById($userId);

        if ($user) {
            Logger::info("New user account registered: {$user->email} (ID: {$userId})");
            return [
                'success' => true,
                'message' => 'Account registered successfully! You can now log in.',
                'user' => $user
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to create user account. Please try again.'
        ];
    }

    public function isRateLimited(string $ip): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM `login_attempts` WHERE `ip` = :ip AND `attempted_at` > DATE_SUB(NOW(), INTERVAL 15 MINUTE)"
        );
        $stmt->execute([':ip' => $ip]);
        $attempts = (int) $stmt->fetchColumn();

        return $attempts >= 5;
    }

    private function recordFailedAttempt(string $email, string $ip): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO `login_attempts` (`email`, `ip`, `attempted_at`) VALUES (:email, :ip, NOW())"
        );
        $stmt->execute([':email' => $email, ':ip' => $ip]);
    }

    private function clearFailedAttempts(string $email, string $ip): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM `login_attempts` WHERE `ip` = :ip OR `email` = :email");
        $stmt->execute([':ip' => $ip, ':email' => $email]);
    }

    private function handleFileUpload(array $file): ?string
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if (!in_array($file['type'], $allowedTypes, true) || $file['size'] > $maxSize) {
            return null;
        }

        if (!is_dir(UPLOADS_PATH)) {
            mkdir(UPLOADS_PATH, 0755, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'user_' . uniqid() . '.' . strtolower($ext);
        $target = UPLOADS_PATH . DS . $filename;

        if (move_uploaded_file($file['tmp_name'], $target)) {
            return 'storage/uploads/' . $filename;
        }

        return null;
    }
}
