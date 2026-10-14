<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\HolidayRepository;
use App\Repositories\HolidayRepositoryInterface;
use App\Repositories\SettingRepository;
use App\Repositories\SettingRepositoryInterface;

class SettingsService
{
    private SettingRepositoryInterface $settingRepo;
    private HolidayRepositoryInterface $holidayRepo;
    private ?array $cache = null;

    public function __construct(
        ?SettingRepositoryInterface $settingRepo = null,
        ?HolidayRepositoryInterface $holidayRepo = null
    ) {
        $this->settingRepo = $settingRepo ?? new SettingRepository();
        $this->holidayRepo = $holidayRepo ?? new HolidayRepository();
    }

    public function getAllSettings(): array
    {
        if ($this->cache === null) {
            $this->cache = $this->settingRepo->getAll();
        }
        return $this->cache;
    }

    public function getSetting(string $key, mixed $default = null): mixed
    {
        $all = $this->getAllSettings();
        return $all[$key] ?? $default;
    }

    public function getWorkingStart(): string
    {
        return (string) $this->getSetting('working_start', '09:00');
    }

    public function getWorkingEnd(): string
    {
        return (string) $this->getSetting('working_end', '17:00');
    }

    public function getLateThresholdMinutes(): int
    {
        return (int) $this->getSetting('late_threshold_minutes', 15);
    }

    public function getRecognitionThreshold(): float
    {
        return (float) $this->getSetting('recognition_threshold', 0.60);
    }

    public function getWeekendDays(): array
    {
        $val = (string) $this->getSetting('weekend_days', '5,6');
        return array_map('intval', explode(',', $val));
    }

    public function updateSettings(array $data): bool
    {
        $this->cache = null;
        return $this->settingRepo->setBatch($data);
    }

    public function getHolidays(): array
    {
        return $this->holidayRepo->findAll();
    }

    public function addHoliday(string $date, string $name): int
    {
        return $this->holidayRepo->create($date, $name);
    }

    public function deleteHoliday(int $id): bool
    {
        return $this->holidayRepo->delete($id);
    }
}
