<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\Alert;
use PDO;

class AlertRepository implements AlertRepositoryInterface
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getInstance()->getConnection();
    }

    public function findByUserId(int $userId, int $limit = 20): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `alerts` WHERE `user_id` = :user_id ORDER BY `id` DESC LIMIT :limit");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $alerts = [];
        while ($row = $stmt->fetch()) {
            $alerts[] = $this->hydrate($row);
        }

        return $alerts;
    }

    public function findAll(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        $sql = "SELECT a.*, u.name as user_name, u.identifier FROM `alerts` a JOIN `users` u ON a.user_id = u.id WHERE u.deleted_at IS NULL";
        $params = [];

        if (!empty($filters['user_id'])) {
            $sql .= " AND a.user_id = :user_id";
            $params[':user_id'] = $filters['user_id'];
        }

        if (!empty($filters['type'])) {
            $sql .= " AND a.type = :type";
            $params[':type'] = $filters['type'];
        }

        if (isset($filters['is_read']) && $filters['is_read'] !== '') {
            $sql .= " AND a.is_read = :is_read";
            $params[':is_read'] = (int) $filters['is_read'];
        }

        $sql .= " ORDER BY a.id DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $param => $val) {
            $stmt->bindValue($param, $val);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countUnread(int $userId): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM `alerts` WHERE `user_id` = :user_id AND `is_read` = 0");
        $stmt->execute([':user_id' => $userId]);

        return (int) $stmt->fetchColumn();
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO `alerts` (`user_id`, `type`, `title`, `message`, `is_read`)
                VALUES (:user_id, :type, :title, :message, 0)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $data['user_id'],
            ':type' => $data['type'] ?? 'System',
            ':title' => $data['title'],
            ':message' => $data['message']
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function markAsRead(int $alertId, int $userId): bool
    {
        $stmt = $this->pdo->prepare("UPDATE `alerts` SET `is_read` = 1 WHERE `id` = :id AND `user_id` = :user_id");
        return $stmt->execute([':id' => $alertId, ':user_id' => $userId]);
    }

    public function markAllAsRead(int $userId): bool
    {
        $stmt = $this->pdo->prepare("UPDATE `alerts` SET `is_read` = 1 WHERE `user_id` = :user_id");
        return $stmt->execute([':user_id' => $userId]);
    }

    public function delete(int $alertId): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM `alerts` WHERE `id` = :id");
        return $stmt->execute([':id' => $alertId]);
    }

    private function hydrate(array $row): Alert
    {
        return new Alert(
            id: (int) $row['id'],
            userId: (int) $row['user_id'],
            type: $row['type'],
            title: $row['title'],
            message: $row['message'],
            isRead: (bool) $row['is_read'],
            createdAt: $row['created_at']
        );
    }
}
