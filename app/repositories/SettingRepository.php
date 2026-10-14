<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class SettingRepository implements SettingRepositoryInterface
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getInstance()->getConnection();
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT `key_name`, `value` FROM `settings`");
        $results = [];
        while ($row = $stmt->fetch()) {
            $results[$row['key_name']] = $row['value'];
        }
        return $results;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $stmt = $this->pdo->prepare("SELECT `value` FROM `settings` WHERE `key_name` = :key LIMIT 1");
        $stmt->execute([':key' => $key]);
        $val = $stmt->fetchColumn();

        return ($val !== false) ? $val : $default;
    }

    public function set(string $key, string $value): bool
    {
        $sql = "INSERT INTO `settings` (`key_name`, `value`) VALUES (:key, :val)
                ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = NOW()";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([':key' => $key, ':val' => $value]);
    }

    public function setBatch(array $settings): bool
    {
        $this->pdo->beginTransaction();
        try {
            foreach ($settings as $key => $val) {
                $this->set((string)$key, (string)$val);
            }
            $this->pdo->commit();
            return true;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            return false;
        }
    }
}
