<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Validator;
use App\Middleware\AuthMiddleware;
use App\Repositories\FaceDataRepository;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryInterface;

class ProfileController extends Controller
{
    private UserRepositoryInterface $userRepo;
    private FaceDataRepository $faceDataRepo;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->userRepo = new UserRepository();
        $this->faceDataRepo = new FaceDataRepository();
    }

    public function showProfile(): void
    {
        $userId = Auth::id();
        $user = $this->userRepo->findById($userId);
        $faceData = $this->faceDataRepo->findByUserId($userId);

        $view = Auth::isAdmin() ? 'admin.profile' : 'user.profile';

        $this->render($view, [
            'title' => 'My Profile',
            'user' => $user,
            'hasFaceData' => ($faceData !== null),
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error'),
            'errors' => $this->getFlash('errors') ?? []
        ]);
    }

    public function updateProfile(): void
    {
        if (!Csrf::verifyToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('error', 'Invalid security CSRF token.');
            $this->redirect(BASE_URL . (Auth::isAdmin() ? '/admin/profile' : '/user/profile'));
        }

        $userId = Auth::id();
        $validator = new Validator();

        if (!$validator->validate($_POST, [
            'name' => 'required|min:3|max:100'
        ])) {
            $this->setFlash('errors', $validator->getFirstErrors());
            $this->redirect(BASE_URL . (Auth::isAdmin() ? '/admin/profile' : '/user/profile'));
        }

        $updateData = [
            'name' => trim($_POST['name']),
            'phone' => trim($_POST['phone'] ?? '')
        ];

        // Handle Photo Upload
        if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $photoPath = $this->handlePhotoUpload($_FILES['photo']);
            if ($photoPath) {
                $updateData['photo'] = $photoPath;
            }
        }

        if ($this->userRepo->update($userId, $updateData)) {
            $_SESSION['user_name'] = $updateData['name'];
            if (isset($updateData['photo'])) {
                $_SESSION['user_photo'] = $updateData['photo'];
            }
            $this->setFlash('success', 'Profile information updated successfully.');
        } else {
            $this->setFlash('error', 'Failed to update profile.');
        }

        $this->redirect(BASE_URL . (Auth::isAdmin() ? '/admin/profile' : '/user/profile'));
    }

    public function changePassword(): void
    {
        if (!Csrf::verifyToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('error', 'Invalid security CSRF token.');
            $this->redirect(BASE_URL . (Auth::isAdmin() ? '/admin/profile' : '/user/profile'));
        }

        $userId = Auth::id();
        $user = $this->userRepo->findById($userId);

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['new_password_confirm'] ?? '';

        if (!$user || !password_verify($currentPassword, $user->password)) {
            $this->setFlash('error', 'Current password entered is incorrect.');
            $this->redirect(BASE_URL . (Auth::isAdmin() ? '/admin/profile' : '/user/profile'));
        }

        $validator = new Validator();
        if (!$validator->validate($_POST, [
            'new_password' => 'required|min:6',
            'new_password_confirm' => 'required|match:new_password'
        ])) {
            $this->setFlash('errors', $validator->getFirstErrors());
            $this->redirect(BASE_URL . (Auth::isAdmin() ? '/admin/profile' : '/user/profile'));
        }

        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        if ($this->userRepo->update($userId, ['password' => $hashed])) {
            $this->setFlash('success', 'Account password changed successfully.');
        } else {
            $this->setFlash('error', 'Failed to update password.');
        }

        $this->redirect(BASE_URL . (Auth::isAdmin() ? '/admin/profile' : '/user/profile'));
    }

    private function handlePhotoUpload(array $file): ?string
    {
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowed, true) || $file['size'] > 2 * 1024 * 1024) {
            return null;
        }

        if (!is_dir(UPLOADS_PATH)) {
            mkdir(UPLOADS_PATH, 0755, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'avatar_' . uniqid() . '.' . strtolower($ext);
        $target = UPLOADS_PATH . DS . $filename;

        return move_uploaded_file($file['tmp_name'], $target) ? 'storage/uploads/' . $filename : null;
    }
}
