<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Alert;

interface AlertRepositoryInterface
{
    public function findByUserId(int $userId, int $limit = 20): array;
    public function countUnread(int $userId): int;
    public function create(array $data): int;
    public function markAsRead(int $alertId, int $userId): bool;
    public function markAllAsRead(int $userId): bool;
}
