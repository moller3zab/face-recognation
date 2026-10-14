<?php

declare(strict_types=1);

/**
 * CLI Cron Script for Automated Absence Generation
 * 
 * Usage CLI:
 *   php cron/generate_absences.php
 *   php cron/generate_absences.php 2026-10-02
 * 
 * Windows Task Scheduler Command:
 *   Action: Start a program
 *   Program/script: D:\xampp\php\php.exe
 *   Add arguments: D:\xampp\htdocs\project se\cron\generate_absences.php
 */

require_once __DIR__ . '/../config/constants.php';
require_once APP_PATH . '/core/Autoloader.php';

use App\Core\Autoloader;
use App\Core\Config;
use App\Core\Logger;
use App\Services\AttendanceService;

Autoloader::register();
date_default_timezone_set(Config::get('app.timezone', 'Africa/Cairo'));

$targetDate = $argv[1] ?? date('Y-m-d');

echo "====================================================\n";
echo "Face Attendance System — Absence Generator Cron\n";
echo "Date Target: {$targetDate}\n";
echo "Execution Time: " . date('Y-m-d H:i:s') . "\n";
echo "====================================================\n";

try {
    $service = new AttendanceService();
    $result = $service->generateAbsences($targetDate);

    echo "Status: " . strtoupper($result['status']) . "\n";
    echo "Message: " . $result['message'] . "\n";
    echo "Absences Recorded: " . $result['count'] . "\n";
    
    Logger::info("Cron Absence Generator ran for {$targetDate}: " . $result['message']);
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    Logger::error("Cron Absence Generator Error: " . $e->getMessage());
    exit(1);
}

exit(0);
