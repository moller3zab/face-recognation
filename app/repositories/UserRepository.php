<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\User;
use PDO;

class UserRepository implements UserRepositoryInterface
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getInstance()->getConnection();
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `users` WHERE `id` = :id AND `deleted_at` IS NULL LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `users` WHERE `email` = :email AND `deleted_at` IS NULL LIMIT 1");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    public function findByIdentifier(string $identifier): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `users` WHERE `identifier` = :identifier AND `deleted_at` IS NULL LIMIT 1");
        $stmt->execute([':identifier' => $identifier]);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    public function findAll(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        $sql = "SELECT * FROM `users` WHERE `deleted_at` IS NULL";
        $params = [];

        if (!empty($filters['role'])) {
            $sql .= " AND `role` = :role";
            $params[':role'] = $filters['role'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND `status` = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (`name` LIKE :search OR `email` LIKE :search OR `identifier` LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY `id` DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $param => $val) {
            $stmt->bindValue($param, $val);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $users = [];
        while ($row = $stmt->fetch()) {
            $users[] = $this->hydrate($row);
        }

        return $users;
    }

    public function countAll(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) FROM `users` WHERE `deleted_at` IS NULL";
        $params = [];

        if (!empty($filters['role'])) {
            $sql .= " AND `role` = :role";
            $params[':role'] = $filters['role'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND `status` = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (`name` LIKE :search OR `email` LIKE :search OR `identifier` LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO `users` (`name`, `email`, `password`, `role`, `phone`, `photo`, `department`, `identifier`, `status`)
                VALUES (:name, :email, :password, :role, :phone, :photo, :department, :identifier, :status)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':password' => $data['password'],
            ':role' => $data['role'] ?? 'user',
            ':phone' => $data['phone'] ?? null,
            ':photo' => $data['photo'] ?? null,
            ':department' => $data['department'] ?? null,
            ':identifier' => $data['identifier'],
            ':status' => $data['status'] ?? 'active'
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];

        foreach (['name', 'email', 'phone', 'photo', 'department', 'identifier', 'status', 'role'] as $column) {
            if (array_key_exists($column, $data)) {
                $fields[] = "`{$column}` = :{$column}";
                $params[":{$column}"] = $data[$column];
            }
        }

        if (isset($data['password'])) {
            $fields[] = "`password` = :password";
            $params[':password'] = $data['password'];
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE `users` SET " . implode(', ', $fields) . " WHERE `id` = :id AND `deleted_at` IS NULL";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($params);
    }

    public function softDelete(int $id): bool
    {
        $stmt = $this->pdo->prepare("UPDATE `users` SET `deleted_at` = NOW(), `status` = 'inactive' WHERE `id` = :id");
        return $stmt->execute([':id' => $id]);
    }

    private function hydrate(array $row): User
    {
        return new User(
            id: (int) $row['id'],
            name: $row['name'],
            email: $row['email'],
            password: $row['password'],
            role: $row['role'],
            phone: $row['phone'],
            photo: $row['photo'],
            department: $row['department'],
            identifier: $row['identifier'],
            status: $row['status'],
            deletedAt: $row['deleted_at'],
            createdAt: $row['created_at'],
            updatedAt: $row['updated_at']
        );
    }
}
