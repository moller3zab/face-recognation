<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Validator;
use App\Middleware\RoleMiddleware;
use App\Repositories\AttendanceRepository;
use App\Repositories\FaceDataRepository;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryInterface;
use App\Services\CsvImportService;

class UserController extends Controller
{
    private UserRepositoryInterface $userRepo;
    private AttendanceRepository $attendanceRepo;
    private FaceDataRepository $faceDataRepo;

    public function __construct()
    {
        RoleMiddleware::handle('admin');
        $this->userRepo = new UserRepository();
        $this->attendanceRepo = new AttendanceRepository();
        $this->faceDataRepo = new FaceDataRepository();
    }

    public function index(): void
    {
        $filters = [
            'search' => trim($_GET['search'] ?? ''),
            'role' => trim($_GET['role'] ?? ''),
            'status' => trim($_GET['status'] ?? '')
        ];

        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $users = $this->userRepo->findAll($filters, $limit, $offset);
        $totalUsers = $this->userRepo->countAll($filters);
        $totalPages = (int) ceil($totalUsers / $limit);

        $this->render('admin.users.index', [
            'title' => 'User Management',
            'users' => $users,
            'filters' => $filters,
            'page' => $page,
            'totalPages' => $totalPages,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ]);
    }

    public function create(): void
    {
        $this->render('admin.users.create', [
            'title' => 'Add New User Account',
            'errors' => $this->getFlash('errors') ?? []
        ]);
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $user = $this->userRepo->findById($id);
        if ($user === null) {
            $this->setFlash('error', 'User not found.');
            $this->redirect(BASE_URL . '/admin/users');
        }

        $this->render('admin.users.create', [
            'title' => 'Edit User Account',
            'user' => $user,
            'errors' => $this->getFlash('errors') ?? []
        ]);
    }

