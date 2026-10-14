<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function findByIdentifier(string $identifier): ?User;
    public function findAll(array $filters = [], int $limit = 50, int $offset = 0): array;
    public function countAll(array $filters = []): int;
    public function create(array $data): int;
    public function update(int $id, array $data): bool;
    public function softDelete(int $id): bool;
}
