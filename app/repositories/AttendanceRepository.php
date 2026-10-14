<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\Attendance;
use PDO;

class AttendanceRepository implements AttendanceRepositoryInterface
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getInstance()->getConnection();
    }

    public function findById(int $id): ?Attendance
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `attendance` WHERE `id` = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    public function findByUserAndDate(int $userId, string $date): ?Attendance
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `attendance` WHERE `user_id` = :user_id AND `attendance_date` = :date LIMIT 1");
        $stmt->execute([':user_id' => $userId, ':date' => $date]);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    public function findAll(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        $sql = "SELECT a.*, u.name as user_name, u.department, u.identifier FROM `attendance` a JOIN `users` u ON a.user_id = u.id WHERE u.deleted_at IS NULL";
        $params = [];

        if (!empty($filters['user_id'])) {
            $sql .= " AND a.user_id = :user_id";
            $params[':user_id'] = $filters['user_id'];
        }

        if (!empty($filters['date'])) {
            $sql .= " AND a.attendance_date = :date";
            $params[':date'] = $filters['date'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND a.status = :status";
            $params[':status'] = $filters['status'];
        }

        $sql .= " ORDER BY a.attendance_date DESC, a.check_in DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $param => $val) {
            $stmt->bindValue($param, $val);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO `attendance` (`user_id`, `attendance_date`, `check_in`, `check_out`, `status`, `recognition_method`)
                VALUES (:user_id, :attendance_date, :check_in, :check_out, :status, :recognition_method)
                ON DUPLICATE KEY UPDATE `check_in` = VALUES(`check_in`), `check_out` = VALUES(`check_out`), `status` = VALUES(`status`)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $data['user_id'],
            ':attendance_date' => $data['attendance_date'],
            ':check_in' => $data['check_in'] ?? null,
            ':check_out' => $data['check_out'] ?? null,
            ':status' => $data['status'] ?? 'Present',
            ':recognition_method' => $data['recognition_method'] ?? 'Face Recognition'
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];

        foreach (['check_in', 'check_out', 'status', 'recognition_method'] as $col) {
            if (array_key_exists($col, $data)) {
                $fields[] = "`{$col}` = :{$col}";
                $params[":{$col}"] = $data[$col];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE `attendance` SET " . implode(', ', $fields) . " WHERE `id` = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($params);
    }

    private function hydrate(array $row): Attendance
    {
        return new Attendance(
            id: (int) $row['id'],
            userId: (int) $row['user_id'],
            attendanceDate: $row['attendance_date'],
            checkIn: $row['check_in'],
            checkOut: $row['check_out'],
            status: $row['status'],
            recognitionMethod: $row['recognition_method'],
            createdAt: $row['created_at'],
            updatedAt: $row['updated_at']
        );
    }
}
