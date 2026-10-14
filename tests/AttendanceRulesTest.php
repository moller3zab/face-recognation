<?php

declare(strict_types=1);

namespace Tests;

use App\Services\AttendanceRules;

class AttendanceRulesTest
{
    public function testDetermineStatusPresent(): void
    {
        // 08:55:00 with working start 09:00:00 -> Present
        $status = AttendanceRules::determineStatus('08:55:00', '09:00:00', 15);
        self::expect($status === 'Present', "Expected 'Present', got '{$status}'");
    }

    public function testDetermineStatusWithinGracePeriod(): void
    {
        // 09:10:00 with working start 09:00:00 and 15 min threshold -> Present
        $status = AttendanceRules::determineStatus('09:10:00', '09:00:00', 15);
        self::expect($status === 'Present', "Expected 'Present', got '{$status}'");
    }

    public function testDetermineStatusLate(): void
    {
        // 09:20:00 with working start 09:00:00 and 15 min threshold -> Late
        $status = AttendanceRules::determineStatus('09:20:00', '09:00:00', 15);
        self::expect($status === 'Late', "Expected 'Late', got '{$status}'");
    }

    public function testIsWorkingDayWeekend(): void
    {
        // Friday 2026-10-02 is a weekend in Egypt (Friday, Saturday)
        $isWorking = AttendanceRules::isWorkingDay('2026-10-02', ['Friday', 'Saturday'], []);
        self::expect($isWorking === false, "Expected Friday to be a non-working day");
    }

    public function testIsWorkingDayHoliday(): void
    {
        // Thursday 2026-10-08 marked as holiday
        $isWorking = AttendanceRules::isWorkingDay('2026-10-08', ['Friday', 'Saturday'], ['2026-10-08']);
        self::expect($isWorking === false, "Expected Holiday date to be a non-working day");
    }

    private static function expect(bool $condition, string $message): void
    {
        if (!$condition) {
            throw new \RuntimeException($message);
        }
    }
}
