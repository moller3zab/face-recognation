<?php

declare(strict_types=1);

namespace App\Models;

class CorrectionRequest
{
    public function __construct(
        public ?int $id = null,
        public int $userId = 0,
        public int $attendanceId = 0,
        public ?string $requestedCheckIn = null,
        public ?string $requestedCheckOut = null,
        public string $reason = '',
        public string $status = 'Pending',
        public ?string $adminNote = null,
        public ?int $reviewedBy = null,
        public ?string $reviewedAt = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {}
}
