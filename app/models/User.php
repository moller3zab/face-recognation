<?php

declare(strict_types=1);

namespace App\Models;

class User
{
    public function __construct(
        public ?int $id = null,
        public string $name = '',
        public string $email = '',
        public string $password = '',
        public string $role = 'user',
        public ?string $phone = null,
        public ?string $photo = null,
        public ?string $department = null,
        public string $identifier = '',
        public string $status = 'active',
        public ?string $deletedAt = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {}

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->deletedAt === null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'phone' => $this->phone,
            'photo' => $this->photo,
            'department' => $this->department,
            'identifier' => $this->identifier,
            'status' => $this->status,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}
