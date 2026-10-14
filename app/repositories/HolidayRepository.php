<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class HolidayRepository implements HolidayRepositoryInterface
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getInstance()->getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM `holidays` ORDER BY `date` ASC");
        return $stmt->fetchAll();
    }

    public function isHoliday(string $date): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM `holidays` WHERE `date` = :date");
        $stmt->execute([':date' => $date]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function create(string $date, string $name): int
    {
        $sql = "INSERT INTO `holidays` (`date`, `name`) VALUES (:date, :name)
                ON DUPLICATE KEY UPDATE `name` = VALUES(`name`)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':date' => $date, ':name' => $name]);

        return (int) $this->pdo->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM `holidays` WHERE `id` = :id");
        return $stmt->execute([':id' => $id]);
    }
}
