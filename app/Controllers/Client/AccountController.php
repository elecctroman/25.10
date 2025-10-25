<?php

declare(strict_types=1);

namespace App\Controllers\Client;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Security;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;
use App\Models\ClientEvent;
use App\Models\Order;
use App\Services\CustomerService;

class AccountController extends Controller
{
    private Auth $auth;
    private CustomerService $customerService;

    public function __construct(View $view, Session $session, Security $security, Auth $auth, CustomerService $customerService)
    {
        parent::__construct($view, $session, $security);
        $this->auth = $auth;
        $this->customerService = $customerService;
    }

    public function dashboard(): void
    {
        $this->requireCustomer();
        $user = $this->auth->user();
        $stats = $this->customerService->dashboardStats((int) $user['id']);
        ClientEvent::create([
            'event_type' => 'account_view',
            'payload' => json_encode(['user_id' => $user['id']], JSON_UNESCAPED_UNICODE),
            'created_at' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
        view('client/account_dashboard', [
            'title' => 'Hesabım',
            'user' => $user,
            'stats' => $stats,
            'csrf' => $this->security->csrfToken(),
            'flash' => $this->session->flash('message'),
        ]);
    }

    public function orders(): void
    {
        $this->requireCustomer();
        $user = $this->auth->user();
        $orders = Order::byCustomer((int) $user['id']);
        view('client/order_history', [
            'title' => 'Siparişlerim',
            'orders' => $orders,
            'csrf' => $this->security->csrfToken(),
            'flash' => $this->session->flash('message'),
        ]);
    }

    public function orderDetail(string $orderNo): void
    {
        $this->requireCustomer();
        $user = $this->auth->user();
        $order = Order::findByNumber($orderNo, (int) $user['id']);
        if ($order) {
            foreach ($order['items'] as &$item) {
                $item['deliveries'] = \App\Models\Delivery::byOrderItem((int) $item['id']);
            }
        }
        if (!$order) {
            http_response_code(404);
            echo 'Sipariş bulunamadı';
            return;
        }
        view('client/order_detail', [
            'title' => 'Sipariş #' . $orderNo,
            'order' => $order,
            'csrf' => $this->security->csrfToken(),
            'flash' => $this->session->flash('message'),
        ]);
    }

    public function updateProfile(): void
    {
        $this->requireCustomer();
        if (!$this->security->verifyCsrf($_POST['_token'] ?? '')) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Oturum doğrulaması başarısız.']);
            $this->redirect('/account');
        }
        $user = $this->auth->user();
        $data = Security::sanitizeArray($_POST);
        $errors = Validator::required($data, [
            'name' => 'Ad zorunludur.',
            'email' => 'E-posta zorunludur.',
        ]);
        if ($errors) {
            $this->session->flash('message', ['type' => 'danger', 'text' => implode(' ', $errors)]);
            $this->redirect('/account');
        }
        $this->customerService->updateProfile((int) $user['id'], $data);
        $this->session->flash('message', ['type' => 'success', 'text' => 'Bilgileriniz güncellendi.']);
        $this->redirect('/account');
    }

    public function changePassword(): void
    {
        $this->requireCustomer();
        if (!$this->security->verifyCsrf($_POST['_token'] ?? '')) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Oturum doğrulaması başarısız.']);
            $this->redirect('/account');
        }
        $password = $_POST['password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirm = $_POST['new_password_confirmation'] ?? '';
        if ($newPassword !== $confirm || strlen($newPassword) < 8) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Parola gereksinimleri karşılanmadı.']);
            $this->redirect('/account');
        }
        $user = $this->auth->user();
        if (!$this->customerService->changePassword((int) $user['id'], $password, $newPassword)) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Mevcut parolanız hatalı.']);
            $this->redirect('/account');
        }
        $this->session->flash('message', ['type' => 'success', 'text' => 'Parolanız güncellendi.']);
        $this->redirect('/account');
    }

    private function requireCustomer(): void
    {
        $user = $this->auth->user();
        if (!$user || $user['role'] !== 'customer') {
            $this->session->flash('message', ['type' => 'info', 'text' => 'Devam etmek için giriş yapmalısınız.']);
            $this->redirect('/account/login');
        }
    }
}
