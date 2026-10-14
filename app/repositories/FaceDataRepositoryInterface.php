<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\FaceData;

interface FaceDataRepositoryInterface
{
    public function findByUserId(int $userId): ?FaceData;
    public function saveDescriptor(int $userId, string $descriptorJson): bool;
    public function deleteByUserId(int $userId): bool;
}
