<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Attendance;

interface AttendanceRepositoryInterface
{
    public function findById(int $id): ?Attendance;
    public function findByUserAndDate(int $userId, string $date): ?Attendance;
    public function findAll(array $filters = [], int $limit = 50, int $offset = 0): array;
    public function create(array $data): int;
    public function update(int $id, array $data): bool;
}
