<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Repositories\FaceDataRepository;
use App\Services\SettingsService;

class FaceRegistrationController extends Controller
{
    private FaceDataRepository $faceDataRepo;
    private SettingsService $settingsService;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->faceDataRepo = new FaceDataRepository();
        $this->settingsService = new SettingsService();
    }

    public function showRegistration(): void
    {
        $user = Auth::user();
        $userId = Auth::id();
        $faceData = $this->faceDataRepo->findByUserId($userId);
        $settings = $this->settingsService->getAllSettings();

        $this->render('user.face_registration', [
            'title' => 'Register Face Biometrics',
            'user' => $user,
            'hasFaceData' => ($faceData !== null),
            'settings' => $settings,
            'cameraMode' => in_array($settings['default_camera'] ?? 'user', ['user', 'environment'], true)
                ? $settings['default_camera']
                : 'user',
            'cameraResolution' => in_array($settings['camera_resolution'] ?? '640x480', ['640x480', '1280x720'], true)
                ? $settings['camera_resolution']
                : '640x480',
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ]);
    }

    public function storeDescriptor(): void
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }

        $csrfToken = $input['csrf_token'] ?? null;
        if (!Csrf::verifyToken($csrfToken)) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Invalid security CSRF token.']);
            return;
        }

        $userId = Auth::id();
        $rawDescriptor = $input['descriptor'] ?? null;

        if (is_string($rawDescriptor)) {
            $descriptor = json_decode($rawDescriptor, true);
        } else {
            $descriptor = $rawDescriptor;
        }

        if (!is_array($descriptor) || count($descriptor) !== 128) {
            http_response_code(422);
            echo json_encode(['status' => 'error', 'message' => 'Invalid face descriptor data. Exactly 128 floating-point values required.']);
            return;
        }

        // Sanitize every float
        $sanitized = [];
        foreach ($descriptor as $val) {
            if (!is_numeric($val)) {
                http_response_code(422);
                echo json_encode(['status' => 'error', 'message' => 'Non-numeric value detected in face descriptor vector.']);
                return;
            }
            $sanitized[] = (float) $val;
        }

        $jsonStr = json_encode($sanitized);
        if ($this->faceDataRepo->saveDescriptor($userId, $jsonStr)) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Face biometric profile enrolled successfully.'
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to save face descriptor to database.']);
        }
    }

    public function resetUserFace(): void
    {
        RoleMiddleware::handle('admin');

        if (!Csrf::verifyToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('error', 'Invalid security CSRF token.');
            $this->redirect(BASE_URL . '/admin/users');
        }

        $targetUserId = (int) ($_POST['user_id'] ?? 0);
        if ($targetUserId <= 0) {
            $this->setFlash('error', 'Invalid user target specified.');
            $this->redirect(BASE_URL . '/admin/users');
        }

        if ($this->faceDataRepo->deleteByUserId($targetUserId)) {
            $this->setFlash('success', 'Face biometric data reset successfully for user.');
        } else {
            $this->setFlash('error', 'Failed to reset face biometric data.');
        }

        $this->redirect(BASE_URL . '/admin/users/show?id=' . $targetUserId);
    }
}
