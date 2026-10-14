<?php

declare(strict_types=1);

namespace App\Models;

class Setting
{
    public function __construct(
        public ?int $id = null,
        public string $keyName = '',
        public string $value = '',
        public ?string $updatedAt = null
    ) {}
}
