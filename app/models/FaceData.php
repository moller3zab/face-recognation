<?php

declare(strict_types=1);

namespace App\Models;

class FaceData
{
    public function __construct(
        public ?int $id = null,
        public int $userId = 0,
        public string $faceDescriptor = '',
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {}

    public function getDescriptorArray(): array
    {
        return json_decode($this->faceDescriptor, true) ?? [];
    }
}
