<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\AlertRepository;
use App\Repositories\AttendanceRepository;
use App\Repositories\UserRepository;
use App\Services\AttendanceRules;

class AttendanceService
{
    private AttendanceRepository $attendanceRepo;
    private AlertRepository $alertRepo;
    private SettingsService $settingsService;
    private UserRepository $userRepo;

    public function __construct(
        ?AttendanceRepository $attendanceRepo = null,
        ?AlertRepository $alertRepo = null,
        ?SettingsService $settingsService = null,
        ?UserRepository $userRepo = null
    ) {
        $this->attendanceRepo = $attendanceRepo ?? new AttendanceRepository();
        $this->alertRepo = $alertRepo ?? new AlertRepository();
        $this->settingsService = $settingsService ?? new SettingsService();
        $this->userRepo = $userRepo ?? new UserRepository();
    }

    /**
     * Process face verification attendance attempt (Check-In or Check-Out).
     */
    public function processAttendance(int $userId, float $distance, string $action = 'auto'): array
    {
        $todayDate = date('Y-m-d');
        $nowTime = date('H:i:s');

        $existing = $this->attendanceRepo->findByUserAndDate($userId, $todayDate);

        if ($action === 'auto') {
            if (!$existing || $existing->checkIn === null) {
                $action = 'check_in';
            } elseif ($existing->checkOut === null) {
                $action = 'check_out';
            } else {
                return [
                    'status' => 'error',
                    'message' => 'You have already completed both Check-In and Check-Out for today.'
                ];
            }
        }

        if ($action === 'check_in') {
            if ($existing && $existing->checkIn !== null) {
                return [
                    'status' => 'error',
                    'message' => 'You have already checked in for today at ' . $existing->checkIn . '.'
                ];
            }

            $workingStart = $this->settingsService->get('working_start', '09:00');
            $lateThreshold = (int) $this->settingsService->get('late_threshold_minutes', '15');

            $attendanceStatus = AttendanceRules::determineStatus($nowTime, $workingStart, $lateThreshold);

            $data = [
                'user_id' => $userId,
                'attendance_date' => $todayDate,
                'check_in' => $nowTime,
                'status' => $attendanceStatus,
                'recognition_method' => 'Face Recognition'
            ];

            $this->attendanceRepo->create($data);

            if ($attendanceStatus === 'Late') {
                $this->alertRepo->create([
                    'user_id' => $userId,
                    'type' => 'Late',
                    'title' => 'Late Arrival Recorded',
                    'message' => "Check-in recorded at {$nowTime} (Late arrival after {$workingStart})."
                ]);
            }

            return [
                'status' => 'success',
                'type' => 'check_in',
                'attendance_status' => $attendanceStatus,
                'check_in' => $nowTime,
                'date' => $todayDate,
                'distance' => $distance,
                'message' => "Check-In successful! Status: {$attendanceStatus}."
            ];
        }

        if ($action === 'check_out') {
            if (!$existing || $existing->checkIn === null) {
                return [
                    'status' => 'error',
                    'message' => 'Cannot Check-Out without a valid Check-In for today.'
                ];
            }

            if ($existing->checkOut !== null) {
                return [
                    'status' => 'error',
                    'message' => 'You have already checked out today at ' . $existing->checkOut . '.'
                ];
            }

            $this->attendanceRepo->update($existing->id, [
                'check_out' => $nowTime
            ]);

            return [
                'status' => 'success',
                'type' => 'check_out',
                'attendance_status' => $existing->status,
                'check_in' => $existing->checkIn,
                'check_out' => $nowTime,
                'date' => $todayDate,
                'distance' => $distance,
                'message' => "Check-Out successful at {$nowTime}!"
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Invalid attendance action specified.'
        ];
    }

    /**
     * Generate absence records for all active users for a specific date if it is a working day.
     */
    public function generateAbsences(string $targetDate): array
    {
        $weekendDays = $this->settingsService->getWeekendDays();
        $holidays = array_column($this->settingsService->getHolidays(), 'date');

        if (!AttendanceRules::isWorkingDay($targetDate, $weekendDays, $holidays)) {
            return [
                'status' => 'skipped',
                'count' => 0,
                'message' => "Target date {$targetDate} is a non-working day (weekend or holiday). No absences generated."
            ];
        }

        $users = $this->userRepo->findAll(['status' => 'active'], 1000, 0);
        $generatedCount = 0;

        foreach ($users as $u) {
            $userId = (int) $u->id;
            $existing = $this->attendanceRepo->findByUserAndDate($userId, $targetDate);

            if (!$existing) {
                // Record absence
                $this->attendanceRepo->create([
                    'user_id' => $userId,
                    'attendance_date' => $targetDate,
                    'check_in' => null,
                    'check_out' => null,
                    'status' => 'Absent',
                    'recognition_method' => 'System Auto'
                ]);

                // Generate Alert
                $this->alertRepo->create([
                    'user_id' => $userId,
                    'type' => 'Absent',
                    'title' => 'Absence Recorded',
                    'message' => "Marked absent on {$targetDate} due to no check-in record."
                ]);

                $generatedCount++;
            }
        }

        // Also mark incomplete records for past dates
        $this->markIncompletePastRecords($targetDate);

        return [
            'status' => 'success',
            'count' => $generatedCount,
            'message' => "Successfully generated {$generatedCount} absence record(s) for {$targetDate}."
        ];
    }

    /**
     * Mark records with check_in but no check_out on past dates as 'Incomplete'.
     */
    public function markIncompletePastRecords(string $beforeDate): void
    {
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $sql = "UPDATE `attendance` 
                SET `status` = 'Incomplete' 
                WHERE `attendance_date` <= :before_date 
                  AND `check_in` IS NOT NULL 
                  AND `check_out` IS NULL 
                  AND `status` IN ('Present', 'Late')";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':before_date' => $beforeDate]);
    }
}
