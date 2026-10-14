<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Middleware\AuthMiddleware;
use App\Repositories\AttendanceRepository;
use App\Repositories\CorrectionRepository;
use App\Repositories\FaceDataRepository;
use App\Services\AttendanceRules;
use App\Services\SettingsService;

class UserDashboardController extends Controller
{
    private AttendanceRepository $attendanceRepo;
    private CorrectionRepository $correctionRepo;
    private FaceDataRepository $faceDataRepo;
    private SettingsService $settingsService;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->attendanceRepo = new AttendanceRepository();
        $this->correctionRepo = new CorrectionRepository();
        $this->faceDataRepo = new FaceDataRepository();
        $this->settingsService = new SettingsService();
    }

    public function index(): void
    {
        $user = Auth::user();
        $userId = $user['id'];
        $todayDate = date('Y-m-d');

        // Today Attendance Record
        $todayRecord = $this->attendanceRepo->findByUserAndDate($userId, $todayDate);

        // Calculate Monthly Percentage
        $monthlyPercentage = $this->calculateMonthlyPercentage($userId);

        // Recent Records
        $recentRecords = $this->attendanceRepo->findAll(['user_id' => $userId], 5, 0);

        // Pending Correction Requests
        $pendingCorrections = $this->correctionRepo->findAll(['user_id' => $userId, 'status' => 'Pending'], 5, 0);

        // Face Status
        $faceData = $this->faceDataRepo->findByUserId($userId);

        $this->render('user.dashboard', [
            'title' => 'User Dashboard — Face Attendance',
            'user' => $user,
            'todayRecord' => $todayRecord,
            'monthlyPercentage' => $monthlyPercentage,
            'recentRecords' => $recentRecords,
            'pendingCorrections' => $pendingCorrections,
            'hasFaceData' => ($faceData !== null)
        ]);
    }

    private function calculateMonthlyPercentage(int $userId): float
    {
        $yearMonth = date('Y-m');
        $daysInMonth = (int) date('t');
        $weekendDays = $this->settingsService->getWeekendDays();
        $holidays = array_column($this->settingsService->getHolidays(), 'date');

        $workingDays = 0;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dateStr = sprintf('%s-%02d', $yearMonth, $day);
            if ($dateStr > date('Y-m-d')) {
                break; // Don't count future days
            }

            if (AttendanceRules::isWorkingDay($dateStr, $weekendDays, $holidays)) {
                $workingDays++;
            }
        }

        if ($workingDays === 0) {
            return 100.0;
        }

        // Count Present / Late days in current month
        $records = $this->attendanceRepo->findAll(['user_id' => $userId], 31, 0);
        $attendedCount = 0;

        foreach ($records as $r) {
            if (str_starts_with($r['attendance_date'], $yearMonth)) {
                if ($r['status'] === 'Present' || $r['status'] === 'Late') {
                    $attendedCount++;
                }
            }
        }

        return round(($attendedCount / $workingDays) * 100, 1);
    }
}
