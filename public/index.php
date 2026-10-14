<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
require_once APP_PATH . '/core/Autoloader.php';

use App\Core\Autoloader;
use App\Core\Config;
use App\Core\Logger;
use App\Core\Router;

// Register Autoloader
Autoloader::register();

// Set Timezone
date_default_timezone_set(Config::get('app.timezone', 'Africa/Cairo'));

// Global Session Initialization
\App\Core\Auth::init();

// Error & Exception Handling
error_reporting(E_ALL);
set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline) {
    Logger::error("PHP Error [$errno]: $errstr in $errfile line $errline");
    return true;
});

set_exception_handler(function (Throwable $e) {
    Logger::error("Uncaught Exception: " . $e->getMessage(), [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);

    http_response_code(500);
    $errorView = APP_PATH . DS . 'views' . DS . 'errors' . DS . '500.php';
    if (file_exists($errorView)) {
        require $errorView;
    } else {
        echo '<h1>500 Internal Server Error</h1>';
    }
});

// Initialize Router & Test Routes
$router = new Router();

$router->get('/', [\App\Controllers\HomeController::class, 'index']);


// Auth Routes
$router->get('/login', [\App\Controllers\AuthController::class, 'showLogin']);
$router->post('/login', [\App\Controllers\AuthController::class, 'login']);
$router->get('/register', [\App\Controllers\AuthController::class, 'showRegister']);
$router->post('/register', [\App\Controllers\AuthController::class, 'register']);
$router->post('/logout', [\App\Controllers\AuthController::class, 'logout']);

// Role-Protected Administrative Routes
$router->get('/admin/dashboard', [\App\Controllers\AdminDashboardController::class, 'index']);

// Analytics & Reports Routes (Phase 19)
$router->get('/admin/reports', [\App\Controllers\ReportController::class, 'index']);
$router->get('/admin/reports/export-csv', [\App\Controllers\ReportController::class, 'exportCsv']);


$router->get('/admin/settings', [\App\Controllers\SettingController::class, 'index']);
$router->post('/admin/settings/update', [\App\Controllers\SettingController::class, 'update']);
$router->post('/admin/settings/holidays/add', [\App\Controllers\SettingController::class, 'addHoliday']);
$router->post('/admin/settings/holidays/delete', [\App\Controllers\SettingController::class, 'deleteHoliday']);

// User Management Routes
$router->get('/admin/users', [\App\Controllers\UserController::class, 'index']);
$router->get('/admin/users/create', [\App\Controllers\UserController::class, 'create']);
$router->get('/admin/users/edit', [\App\Controllers\UserController::class, 'edit']);
$router->post('/admin/users/store', [\App\Controllers\UserController::class, 'store']);
$router->post('/admin/users/update', [\App\Controllers\UserController::class, 'update']);
$router->get('/admin/users/show', [\App\Controllers\UserController::class, 'show']);
$router->post('/admin/users/delete', [\App\Controllers\UserController::class, 'delete']);
$router->post('/admin/users/import-csv', [\App\Controllers\UserController::class, 'importCsv']);

// Protected Standard User Routes
$router->get('/user/dashboard', [\App\Controllers\UserDashboardController::class, 'index']);

// Profile Routes
$router->get('/user/profile', [\App\Controllers\ProfileController::class, 'showProfile']);
$router->get('/admin/profile', [\App\Controllers\ProfileController::class, 'showProfile']);
$router->post('/profile/update', [\App\Controllers\ProfileController::class, 'updateProfile']);
$router->post('/profile/change-password', [\App\Controllers\ProfileController::class, 'changePassword']);

// Face Registration Routes (Phase 14)
$router->get('/face/register', [\App\Controllers\FaceRegistrationController::class, 'showRegistration']);
$router->post('/face/store', [\App\Controllers\FaceRegistrationController::class, 'storeDescriptor']);
$router->post('/admin/users/reset-face', [\App\Controllers\FaceRegistrationController::class, 'resetUserFace']);

// Attendance Scanning & Verification Routes (Phase 15)
$router->get('/attendance/scan', [\App\Controllers\AttendanceController::class, 'showScanPage']);
$router->post('/attendance/verify-identity', [\App\Controllers\AttendanceController::class, 'verifyIdentity']);
$router->post('/attendance/verify', [\App\Controllers\AttendanceController::class, 'verifyAndMark']);

// Attendance Records & Absence Generation Routes (Phase 16)
$router->get('/user/attendance', [\App\Controllers\AttendanceController::class, 'userAttendance']);
$router->get('/admin/attendance', [\App\Controllers\AttendanceController::class, 'adminAttendance']);
$router->post('/admin/attendance/generate-absences', [\App\Controllers\AttendanceController::class, 'triggerGenerateAbsences']);

// Correction Request Routes (Phase 17)
$router->get('/corrections', [\App\Controllers\CorrectionController::class, 'userIndex']);
$router->get('/corrections/create', [\App\Controllers\CorrectionController::class, 'create']);
$router->post('/corrections/store', [\App\Controllers\CorrectionController::class, 'store']);
$router->get('/admin/corrections', [\App\Controllers\CorrectionController::class, 'adminIndex']);
$router->post('/admin/corrections/review', [\App\Controllers\CorrectionController::class, 'review']);

// Alert & Notification Routes (Phase 18)
$router->get('/alerts', [\App\Controllers\AlertController::class, 'userIndex']);
$router->get('/alerts/unread-count', [\App\Controllers\AlertController::class, 'unreadCount']);
$router->post('/alerts/mark-read', [\App\Controllers\AlertController::class, 'markAsRead']);
$router->post('/alerts/mark-all-read', [\App\Controllers\AlertController::class, 'markAllAsRead']);
$router->get('/admin/alerts', [\App\Controllers\AlertController::class, 'adminIndex']);
$router->post('/admin/alerts/send', [\App\Controllers\AlertController::class, 'sendNotification']);
$router->post('/admin/alerts/delete', [\App\Controllers\AlertController::class, 'deleteAlert']);







$router->get('/test-route', function () {
    echo json_encode([
        'status' => 'success',
        'message' => 'Routing system operational!',
        'app' => Config::get('app.name'),
        'timezone' => date_default_timezone_get(),
        'time' => date('Y-m-d H:i:s')
    ]);
});

$router->get('/db-check', function () {
    header('Content-Type: application/json');
    try {
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

        $tableCounts = [];
        foreach ($tables as $table) {
            $stmt = $pdo->query("SELECT COUNT(*) FROM `$table`");
            $tableCounts[$table] = (int) $stmt->fetchColumn();
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Database connection established successfully!',
            'database' => Config::get('database.dbname'),
            'tables' => $tableCounts
        ]);
    } catch (\Throwable $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
});

// Dispatch Current Request
$router->dispatch($_SERVER['REQUEST_URI'] ?? '/', $_SERVER['REQUEST_METHOD'] ?? 'GET');
