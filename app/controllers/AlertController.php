<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Repositories\AlertRepository;
use App\Repositories\UserRepository;

class AlertController extends Controller
{
    private AlertRepository $alertRepo;
    private UserRepository $userRepo;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->alertRepo = new AlertRepository();
        $this->userRepo = new UserRepository();
    }

    public function userIndex(): void
    {
        $userId = Auth::id();
        $filters = ['user_id' => $userId];

        if (!empty($_GET['type'])) {
            $filters['type'] = trim($_GET['type']);
        }
        if (isset($_GET['is_read']) && $_GET['is_read'] !== '') {
            $filters['is_read'] = $_GET['is_read'];
        }

        $alerts = $this->alertRepo->findAll($filters, 100, 0);

        $this->render('user.alerts.index', [
            'title' => 'My System Alerts & Notifications',
            'alerts' => $alerts,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ]);
    }

    public function unreadCount(): void
    {
        header('Content-Type: application/json');
        $userId = Auth::id();
        $count = $this->alertRepo->countUnread($userId);
        echo json_encode(['count' => $count]);
    }

    public function markAsRead(): void
    {
        if (!Csrf::verifyToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('error', 'Invalid security CSRF token.');
            $this->redirect(BASE_URL . '/alerts');
        }

        $alertId = (int) ($_POST['alert_id'] ?? 0);
        $userId = Auth::id();

        if ($this->alertRepo->markAsRead($alertId, $userId)) {
            $this->setFlash('success', 'Alert marked as read.');
        }

        $this->redirect(BASE_URL . '/alerts');
    }

    public function markAllAsRead(): void
    {
        if (!Csrf::verifyToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('error', 'Invalid security CSRF token.');
            $this->redirect(BASE_URL . '/alerts');
        }

        $userId = Auth::id();
        $this->alertRepo->markAllAsRead($userId);
        $this->setFlash('success', 'All notifications marked as read.');

        $this->redirect(BASE_URL . '/alerts');
    }

    public function adminIndex(): void
    {
        RoleMiddleware::handle('admin');

        $filters = [];
        if (!empty($_GET['type'])) {
            $filters['type'] = trim($_GET['type']);
        }
        if (!empty($_GET['user_id'])) {
            $filters['user_id'] = (int) $_GET['user_id'];
        }

        $alerts = $this->alertRepo->findAll($filters, 100, 0);
        $users = $this->userRepo->findAll(['status' => 'active'], 500, 0);

        $this->render('admin.alerts.index', [
            'title' => 'System Alerts Management',
            'alerts' => $alerts,
            'users' => $users,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ]);
    }

    public function sendNotification(): void
    {
        RoleMiddleware::handle('admin');

        if (!Csrf::verifyToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('error', 'Invalid security CSRF token.');
            $this->redirect(BASE_URL . '/admin/alerts');
        }

        $targetUserId = (int) ($_POST['user_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (empty($title) || empty($message)) {
            $this->setFlash('error', 'Notification title and message are required.');
            $this->redirect(BASE_URL . '/admin/alerts');
        }

        if ($targetUserId === 0) {
            // Broadcast to all active users
            $activeUsers = $this->userRepo->findAll(['status' => 'active'], 1000, 0);
            $count = 0;
            foreach ($activeUsers as $u) {
                $this->alertRepo->create([
                    'user_id' => $u->id,
                    'type' => 'System',
                    'title' => $title,
                    'message' => $message
                ]);
                $count++;
            }
            $this->setFlash('success', "System broadcast sent to {$count} active user(s).");
        } else {
            $this->alertRepo->create([
                'user_id' => $targetUserId,
                'type' => 'System',
                'title' => $title,
                'message' => $message
            ]);
            $this->setFlash('success', 'System notification sent to specified user.');
        }

        $this->redirect(BASE_URL . '/admin/alerts');
    }

    public function deleteAlert(): void
    {
        RoleMiddleware::handle('admin');

        if (!Csrf::verifyToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('error', 'Invalid security CSRF token.');
            $this->redirect(BASE_URL . '/admin/alerts');
        }

        $alertId = (int) ($_POST['alert_id'] ?? 0);
        if ($this->alertRepo->delete($alertId)) {
            $this->setFlash('success', 'Alert removed successfully.');
        } else {
            $this->setFlash('error', 'Failed to remove alert.');
        }

        $this->redirect(BASE_URL . '/admin/alerts');
    }
}
