<?php

declare(strict_types=1);

namespace App\Models;

class Attendance
{
    public function __construct(
        public ?int $id = null,
        public int $userId = 0,
        public string $attendanceDate = '',
        public ?string $checkIn = null,
        public ?string $checkOut = null,
        public string $status = 'Present',
        public string $recognitionMethod = 'Face Recognition',
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {}
}
