<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Middleware\RoleMiddleware;
use App\Repositories\UserRepository;
use App\Services\ReportService;

class ReportController extends Controller
{
    private ReportService $reportService;
    private UserRepository $userRepo;

    public function __construct()
    {
        RoleMiddleware::handle('admin');
        $this->reportService = new ReportService();
        $this->userRepo = new UserRepository();
    }

    public function index(): void
    {
        $reportType = $_GET['type'] ?? 'daily';
        $filters = [
            'date' => $_GET['date'] ?? null,
            'date_from' => $_GET['date_from'] ?? null,
            'date_to' => $_GET['date_to'] ?? null,
            'user_id' => $_GET['user_id'] ?? null,
            'department' => $_GET['department'] ?? null,
            'status' => $_GET['status'] ?? null,
        ];

        $records = $this->reportService->getReportData($reportType, $filters);
        $users = $this->userRepo->findAll(['status' => 'active'], 500, 0);

        $this->render('admin.reports.index', [
            'title' => 'Attendance Analytics & Reports',
            'reportType' => $reportType,
            'filters' => $filters,
            'records' => $records,
            'users' => $users
        ]);
    }

    public function exportCsv(): void
    {
        $reportType = $_GET['type'] ?? 'daily';
        $filters = [
            'date' => $_GET['date'] ?? null,
            'date_from' => $_GET['date_from'] ?? null,
            'date_to' => $_GET['date_to'] ?? null,
            'user_id' => $_GET['user_id'] ?? null,
            'department' => $_GET['department'] ?? null,
            'status' => $_GET['status'] ?? null,
        ];

        $records = $this->reportService->getReportData($reportType, $filters);

        $filename = 'attendance_report_' . date('Y_m_d_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');

        // Write UTF-8 BOM for Microsoft Excel compatibility (Arabic & special chars)
        fwrite($out, "\xEF\xBB\xBF");

        // Header Row
        fputcsv($out, ['ID', 'User Name', 'Identifier', 'Department', 'Email', 'Attendance Date', 'Check In', 'Check Out', 'Status', 'Recognition Method']);

        foreach ($records as $r) {
            fputcsv($out, [
                $r['id'],
                $r['user_name'],
                $r['identifier'],
                $r['department'] ?? 'General',
                $r['email'],
                $r['attendance_date'],
                $r['check_in'] ?? '',
                $r['check_out'] ?? '',
                $r['status'],
                $r['recognition_method'] ?? 'Face Recognition'
            ]);
        }

        fclose($out);
        exit;
    }
}
