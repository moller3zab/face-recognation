<?php

declare(strict_types=1);

/**
 * Custom Automated Test Runner for Face Attendance System
 */

require_once __DIR__ . '/../config/constants.php';
require_once APP_PATH . '/core/Autoloader.php';

use App\Core\Autoloader;

Autoloader::register();

// Register Test Namespace Mapping
spl_autoload_register(function (string $class) {
    if (str_starts_with($class, 'Tests\\')) {
        $file = __DIR__ . '/' . str_replace('\\', '/', substr($class, 6)) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});

ini_set('assert.active', '1');
ini_set('assert.exception', '1');

$testClasses = [
    \Tests\AttendanceRulesTest::class,
    \Tests\FaceRecognitionServiceTest::class
];

echo "====================================================\n";
echo "FACE ATTENDANCE SYSTEM — AUTOMATED UNIT TEST RUNNER\n";
echo "====================================================\n\n";

$totalTests = 0;
$passed = 0;
$failed = 0;

foreach ($testClasses as $testClass) {
    $instance = new $testClass();
    $methods = get_class_methods($instance);

    echo "Running Suite: {$testClass}\n";

    foreach ($methods as $method) {
        if (!str_starts_with($method, 'test')) {
            continue;
        }

        $totalTests++;
        try {
            $instance->$method();
            echo "  [PASS] {$method}\n";
            $passed++;
        } catch (\Throwable $e) {
            echo "  [FAIL] {$method}: " . $e->getMessage() . "\n";
            $failed++;
        }
    }
    echo "\n";
}

echo "====================================================\n";
echo "SUMMARY: Total: {$totalTests} | Passed: {$passed} | Failed: {$failed}\n";
echo "====================================================\n";

exit($failed === 0 ? 0 : 1);
