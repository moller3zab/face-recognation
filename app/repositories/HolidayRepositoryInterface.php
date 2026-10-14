<?php

declare(strict_types=1);

namespace App\Repositories;

interface HolidayRepositoryInterface
{
    public function findAll(): array;
    public function isHoliday(string $date): bool;
    public function create(string $date, string $name): int;
    public function delete(int $id): bool;
}