    public function store(): void
    {
        if (!Csrf::verifyToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('error', 'Invalid security CSRF token.');
            $this->redirect(BASE_URL . '/admin/users/create');
        }

        $validator = new Validator();
        if (!$validator->validate($_POST, [
            'name' => 'required|min:3|max:100',
            'email' => 'required|email|unique:users,email',
            'identifier' => 'required|min:3|max:50|unique:users,identifier',
            'password' => 'required|min:6',
            'role' => 'required|in:user,admin',
            'status' => 'required|in:active,inactive'
        ])) {
            $this->setFlash('errors', $validator->getFirstErrors());
            $this->redirect(BASE_URL . '/admin/users/create');
        }

        $data = [
            'name' => trim($_POST['name']),
            'email' => trim($_POST['email']),
            'identifier' => trim($_POST['identifier']),
            'password' => password_hash($_POST['password'], PASSWORD_BCRYPT),
            'role' => trim($_POST['role']),
            'department' => trim($_POST['department'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'status' => trim($_POST['status'])
        ];

        // Safe Photo Upload Handling
        if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $photoPath = $this->handlePhotoUpload($_FILES['photo']);
            if ($photoPath) {
                $data['photo'] = $photoPath;
            }
        }

        $userId = $this->userRepo->create($data);
        if ($userId > 0) {
            $this->setFlash('success', 'User account created successfully.');
            $this->redirect(BASE_URL . '/admin/users');
        } else {
            $this->setFlash('error', 'Failed to create user account.');
            $this->redirect(BASE_URL . '/admin/users/create');
        }
    }

    public function update(): void
    {
        if (!Csrf::verifyToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('error', 'Invalid security CSRF token.');
            $this->redirect(BASE_URL . '/admin/users');
        }

        $id = (int) ($_POST['id'] ?? 0);
        $existingUser = $this->userRepo->findById($id);
        if ($existingUser === null) {
            $this->setFlash('error', 'User not found.');
            $this->redirect(BASE_URL . '/admin/users');
        }

        $validator = new Validator();
        $valid = $validator->validate($_POST, [
            'name' => 'required|min:3|max:100',
            'email' => "required|email|unique:users,email,{$id}",
            'identifier' => "required|min:3|max:50|unique:users,identifier,{$id}",
            'role' => 'required|in:user,admin',
            'status' => 'required|in:active,inactive'
        ]);

        $newPassword = trim((string) ($_POST['password'] ?? ''));
        if ($newPassword !== '' && strlen($newPassword) < 6) {
            $validator->addError('password', 'Password must be at least 6 characters.');
            $valid = false;
        }

        if (!$valid) {
            $this->setFlash('errors', $validator->getFirstErrors());
            $this->redirect(BASE_URL . '/admin/users/edit?id=' . $id);
        }

        $data = [
            'name' => trim($_POST['name']),
            'email' => trim($_POST['email']),
            'identifier' => trim($_POST['identifier']),
            'role' => trim($_POST['role']),
            'status' => trim($_POST['status']),
            'department' => trim($_POST['department'] ?? ''),
            'phone' => trim($_POST['phone'] ?? '')
        ];

        if ($newPassword !== '') {
            $data['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
        }

        if (!empty($_FILES['photo']['name'])) {
            $photoPath = $_FILES['photo']['error'] === UPLOAD_ERR_OK
                ? $this->handlePhotoUpload($_FILES['photo'])
                : null;
            if ($photoPath === null) {
                $this->setFlash('error', 'The profile photo could not be uploaded. Use a JPEG, PNG, or WebP file under 2 MB.');
                $this->redirect(BASE_URL . '/admin/users/edit?id=' . $id);
            }
            $data['photo'] = $photoPath;
        }

        $removesActiveAdmin = $existingUser->role === 'admin'
            && $existingUser->status === 'active'
            && ($data['role'] !== 'admin' || $data['status'] !== 'active');
        if ($removesActiveAdmin && $this->userRepo->countAll(['role' => 'admin', 'status' => 'active']) <= 1) {
            $this->setFlash('error', 'The last active administrator cannot be demoted or deactivated.');
            $this->redirect(BASE_URL . '/admin/users/edit?id=' . $id);
        }

        if ($this->userRepo->update($id, $data)) {
            $this->setFlash('success', 'User account updated successfully.');
        } else {
            $this->setFlash('error', 'Failed to update user account.');
        }
        $this->redirect(BASE_URL . '/admin/users/show?id=' . $id);
    }

    public function show(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $user = $this->userRepo->findById($id);

        if (!$user) {
            $this->setFlash('error', 'User not found.');
            $this->redirect(BASE_URL . '/admin/users');
        }

        $attendanceLogs = $this->attendanceRepo->findAll(['user_id' => $id], 15, 0);
        $faceData = $this->faceDataRepo->findByUserId($id);

        $this->render('admin.users.show', [
            'title' => 'User Profile — ' . $user->name,
            'user' => $user,
            'attendanceLogs' => $attendanceLogs,
            'hasFaceData' => ($faceData !== null)
        ]);
    }

    public function delete(): void
    {
        if (!Csrf::verifyToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('error', 'Invalid security CSRF token.');
            $this->redirect(BASE_URL . '/admin/users');
        }

        $id = (int)($_POST['id'] ?? 0);
        $currentAdminId = Auth::id();

        // Safety Guard: Cannot soft-delete yourself
        if ($id === $currentAdminId) {
            $this->setFlash('error', 'You cannot delete your own active administrator account.');
            $this->redirect(BASE_URL . '/admin/users');
        }

        // Safety Guard: Cannot delete last admin
        $targetUser = $this->userRepo->findById($id);
        if ($targetUser && $targetUser->isAdmin()) {
            $adminCount = $this->userRepo->countAll(['role' => 'admin']);
            if ($adminCount <= 1) {
                $this->setFlash('error', 'Cannot delete the last remaining system administrator account.');
                $this->redirect(BASE_URL . '/admin/users');
            }
        }

        if ($this->userRepo->softDelete($id)) {
            $this->setFlash('success', 'User account deactivated and soft-deleted successfully.');
        } else {
            $this->setFlash('error', 'Failed to delete user account.');
        }

        $this->redirect(BASE_URL . '/admin/users');
    }

    public function importCsv(): void
    {
        if (!Csrf::verifyToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('error', 'Invalid security CSRF token.');
            $this->redirect(BASE_URL . '/admin/users');
        }

        if (empty($_FILES['csv_file']['tmp_name']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            $this->setFlash('error', 'Please select a valid CSV file to upload.');
            $this->redirect(BASE_URL . '/admin/users');
        }

        $csvService = new CsvImportService($this->userRepo);
        $result = $csvService->importUsersFromCsv($_FILES['csv_file']['tmp_name']);

        if ($result['imported'] > 0) {
            $msg = "HR Import Complete: {$result['imported']} account(s) created inactive. Review each record, set its initial password, and activate it before sign-in.";
            if (!empty($result['errors'])) {
                $msg .= " (" . count($result['errors']) . " row warnings)";
            }
            $this->setFlash('success', $msg);
        } else {
            $this->setFlash('error', 'CSV Import Failed: ' . implode(' | ', $result['errors']));
        }

        $this->redirect(BASE_URL . '/admin/users');
    }

    private function handlePhotoUpload(array $file): ?string
    {
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowed, true) || $file['size'] > 2 * 1024 * 1024) {
            return null;
        }

        if (!is_dir(UPLOADS_PATH)) {
            mkdir(UPLOADS_PATH, 0755, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'profile_' . uniqid() . '.' . strtolower($ext);
        $target = UPLOADS_PATH . DS . $filename;

        return move_uploaded_file($file['tmp_name'], $target) ? 'storage/uploads/' . $filename : null;
    }
}
