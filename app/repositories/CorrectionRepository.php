<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\CorrectionRequest;
use PDO;

class CorrectionRepository implements CorrectionRepositoryInterface
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getInstance()->getConnection();
    }

    public function findById(int $id): ?CorrectionRequest
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `correction_requests` WHERE `id` = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    public function findAll(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        $sql = "SELECT c.*, u.name as user_name, u.identifier, a.attendance_date FROM `correction_requests` c JOIN `users` u ON c.user_id = u.id JOIN `attendance` a ON c.attendance_id = a.id WHERE u.deleted_at IS NULL";
        $params = [];

        if (!empty($filters['user_id'])) {
            $sql .= " AND c.user_id = :user_id";
            $params[':user_id'] = $filters['user_id'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND c.status = :status";
            $params[':status'] = $filters['status'];
        }

        $sql .= " ORDER BY c.id DESC LIMIT :limit OFFSET :offset";
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
        $sql = "INSERT INTO `correction_requests` (`user_id`, `attendance_id`, `requested_check_in`, `requested_check_out`, `reason`, `status`)
                VALUES (:user_id, :attendance_id, :requested_check_in, :requested_check_out, :reason, 'Pending')";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $data['user_id'],
            ':attendance_id' => $data['attendance_id'],
            ':requested_check_in' => $data['requested_check_in'] ?? null,
            ':requested_check_out' => $data['requested_check_out'] ?? null,
            ':reason' => $data['reason']
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function updateStatus(int $id, string $status, ?string $adminNote = null, ?int $reviewedBy = null): bool
    {
        $sql = "UPDATE `correction_requests` SET `status` = :status, `admin_note` = :admin_note, `reviewed_by` = :reviewed_by, `reviewed_at` = NOW() WHERE `id` = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':status' => $status,
            ':admin_note' => $adminNote,
            ':reviewed_by' => $reviewedBy
        ]);
    }

    private function hydrate(array $row): CorrectionRequest
    {
        return new CorrectionRequest(
            id: (int) $row['id'],
            userId: (int) $row['user_id'],
            attendanceId: (int) $row['attendance_id'],
            requestedCheckIn: $row['requested_check_in'],
            requestedCheckOut: $row['requested_check_out'],
            reason: $row['reason'],
            status: $row['status'],
            adminNote: $row['admin_note'],
            reviewedBy: $row['reviewed_by'] !== null ? (int) $row['reviewed_by'] : null,
            reviewedAt: $row['reviewed_at'],
            createdAt: $row['created_at'],
            updatedAt: $row['updated_at']
        );
    }
}
