<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Security;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;
use App\Integrations\StubProvider;
use App\Models\Provider;
use App\Models\ProviderEvent;

class ProviderController extends Controller
{
    public function __construct(View $view, Session $session, Security $security)
    {
        parent::__construct($view, $session, $security);
    }

    public function index(): void
    {
        $provider = new StubProvider();
        view('admin/providers', [
            'title' => 'Sağlayıcılar',
            'providers' => Provider::paginate(50),
            'balance' => $provider->getBalance(),
            'events' => ProviderEvent::paginate(20),
            'csrf' => $this->security->csrfToken(),
            'flash' => $this->session->flash('message'),
        ]);
    }

    public function store(): void
    {
        if (!$this->security->verifyCsrf($_POST['_token'] ?? '')) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Geçersiz oturum.']);
            $this->redirect('/admin/providers');
        }
        $errors = Validator::required($_POST, [
            'name' => 'Sağlayıcı adı zorunludur.',
            'key' => 'Anahtar zorunludur.',
        ]);
        if ($errors) {
            $this->session->flash('message', ['type' => 'danger', 'text' => implode(' ', $errors)]);
            $this->redirect('/admin/providers');
        }
        Provider::create([
            'name' => $_POST['name'],
            'key' => $_POST['key'],
            'status' => $_POST['status'] ?? 'active',
            'config' => json_encode(['api_key' => $_POST['api_key'] ?? ''], JSON_UNESCAPED_UNICODE),
        ]);
        $this->session->flash('message', ['type' => 'success', 'text' => 'Sağlayıcı kaydedildi.']);
        $this->redirect('/admin/providers');
    }
}
