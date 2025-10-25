<?php

declare(strict_types=1);

namespace App\Controllers\Client;

use App\Core\Controller;
use App\Core\Mailer;
use App\Core\Security;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;
use App\Models\Setting;

class SupportController extends Controller
{
    private Mailer $mailer;

    public function __construct(View $view, Session $session, Security $security, Mailer $mailer)
    {
        parent::__construct($view, $session, $security);
        $this->mailer = $mailer;
    }

    public function form(): void
    {
        view('client/support', [
            'title' => 'Destek',
            'csrf' => $this->security->csrfToken(),
            'flash' => $this->session->flash('message'),
        ]);
    }

    public function submit(): void
    {
        if (!$this->security->verifyCsrf($_POST['_token'] ?? '')) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Oturum doğrulaması başarısız.']);
            $this->redirect('/destek');
        }
        $data = Security::sanitizeArray($_POST);
        $errors = Validator::required($data, [
            'email' => 'E-posta zorunludur.',
            'subject' => 'Konu zorunludur.',
            'message' => 'Mesaj zorunludur.',
        ]);
        if ($errors) {
            $this->session->flash('message', ['type' => 'danger', 'text' => implode(' ', $errors)]);
            $this->redirect('/destek');
        }
        $supportEmail = Setting::get('support_email', $this->mailerConfigFrom() ?? 'support@example.com');
        $body = "Gönderen: {$data['email']}\nKonu: {$data['subject']}\nMesaj:\n{$data['message']}";
        $this->mailer->send($supportEmail, '[Destek] ' . $data['subject'], $body);
        $this->session->flash('message', ['type' => 'success', 'text' => 'Mesajınız alındı. En kısa sürede dönüş yapılacaktır.']);
        $this->redirect('/destek');
    }

    private function mailerConfigFrom(): ?string
    {
        global $container;
        $config = $container['config'];
        $mail = $config->get('mail');
        return $mail['from_email'] ?? null;
    }
}
