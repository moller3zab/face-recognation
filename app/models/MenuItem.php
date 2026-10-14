<?php

declare(strict_types=1);

namespace App\Models;

class MenuItem
{
    public array $children = [];

    public function __construct(
        public ?int $id = null,
        public ?int $parentId = null,
        public string $title = '',
        public string $url = '',
        public ?string $icon = null,
        public string $role = 'both',
        public int $sortOrder = 0,
        public bool $isActive = true
    ) {}
}
