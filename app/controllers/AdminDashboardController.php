<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Middleware\RoleMiddleware;
use App\Repositories\AttendanceRepository;
use App\Repositories\CorrectionRepository;
use App\Services\ReportService;

class AdminDashboardController extends Controller
{
    private ReportService $reportService;
    private AttendanceRepository $attendanceRepo;
    private CorrectionRepository $correctionRepo;

    public function __construct()
    {
        RoleMiddleware::handle('admin');
        $this->reportService = new ReportService();
        $this->attendanceRepo = new AttendanceRepository();
        $this->correctionRepo = new CorrectionRepository();
    }

    public function index(): void
    {
        $stats = $this->reportService->getAdminDashboardStats();
        $chartData = $this->reportService->getAttendanceChartData();
        $recentAttendance = $this->attendanceRepo->findAll([], 5, 0);
        $pendingCorrections = $this->correctionRepo->findAll(['status' => 'Pending'], 5, 0);

        $this->render('admin.dashboard', [
            'title' => 'Admin Dashboard — Face Attendance',
            'stats' => $stats,
            'chartData' => $chartData,
            'recentAttendance' => $recentAttendance,
            'pendingCorrections' => $pendingCorrections
        ]);
    }
}
