<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\RateLimiter;
use App\Core\Security;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;
use App\Models\User;

class AuthController extends Controller
{
    private Auth $auth;
    private RateLimiter $rateLimiter;

    public function __construct(View $view, Session $session, Security $security, Auth $auth, RateLimiter $rateLimiter)
    {
        parent::__construct($view, $session, $security);
        $this->auth = $auth;
        $this->rateLimiter = $rateLimiter;
    }

    public function showLogin(): void
    {
        if ($this->auth->check()) {
            $this->redirect('/admin/dashboard');
        }
        view('admin/login', [
            'title' => 'Yönetici Girişi',
            'flash' => $this->session->flash('message'),
        ]);
    }

    public function login(): void
    {
        if (!$this->security->verifyCsrf($_POST['_token'] ?? '')) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'CSRF doğrulaması başarısız.']);
            $this->redirect('/login');
        }

        $email = trim($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $rateKey = 'login_' . ($email ?: ($_SERVER['REMOTE_ADDR'] ?? 'ip'));
        if (!$this->rateLimiter->check($rateKey, 5, 300)) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Çok fazla deneme yapıldı. Lütfen daha sonra tekrar deneyin.']);
            $this->redirect('/login');
        }

        $errors = Validator::required($_POST, [
            'email' => 'E-posta zorunludur.',
            'password' => 'Parola zorunludur.',
        ]);
        if ($errors) {
            $this->session->flash('message', ['type' => 'danger', 'text' => implode(' ', $errors)]);
            $this->redirect('/login');
        }

        if ($this->auth->attempt($email, $password)) {
            $this->redirect('/admin/dashboard');
        }

        $this->session->flash('message', ['type' => 'danger', 'text' => 'Geçersiz kimlik bilgileri.']);
        $this->redirect('/login');
    }

    public function logout(): void
    {
        if ($this->security->verifyCsrf($_POST['_token'] ?? '')) {
            $this->auth->logout();
        }
        $this->redirect('/login');
    }
}
