<?php

declare(strict_types=1);

namespace App\Repositories;

interface MenuItemRepositoryInterface
{
    public function findByRole(string $role): array;
    public function getTreeByRole(string $role): array;
}
