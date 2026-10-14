<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Validator;
use App\Middleware\RoleMiddleware;
use App\Services\SettingsService;

class SettingController extends Controller
{
    private SettingsService $settingsService;

    public function __construct()
    {
        RoleMiddleware::handle('admin');
        $this->settingsService = new SettingsService();
    }

    public function index(): void
    {
        $settings = $this->settingsService->getAllSettings();
        $holidays = $this->settingsService->getHolidays();

        $this->render('admin.settings', [
            'title' => 'System Settings & Holidays',
            'settings' => $settings,
            'holidays' => $holidays,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ]);
    }

    public function update(): void
    {
        if (!Csrf::verifyToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('error', 'Invalid security CSRF token.');
            $this->redirect(BASE_URL . '/admin/settings');
        }

        $validator = new Validator();
        if (!$validator->validate($_POST, [
            'working_start' => 'required',
            'working_end' => 'required',
            'late_threshold_minutes' => 'required',
            'recognition_threshold' => 'required',
            'default_camera' => 'required|in:user,environment',
            'camera_resolution' => 'required|in:640x480,1280x720'
        ])) {
            $this->setFlash('error', 'Please enter valid settings values.');
            $this->redirect(BASE_URL . '/admin/settings');
        }

        $updateData = [
            'working_start' => trim($_POST['working_start']),
            'working_end' => trim($_POST['working_end']),
            'late_threshold_minutes' => (string) (int) $_POST['late_threshold_minutes'],
            'recognition_threshold' => (string) (float) $_POST['recognition_threshold'],
            'default_camera' => trim($_POST['default_camera'] ?? 'user'),
            'camera_resolution' => trim($_POST['camera_resolution'] ?? '640x480')
        ];

        if (isset($_POST['weekend_days']) && is_array($_POST['weekend_days'])) {
            $updateData['weekend_days'] = implode(',', array_map('intval', $_POST['weekend_days']));
        }

        if ($this->settingsService->updateSettings($updateData)) {
            $this->setFlash('success', 'System settings updated successfully!');
        } else {
            $this->setFlash('error', 'Failed to update system settings.');
        }

        $this->redirect(BASE_URL . '/admin/settings');
    }

    public function addHoliday(): void
    {
        if (!Csrf::verifyToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('error', 'Invalid security CSRF token.');
            $this->redirect(BASE_URL . '/admin/settings');
        }

        $date = trim($_POST['date'] ?? '');
        $name = trim($_POST['name'] ?? '');

        if (!empty($date) && !empty($name)) {
            $this->settingsService->addHoliday($date, $name);
            $this->setFlash('success', 'Official holiday added successfully!');
        } else {
            $this->setFlash('error', 'Please provide both holiday date and title.');
        }

        $this->redirect(BASE_URL . '/admin/settings');
    }

    public function deleteHoliday(): void
    {
        if (!Csrf::verifyToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('error', 'Invalid security CSRF token.');
            $this->redirect(BASE_URL . '/admin/settings');
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->settingsService->deleteHoliday($id);
            $this->setFlash('success', 'Holiday deleted successfully.');
        }

        $this->redirect(BASE_URL . '/admin/settings');
    }
}
