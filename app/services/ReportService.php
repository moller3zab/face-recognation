<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use PDO;

class ReportService
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getInstance()->getConnection();
    }

    public function getAdminDashboardStats(): array
    {
        $today = date('Y-m-d');

        // Total active users
        $stmtUsers = $this->pdo->query("SELECT COUNT(*) FROM `users` WHERE `deleted_at` IS NULL AND `status` = 'active'");
        $totalUsers = (int) $stmtUsers->fetchColumn();

        // Today present
        $stmtPresent = $this->pdo->prepare("SELECT COUNT(*) FROM `attendance` WHERE `attendance_date` = :date AND `status` = 'Present'");
        $stmtPresent->execute([':date' => $today]);
        $presentToday = (int) $stmtPresent->fetchColumn();

        // Today late
        $stmtLate = $this->pdo->prepare("SELECT COUNT(*) FROM `attendance` WHERE `attendance_date` = :date AND `status` = 'Late'");
        $stmtLate->execute([':date' => $today]);
        $lateToday = (int) $stmtLate->fetchColumn();

        // Today absent
        $stmtAbsent = $this->pdo->prepare("SELECT COUNT(*) FROM `attendance` WHERE `attendance_date` = :date AND `status` = 'Absent'");
        $stmtAbsent->execute([':date' => $today]);
        $absentToday = (int) $stmtAbsent->fetchColumn();

        // Pending corrections
        $stmtCorrections = $this->pdo->query("SELECT COUNT(*) FROM `correction_requests` WHERE `status` = 'Pending'");
        $pendingCorrections = (int) $stmtCorrections->fetchColumn();

        return [
            'totalUsers' => $totalUsers,
            'presentToday' => $presentToday,
            'lateToday' => $lateToday,
            'absentToday' => $absentToday,
            'pendingCorrections' => $pendingCorrections
        ];
    }

    public function getAttendanceChartData(): array
    {
        // Last 7 Days trend
        $sql = "SELECT `attendance_date`,
                       SUM(CASE WHEN `status` = 'Present' THEN 1 ELSE 0 END) as present_count,
                       SUM(CASE WHEN `status` = 'Late' THEN 1 ELSE 0 END) as late_count,
                       SUM(CASE WHEN `status` = 'Absent' THEN 1 ELSE 0 END) as absent_count
                FROM `attendance`
                WHERE `attendance_date` >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                GROUP BY `attendance_date`
                ORDER BY `attendance_date` ASC";

        $stmt = $this->pdo->query($sql);
        $dailyTrend = $stmt->fetchAll();

        // Status Distribution
        $sqlDist = "SELECT `status`, COUNT(*) as total FROM `attendance` GROUP BY `status`";
        $stmtDist = $this->pdo->query($sqlDist);
        $statusDist = $stmtDist->fetchAll();

        return [
            'dailyTrend' => $dailyTrend,
            'statusDist' => $statusDist
        ];
    }

    public function getReportData(string $reportType, array $filters = []): array
    {
        $sql = "SELECT a.*, u.name as user_name, u.department, u.identifier, u.email 
                FROM `attendance` a 
                JOIN `users` u ON a.user_id = u.id 
                WHERE u.deleted_at IS NULL";
        $params = [];

        if (!empty($filters['date'])) {
            $sql .= " AND a.attendance_date = :date";
            $params[':date'] = $filters['date'];
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND a.attendance_date >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND a.attendance_date <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        if (!empty($filters['user_id'])) {
            $sql .= " AND a.user_id = :user_id";
            $params[':user_id'] = $filters['user_id'];
        }

        if (!empty($filters['department'])) {
            $sql .= " AND u.department = :department";
            $params[':department'] = $filters['department'];
        }

        if ($reportType === 'late') {
            $sql .= " AND a.status = 'Late'";
        } elseif ($reportType === 'absent') {
            $sql .= " AND a.status = 'Absent'";
        } elseif (!empty($filters['status'])) {
            $sql .= " AND a.status = :status";
            $params[':status'] = $filters['status'];
        }

        $sql .= " ORDER BY a.attendance_date DESC, a.check_in DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
}
