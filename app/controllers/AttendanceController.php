<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Repositories\AttendanceRepository;
use App\Repositories\FaceDataRepository;
use App\Services\AttendanceService;
use App\Services\FaceRecognitionService;
use App\Services\SettingsService;

class AttendanceController extends Controller
{
    private AttendanceRepository $attendanceRepo;
    private FaceDataRepository $faceDataRepo;
    private FaceRecognitionService $faceRecognitionService;
    private AttendanceService $attendanceService;
    private SettingsService $settingsService;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->attendanceRepo = new AttendanceRepository();
        $this->faceDataRepo = new FaceDataRepository();
        $this->faceRecognitionService = new FaceRecognitionService();
        $this->attendanceService = new AttendanceService();
        $this->settingsService = new SettingsService();
    }

    public function showScanPage(): void
    {
        $user = Auth::user();
        $userId = Auth::id();
        $faceData = $this->faceDataRepo->findByUserId($userId);
        $settings = $this->settingsService->getAllSettings();

        // Random liveness challenge direction for head-turn check
        $livenessDirection = (rand(0, 1) === 0) ? 'left' : 'right';

        $this->render('attendance.scan', [
            'title' => 'Mark Attendance — Face Scanning',
            'user' => $user,
            'hasFaceData' => ($faceData !== null),
            'settings' => $settings,
            'cameraMode' => in_array($settings['default_camera'] ?? 'user', ['user', 'environment'], true)
                ? $settings['default_camera']
                : 'user',
            'cameraResolution' => in_array($settings['camera_resolution'] ?? '640x480', ['640x480', '1280x720'], true)
                ? $settings['camera_resolution']
                : '640x480',
            'livenessDirection' => $livenessDirection
        ]);
    }

    public function userAttendance(): void
    {
        $userId = Auth::id();
        $filters = ['user_id' => $userId];

        if (!empty($_GET['date'])) {
            $filters['date'] = trim($_GET['date']);
        }
        if (!empty($_GET['status'])) {
            $filters['status'] = trim($_GET['status']);
        }

        $records = $this->attendanceRepo->findAll($filters, 100, 0);

        $this->render('user.attendance', [
            'title' => 'My Personal Attendance History',
            'records' => $records
        ]);
    }

    public function adminAttendance(): void
    {
        RoleMiddleware::handle('admin');

        $filters = [];
        if (!empty($_GET['date'])) {
            $filters['date'] = trim($_GET['date']);
        }
        if (!empty($_GET['status'])) {
            $filters['status'] = trim($_GET['status']);
        }

        $records = $this->attendanceRepo->findAll($filters, 100, 0);

        $this->render('admin.attendance.index', [
            'title' => 'System Attendance Master Log',
            'records' => $records,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ]);
    }

    public function triggerGenerateAbsences(): void
    {
        RoleMiddleware::handle('admin');

        if (!Csrf::verifyToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('error', 'Invalid security CSRF token.');
            $this->redirect(BASE_URL . '/admin/attendance');
        }

        $targetDate = trim($_POST['target_date'] ?? date('Y-m-d'));
        $result = $this->attendanceService->generateAbsences($targetDate);

        if ($result['status'] === 'skipped') {
            $this->setFlash('error', $result['message']);
        } else {
            $this->setFlash('success', $result['message']);
        }

        $this->redirect(BASE_URL . '/admin/attendance');
    }

    public function verifyAndMark(): void
    {
        header('Content-Type: application/json');

        // Rate-limiting check for face verification
        $failedAttempts = $_SESSION['failed_face_attempts'] ?? 0;
        $lastAttemptTime = $_SESSION['last_face_attempt_time'] ?? 0;

        if ($failedAttempts >= 5 && (time() - $lastAttemptTime) < 300) {
            http_response_code(429);
            echo json_encode([
                'status' => 'error',
                'message' => 'Too many failed face verification attempts. Please wait 5 minutes before trying again.'
            ]);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }

        if (!Csrf::verifyToken($input['csrf_token'] ?? null)) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Invalid security CSRF token.']);
            return;
        }

        $userId = Auth::id();
        $faceData = $this->faceDataRepo->findByUserId($userId);

        if (!$faceData) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'You have not registered your face biometrics yet. Please register your face first.'
            ]);
            return;
        }

        $livenessPassed = !empty($input['liveness_passed']);
        if (!$livenessPassed) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Liveness verification challenge failed. Head turn motion was not detected.'
            ]);
            return;
        }

        $rawLiveDescriptor = $input['descriptor'] ?? null;
        if (is_string($rawLiveDescriptor)) {
            $liveDescriptor = json_decode($rawLiveDescriptor, true);
        } else {
            $liveDescriptor = $rawLiveDescriptor;
        }

        if (!is_array($liveDescriptor) || count($liveDescriptor) !== 128) {
            http_response_code(422);
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid face descriptor data. 128 float vector required.'
            ]);
            return;
        }

        $storedDescriptor = json_decode($faceData->faceDescriptor, true);
        if (!is_array($storedDescriptor) || count($storedDescriptor) !== 128) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Stored face biometric record is corrupted. Please re-enroll your face.'
            ]);
            return;
        }

        $threshold = (float) $this->settingsService->get('recognition_threshold', '0.60');

        $result = $this->faceRecognitionService->verifyFace($liveDescriptor, $storedDescriptor, $threshold);

        if (!$result['isMatch']) {
            $_SESSION['failed_face_attempts'] = $failedAttempts + 1;
            $_SESSION['last_face_attempt_time'] = time();

            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => "Face verification failed! Distance ({$result['distance']}) exceeded threshold ({$threshold}). Access denied."
            ]);
            return;
        }

        $_SESSION['failed_face_attempts'] = 0;

        $action = $input['action'] ?? 'auto';
        $attendanceResult = $this->attendanceService->processAttendance($userId, $result['distance'], $action);

        if ($attendanceResult['status'] === 'error') {
            http_response_code(400);
        }

        echo json_encode($attendanceResult);
    }

    public function verifyIdentity(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $now = time();
        $attempts = $_SESSION['identity_check_attempts'] ?? 0;
        $lastAttempt = $_SESSION['identity_check_last_attempt'] ?? 0;
        if ($now - $lastAttempt >= 300) {
            $attempts = 0;
            $_SESSION['identity_check_attempts'] = 0;
        }
        if ($attempts >= 5 && ($now - $lastAttempt) < 300) {
            http_response_code(429);
            echo json_encode(['status' => 'error', 'message' => 'Too many attempts. Please wait five minutes and try again.']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input) || !Csrf::verifyToken($input['csrf_token'] ?? null)) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'The security token is invalid. Refresh the page and try again.']);
            return;
        }

        $faceData = $this->faceDataRepo->findByUserId((int) Auth::id());
        if ($faceData === null) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Register your face before running identity verification.']);
            return;
        }

        $liveDescriptor = $input['descriptor'] ?? null;
        if (!is_array($liveDescriptor) || count($liveDescriptor) !== 128) {
            http_response_code(422);
            echo json_encode(['status' => 'error', 'message' => 'The live face data is invalid. Please try again.']);
            return;
        }
        foreach ($liveDescriptor as $value) {
            if (!is_numeric($value) || !is_finite((float) $value)) {
                http_response_code(422);
                echo json_encode(['status' => 'error', 'message' => 'The live face data is invalid. Please try again.']);
                return;
            }
        }

        $storedDescriptor = json_decode($faceData->faceDescriptor, true);
        if (!is_array($storedDescriptor) || count($storedDescriptor) !== 128) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'The registered face data is unavailable. Please register your face again.']);
            return;
        }
        foreach ($storedDescriptor as $value) {
            if (!is_numeric($value) || !is_finite((float) $value)) {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'The registered face data is unavailable. Please register your face again.']);
                return;
            }
        }

        $threshold = $this->settingsService->getRecognitionThreshold();
        $result = $this->faceRecognitionService->verifyFace($liveDescriptor, $storedDescriptor, $threshold);
        if (!$result['isMatch']) {
            $_SESSION['identity_check_attempts'] = $attempts + 1;
            $_SESSION['identity_check_last_attempt'] = $now;
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'The live face did not match this account.']);
            return;
        }

        $_SESSION['identity_check_attempts'] = 0;
        echo json_encode([
            'status' => 'success',
            'message' => 'Your face matched the biometric profile registered to this account.',
            'distance' => $result['distance']
        ]);
    }
}
