<?php

declare(strict_types=1);

namespace App\Models;

class Alert
{
    public function __construct(
        public ?int $id = null,
        public int $userId = 0,
        public string $type = 'System',
        public string $title = '',
        public string $message = '',
        public bool $isRead = false,
        public ?string $createdAt = null
    ) {}
}
