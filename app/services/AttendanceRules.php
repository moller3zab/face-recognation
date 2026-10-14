<?php

declare(strict_types=1);

namespace App\Services;

class AttendanceRules
{
    /**
     * Evaluates attendance check-in status: 'Present' vs 'Late'.
     */
    public static function evaluateStatus(string $checkInTime, string $workingStart, int $lateThresholdMinutes): string
    {
        $checkInSec = strtotime("1970-01-01 {$checkInTime}");
        $startSec = strtotime("1970-01-01 {$workingStart}");
        $thresholdSec = $startSec + ($lateThresholdMinutes * 60);

        if ($checkInSec === false || $startSec === false) {
            return 'Present';
        }

        return ($checkInSec <= $thresholdSec) ? 'Present' : 'Late';
    }

    /**
     * Alias for evaluateStatus.
     */
    public static function determineStatus(string $checkInTime, string $workingStart, int $lateThresholdMinutes): string
    {
        return self::evaluateStatus($checkInTime, $workingStart, $lateThresholdMinutes);
    }

    /**
     * Checks if a calendar date is an active working day (not weekend or holiday).
     *
     * @param string $dateStr Date format 'YYYY-MM-DD'
     * @param array $weekendDays Array of day indexes [5, 6] or names ['Friday', 'Saturday']
     * @param array $holidayDates List of official holiday dates ['YYYY-MM-DD']
     * @return bool True if working day, false if weekend or holiday
     */
    public static function isWorkingDay(string $dateStr, array $weekendDays = [5, 6], array $holidayDates = []): bool
    {
        if (in_array($dateStr, $holidayDates, true)) {
            return false;
        }

        $timestamp = strtotime($dateStr);
        if ($timestamp === false) {
            return true;
        }

        $dayOfWeekNum = (int) date('w', $timestamp); // 0=Sunday..6=Saturday
        $dayOfWeekName = date('l', $timestamp);     // 'Friday', 'Saturday'

        if (in_array($dayOfWeekNum, $weekendDays, true) || in_array($dayOfWeekName, $weekendDays, true)) {
            return false;
        }

        return true;
    }
}
