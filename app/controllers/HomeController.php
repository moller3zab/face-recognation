<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Repositories\FaceDataRepository;
use App\Services\ReportService;
use App\Services\SettingsService;

class HomeController extends Controller
{
    private ReportService $reportService;

    public function __construct()
    {
        $this->reportService = new ReportService();
    }

    public function index(): void
    {
        $isLoggedIn = Auth::check();
        $userId = Auth::id();
        $hasFaceData = $isLoggedIn && $userId !== null
            ? (new FaceDataRepository())->findByUserId($userId) !== null
            : false;

        $stats = $this->reportService->getAdminDashboardStats();
        $settings = (new SettingsService())->getAllSettings();

        $this->render('home.index', [
            'title' => 'Smart Face Recognition Attendance System',
            'stats' => $stats,
            'isLoggedIn' => $isLoggedIn,
            'hasFaceData' => $hasFaceData,
            'cameraMode' => in_array($settings['default_camera'] ?? 'user', ['user', 'environment'], true)
                ? $settings['default_camera']
                : 'user',
            'cameraResolution' => in_array($settings['camera_resolution'] ?? '640x480', ['640x480', '1280x720'], true)
                ? $settings['camera_resolution']
                : '640x480',
            'csrfToken' => $isLoggedIn ? Csrf::generateToken() : '',
            'user' => Auth::user()
        ], 'landing');
    }
}
