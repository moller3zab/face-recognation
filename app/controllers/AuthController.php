<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Validator;
use App\Services\AuthService;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirect(Auth::isAdmin() ? BASE_URL . '/admin/dashboard' : BASE_URL . '/user/dashboard');
        }

        $this->render('auth.login', [
            'title' => 'Login — Face Attendance',
            'error' => $this->getFlash('error'),
            'success' => $this->getFlash('success')
        ], '');
    }

    public function login(): void
    {
        if (!Csrf::verifyToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('error', 'Invalid security CSRF token.');
            $this->redirect(BASE_URL . '/login');
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        $validator = new Validator();
        if (!$validator->validate($_POST, [
            'email' => 'required|email',
            'password' => 'required'
        ])) {
            $this->setFlash('error', 'Please fill in all required fields correctly.');
            $this->redirect(BASE_URL . '/login');
        }

        $result = $this->authService->authenticate($email, $password, $ip);

        if ($result['success']) {
            $this->redirect($result['redirect']);
        } else {
            $this->setFlash('error', $result['message']);
            $this->redirect(BASE_URL . '/login');
        }
    }

    public function showRegister(): void
    {
        if (Auth::check()) {
            $this->redirect(Auth::isAdmin() ? BASE_URL . '/admin/dashboard' : BASE_URL . '/user/dashboard');
        }

        $this->render('auth.register', [
            'title' => 'Register Account — Face Attendance',
            'errors' => $this->getFlash('errors') ?? [],
            'error' => $this->getFlash('error')
        ], '');
    }

    public function register(): void
    {
        if (!Csrf::verifyToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('error', 'Invalid security CSRF token.');
            $this->redirect(BASE_URL . '/register');
        }

        $validator = new Validator();
        if (!$validator->validate($_POST, [
            'name' => 'required|min:3|max:100',
            'email' => 'required|email|unique:users,email',
            'identifier' => 'required|min:3|max:50|unique:users,identifier',
            'password' => 'required|min:6',
            'password_confirm' => 'required|match:password'
        ])) {
            $this->setFlash('errors', $validator->getFirstErrors());
            $this->redirect(BASE_URL . '/register');
        }

        $result = $this->authService->registerUser($_POST, $_FILES);

        if ($result['success']) {
            $this->setFlash('success', $result['message']);
            $this->redirect(BASE_URL . '/login');
        } else {
            $this->setFlash('error', $result['message']);
            $this->redirect(BASE_URL . '/register');
        }
    }

    public function logout(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && Csrf::verifyToken($_POST['csrf_token'] ?? null)) {
            Auth::logout();
        }

        $this->redirect(BASE_URL . '/login');
    }
}
