<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\FaceData;
use PDO;

class FaceDataRepository implements FaceDataRepositoryInterface
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getInstance()->getConnection();
    }

    public function findByUserId(int $userId): ?FaceData
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `face_data` WHERE `user_id` = :user_id LIMIT 1");
        $stmt->execute([':user_id' => $userId]);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    public function saveDescriptor(int $userId, string $descriptorJson): bool
    {
        $sql = "INSERT INTO `face_data` (`user_id`, `face_descriptor`)
                VALUES (:user_id, :face_descriptor)
                ON DUPLICATE KEY UPDATE `face_descriptor` = VALUES(`face_descriptor`), `updated_at` = NOW()";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':user_id' => $userId,
            ':face_descriptor' => $descriptorJson
        ]);
    }

    public function deleteByUserId(int $userId): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM `face_data` WHERE `user_id` = :user_id");
        return $stmt->execute([':user_id' => $userId]);
    }

    private function hydrate(array $row): FaceData
    {
        return new FaceData(
            id: (int) $row['id'],
            userId: (int) $row['user_id'],
            faceDescriptor: $row['face_descriptor'],
            createdAt: $row['created_at'],
            updatedAt: $row['updated_at']
        );
    }
}
