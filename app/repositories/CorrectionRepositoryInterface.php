<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\CorrectionRequest;

interface CorrectionRepositoryInterface
{
    public function findById(int $id): ?CorrectionRequest;
    public function findAll(array $filters = [], int $limit = 50, int $offset = 0): array;
    public function create(array $data): int;
    public function updateStatus(int $id, string $status, ?string $adminNote = null, ?int $reviewedBy = null): bool;
}
