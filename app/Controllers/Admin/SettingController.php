<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Mailer;
use App\Core\Security;
use App\Core\Session;
use App\Core\View;
use App\Models\Setting;
use App\Services\SettingService;

class SettingController extends Controller
{
    private SettingService $settingService;
    private Mailer $mailer;

    public function __construct(View $view, Session $session, Security $security, SettingService $settingService, Mailer $mailer)
    {
        parent::__construct($view, $session, $security);
        $this->settingService = $settingService;
        $this->mailer = $mailer;
    }

    public function index(): void
    {
        view('admin/settings', [
            'title' => 'Ayarlar',
            'settings' => $this->loadSettings(),
            'csrf' => $this->security->csrfToken(),
            'flash' => $this->session->flash('message'),
        ]);
    }

    public function save(): void
    {
        if (!$this->security->verifyCsrf($_POST['_token'] ?? '')) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Geçersiz oturum.']);
            $this->redirect('/admin/settings');
        }
        $settings = [
            'site.name' => $_POST['site_name'],
            'site.logo' => $_POST['site_logo'],
            'mail.host' => $_POST['mail_host'],
            'mail.port' => (int) $_POST['mail_port'],
            'mail.username' => $_POST['mail_username'],
            'mail.password' => $_POST['mail_password'],
            'mail.from' => $_POST['mail_from'],
            'tax.default_rate' => (float) $_POST['tax_default_rate'],
            'currency.default' => $_POST['currency_default'],
        ];
        $this->settingService->save($settings, auth_user()['id'] ?? null);
        $this->session->flash('message', ['type' => 'success', 'text' => 'Ayarlar kaydedildi.']);
        $this->redirect('/admin/settings');
    }

    public function testEmail(): void
    {
        if (!$this->security->verifyCsrf($_POST['_token'] ?? '')) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Geçersiz oturum.']);
            $this->redirect('/admin/settings');
        }
        $email = $_POST['test_email'] ?? ''; 
        $result = $this->mailer->send($email, 'Test E-postası', 'Bu bir test e-postasıdır.');
        $this->session->flash('message', ['type' => $result ? 'success' : 'danger', 'text' => $result ? 'E-posta gönderildi.' : 'E-posta gönderilemedi.']);
        $this->redirect('/admin/settings');
    }

    private function loadSettings(): array
    {
        return [
            'site_name' => Setting::get('site.name', 'Dijital Satış Paneli'),
            'site_logo' => Setting::get('site.logo', ''),
            'mail_host' => Setting::get('mail.host', ''),
            'mail_port' => Setting::get('mail.port', 25),
            'mail_username' => Setting::get('mail.username', ''),
            'mail_password' => Setting::get('mail.password', ''),
            'mail_from' => Setting::get('mail.from', 'no-reply@example.com'),
            'tax_default_rate' => Setting::get('tax.default_rate', 18),
            'currency_default' => Setting::get('currency.default', 'TRY'),
        ];
    }
}
