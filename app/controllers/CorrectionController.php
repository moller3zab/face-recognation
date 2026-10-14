<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Database;
use App\Core\Validator;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Repositories\AlertRepository;
use App\Repositories\AttendanceRepository;
use App\Repositories\CorrectionRepository;
use App\Services\AttendanceRules;
use App\Services\SettingsService;
use PDO;

class CorrectionController extends Controller
{
    private CorrectionRepository $correctionRepo;
    private AttendanceRepository $attendanceRepo;
    private AlertRepository $alertRepo;
    private SettingsService $settingsService;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->correctionRepo = new CorrectionRepository();
        $this->attendanceRepo = new AttendanceRepository();
        $this->alertRepo = new AlertRepository();
        $this->settingsService = new SettingsService();
    }

    public function userIndex(): void
    {
        $userId = Auth::id();
        $requests = $this->correctionRepo->findAll(['user_id' => $userId], 100, 0);

        $this->render('user.corrections.index', [
            'title' => 'My Correction Requests',
            'requests' => $requests,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ]);
    }

    public function create(): void
    {
        $userId = Auth::id();
        $attendanceId = (int) ($_GET['attendance_id'] ?? 0);
        
        $userRecords = $this->attendanceRepo->findAll(['user_id' => $userId], 30, 0);
        $selectedRecord = null;

        if ($attendanceId > 0) {
            $selectedRecord = $this->attendanceRepo->findById($attendanceId);
            if (!$selectedRecord || $selectedRecord->userId !== $userId) {
                $this->setFlash('error', 'Unauthorized or invalid attendance record selected.');
                $this->redirect(BASE_URL . '/corrections');
            }
        }

        $this->render('user.corrections.create', [
            'title' => 'Request Attendance Correction',
            'userRecords' => $userRecords,
            'selectedRecord' => $selectedRecord,
            'errors' => $this->getFlash('errors') ?? []
        ]);
    }

    public function store(): void
    {
        if (!Csrf::verifyToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('error', 'Invalid security CSRF token.');
            $this->redirect(BASE_URL . '/corrections/create');
        }

        $userId = Auth::id();
        $validator = new Validator();

        if (!$validator->validate($_POST, [
            'attendance_id' => 'required',
            'reason' => 'required|min:5'
        ])) {
            $this->setFlash('errors', $validator->getFirstErrors());
            $this->redirect(BASE_URL . '/corrections/create?attendance_id=' . ($_POST['attendance_id'] ?? ''));
        }

        $attendanceId = (int) $_POST['attendance_id'];
        $attendance = $this->attendanceRepo->findById($attendanceId);

        if (!$attendance || $attendance->userId !== $userId) {
            $this->setFlash('error', 'Unauthorized action on attendance record.');
            $this->redirect(BASE_URL . '/corrections');
        }

        $reqCheckIn = !empty($_POST['requested_check_in']) ? trim($_POST['requested_check_in']) : null;
        $reqCheckOut = !empty($_POST['requested_check_out']) ? trim($_POST['requested_check_out']) : null;
        $reason = trim($_POST['reason']);

        $requestId = $this->correctionRepo->create([
            'user_id' => $userId,
            'attendance_id' => $attendanceId,
            'requested_check_in' => $reqCheckIn,
            'requested_check_out' => $reqCheckOut,
            'reason' => $reason
        ]);

        if ($requestId) {
            $this->setFlash('success', 'Correction request submitted successfully for admin review.');
        } else {
            $this->setFlash('error', 'Failed to submit correction request.');
        }

        $this->redirect(BASE_URL . '/corrections');
    }

    public function adminIndex(): void
    {
        RoleMiddleware::handle('admin');

        $status = $_GET['status'] ?? null;
        $filters = [];
        if (!empty($status)) {
            $filters['status'] = trim($status);
        }

        $requests = $this->correctionRepo->findAll($filters, 100, 0);

        $this->render('admin.corrections.index', [
            'title' => 'Manage Attendance Correction Requests',
            'requests' => $requests,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ]);
    }

    public function review(): void
    {
        RoleMiddleware::handle('admin');

        if (!Csrf::verifyToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('error', 'Invalid security CSRF token.');
            $this->redirect(BASE_URL . '/admin/corrections');
        }

        $requestId = (int) ($_POST['request_id'] ?? 0);
        $actionStatus = $_POST['status'] ?? ''; // 'Approved' or 'Rejected'
        $adminNote = trim($_POST['admin_note'] ?? '');
        $adminId = Auth::id();

        if (!in_array($actionStatus, ['Approved', 'Rejected'], true)) {
            $this->setFlash('error', 'Invalid action specified.');
            $this->redirect(BASE_URL . '/admin/corrections');
        }

        $request = $this->correctionRepo->findById($requestId);
        if (!$request || $request->status !== 'Pending') {
            $this->setFlash('error', 'Correction request not found or already reviewed.');
            $this->redirect(BASE_URL . '/admin/corrections');
        }

        $pdo = Database::getInstance()->getConnection();

        try {
            $pdo->beginTransaction();

            // 1. Update correction request status
            $this->correctionRepo->updateStatus($requestId, $actionStatus, $adminNote, $adminId);

            // 2. If approved, update attendance record
            if ($actionStatus === 'Approved') {
                $attendance = $this->attendanceRepo->findById($request->attendanceId);
                if ($attendance) {
                    $newCheckIn = $request->requestedCheckIn ?? $attendance->checkIn;
                    $newCheckOut = $request->requestedCheckOut ?? $attendance->checkOut;

                    $workingStart = $this->settingsService->get('working_start', '09:00');
                    $lateThreshold = (int) $this->settingsService->get('late_threshold_minutes', '15');

                    $newStatus = $attendance->status;
                    if ($newCheckIn !== null) {
                        $newStatus = AttendanceRules::determineStatus($newCheckIn, $workingStart, $lateThreshold);
                    }

                    $this->attendanceRepo->update($attendance->id, [
                        'check_in' => $newCheckIn,
                        'check_out' => $newCheckOut,
                        'status' => $newStatus
                    ]);
                }
            }

            // 3. Send notification alert to user
            $this->alertRepo->create([
                'user_id' => $request->userId,
                'type' => $actionStatus === 'Approved' ? 'Correction Approved' : 'Correction Rejected',
                'title' => "Correction Request {$actionStatus}",
                'message' => "Your correction request #{$requestId} has been {$actionStatus}. Note: " . ($adminNote ?: 'No note provided.')
            ]);

            $pdo->commit();
            $this->setFlash('success', "Correction request #{$requestId} successfully {$actionStatus}.");
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $this->setFlash('error', 'Failed to process correction review: ' . $e->getMessage());
        }

        $this->redirect(BASE_URL . '/admin/corrections');
    }
}
