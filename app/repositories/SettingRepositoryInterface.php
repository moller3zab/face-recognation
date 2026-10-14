<?php

declare(strict_types=1);

namespace App\Repositories;

interface SettingRepositoryInterface
{
    public function getAll(): array;
    public function get(string $key, mixed $default = null): mixed;
    public function set(string $key, string $value): bool;
    public function setBatch(array $settings): bool;
}
