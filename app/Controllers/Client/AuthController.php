<?php

declare(strict_types=1);

namespace App\Controllers\Client;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\RateLimiter;
use App\Core\Security;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;
use App\Services\CustomerService;

class AuthController extends Controller
{
    private Auth $auth;
    private RateLimiter $rateLimiter;
    private CustomerService $customerService;

    public function __construct(View $view, Session $session, Security $security, Auth $auth, RateLimiter $rateLimiter, CustomerService $customerService)
    {
        parent::__construct($view, $session, $security);
        $this->auth = $auth;
        $this->rateLimiter = $rateLimiter;
        $this->customerService = $customerService;
    }

    public function showLogin(): void
    {
        if ($this->auth->check() && $this->auth->user()['role'] === 'customer') {
            $this->redirect('/account');
        }
        view('client/login', [
            'title' => 'Müşteri Girişi',
            'csrf' => $this->security->csrfToken(),
            'flash' => $this->session->flash('message'),
        ]);
    }

    public function login(): void
    {
        if (!$this->security->verifyCsrf($_POST['_token'] ?? '')) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Oturum doğrulaması başarısız.']);
            $this->redirect('/account/login');
        }
        $key = 'client_login_' . ($_SERVER['REMOTE_ADDR'] ?? 'cli');
        if (!$this->rateLimiter->check($key, 5, 300)) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Çok fazla deneme. Lütfen daha sonra tekrar deneyin.']);
            $this->redirect('/account/login');
        }
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        if (!$this->auth->attempt($email, $password) || $this->auth->user()['role'] !== 'customer') {
            if ($this->auth->check()) {
                $this->auth->logout();
            }
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Geçersiz giriş bilgileri.']);
            $this->redirect('/account/login');
        }
        $this->session->flash('message', ['type' => 'success', 'text' => 'Hoş geldiniz!']);
        $this->redirect('/account');
    }

    public function logout(): void
    {
        $this->auth->logout();
        $this->session->flash('message', ['type' => 'success', 'text' => 'Çıkış yapıldı.']);
        $this->redirect('/');
    }

    public function showRegister(): void
    {
        view('client/register', [
            'title' => 'Yeni Hesap Oluştur',
            'csrf' => $this->security->csrfToken(),
            'flash' => $this->session->flash('message'),
        ]);
    }

    public function register(): void
    {
        if (!$this->security->verifyCsrf($_POST['_token'] ?? '')) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Oturum doğrulaması başarısız.']);
            $this->redirect('/account/register');
        }
        $data = Security::sanitizeArray($_POST);
        $errors = Validator::required($data, [
            'name' => 'Ad alanı zorunludur.',
            'email' => 'E-posta alanı zorunludur.',
            'password' => 'Parola zorunludur.',
        ]);
        if (strlen($data['password'] ?? '') < 8) {
            $errors['password'] = 'Parola en az 8 karakter olmalıdır.';
        }
        if (($data['password'] ?? '') !== ($data['password_confirmation'] ?? '')) {
            $errors['password_confirmation'] = 'Parolalar eşleşmiyor.';
        }
        if ($errors) {
            $this->session->flash('message', ['type' => 'danger', 'text' => implode(' ', $errors)]);
            $this->redirect('/account/register');
        }
        $existing = \App\Models\User::findByEmail($data['email']);
        if ($existing) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Bu e-posta ile kayıtlı bir hesap bulunuyor.']);
            $this->redirect('/account/register');
        }
        $userId = $this->customerService->register($data);
        $this->session->flash('message', ['type' => 'success', 'text' => 'Kayıt başarılı! Lütfen giriş yapın.']);
        $this->redirect('/account/login');
    }

    public function showForgot(): void
    {
        view('client/forgot_password', [
            'title' => 'Şifre Sıfırlama',
            'csrf' => $this->security->csrfToken(),
            'flash' => $this->session->flash('message'),
        ]);
    }

    public function sendReset(): void
    {
        if (!$this->security->verifyCsrf($_POST['_token'] ?? '')) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Oturum doğrulaması başarısız.']);
            $this->redirect('/account/forgot');
        }
        $email = trim($_POST['email'] ?? '');
        if (!$email) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'E-posta zorunludur.']);
            $this->redirect('/account/forgot');
        }
        $token = $this->customerService->createResetToken($email);
        if ($token) {
            $this->session->flash('message', ['type' => 'success', 'text' => 'Şifre sıfırlama bağlantısı e-posta adresinize gönderildi.']);
        } else {
            $this->session->flash('message', ['type' => 'info', 'text' => 'Eğer kayıtlı bir hesabınız varsa e-posta gönderildi.']);
        }
        $this->redirect('/account/forgot');
    }

    public function showReset(): void
    {
        $token = $_GET['token'] ?? '';
        view('client/reset_password', [
            'title' => 'Parolayı Yenile',
            'csrf' => $this->security->csrfToken(),
            'token' => $token,
            'flash' => $this->session->flash('message'),
        ]);
    }

    public function reset(): void
    {
        if (!$this->security->verifyCsrf($_POST['_token'] ?? '')) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Oturum doğrulaması başarısız.']);
            $this->redirect('/account/reset?token=' . urlencode($_POST['token'] ?? ''));
        }
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmation = $_POST['password_confirmation'] ?? '';
        if ($password !== $confirmation || strlen($password) < 8) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Parola gereksinimleri karşılanmadı.']);
            $this->redirect('/account/reset?token=' . urlencode($token));
        }
        if ($this->customerService->resetPassword($token, $password)) {
            $this->session->flash('message', ['type' => 'success', 'text' => 'Parolanız yenilendi. Giriş yapabilirsiniz.']);
            $this->redirect('/account/login');
        }
        $this->session->flash('message', ['type' => 'danger', 'text' => 'Geçersiz veya süresi dolmuş bağlantı.']);
        $this->redirect('/account/reset?token=' . urlencode($token));
    }
}
