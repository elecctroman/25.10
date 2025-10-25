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
use App\Services\CartService;
use App\Services\OrderService;

class CheckoutController extends Controller
{
    private CartService $cartService;
    private OrderService $orderService;
    private Auth $auth;

    public function __construct(View $view, Session $session, Security $security, CartService $cartService, OrderService $orderService, Auth $auth)
    {
        parent::__construct($view, $session, $security);
        $this->cartService = $cartService;
        $this->orderService = $orderService;
        $this->auth = $auth;
    }

    public function index(): void
    {
        $summary = $this->cartService->summary();
        if (empty($summary['items'])) {
            $this->session->flash('message', ['type' => 'info', 'text' => 'Sepetiniz boş.']);
            $this->redirect('/cart');
        }
        view('client/checkout', [
            'title' => 'Ödeme',
            'cart' => $summary,
            'user' => $this->auth->user(),
            'csrf' => $this->security->csrfToken(),
            'flash' => $this->session->flash('message'),
        ]);
    }

    public function process(): void
    {
        if (!$this->security->verifyCsrf($_POST['_token'] ?? '')) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Oturum doğrulaması başarısız.']);
            $this->redirect('/checkout');
        }
        $summary = $this->cartService->summary();
        if (empty($summary['items'])) {
            $this->session->flash('message', ['type' => 'info', 'text' => 'Sepetiniz boş.']);
            $this->redirect('/cart');
        }
        $data = Security::sanitizeArray($_POST);
        $errors = Validator::required($data, [
            'email' => 'E-posta gerekli.',
        ]);
        if ($errors) {
            $this->session->flash('message', ['type' => 'danger', 'text' => implode(' ', $errors)]);
            $this->redirect('/checkout');
        }
        $user = $this->auth->user();
        if ($user && $user['role'] !== 'customer') {
            $user = null;
        }
        $customer = [
            'id' => $user['id'] ?? null,
            'email' => $user['email'] ?? $data['email'],
            'name' => $user['name'] ?? ($data['name'] ?? 'Müşteri'),
        ];
        $orderItems = [];
        foreach ($summary['items'] as $key => $item) {
            $orderItems[] = [
                'product_id' => $item['product_id'],
                'variant_id' => $item['variant_id'],
                'qty' => $item['qty'],
                'unit_price' => $item['price'],
                'tax_rate' => $item['tax_rate'],
                'total' => ($item['price'] + ($item['price'] * $item['tax_rate'] / 100)) * $item['qty'],
            ];
        }
        $coupon = $this->cartService->coupon();
        $orderId = $this->orderService->createFromCart($customer, $summary, $orderItems, [
            'coupon_id' => $coupon['id'] ?? null,
            'mark_delivered' => true,
        ]);
        ClientEvent::create([
            'event_type' => 'purchase',
            'payload' => json_encode(['order_id' => $orderId, 'total' => $summary['total']], JSON_UNESCAPED_UNICODE),
            'created_at' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
        $this->cartService->clear();
        view('client/checkout_success', [
            'title' => 'Sipariş Alındı',
            'order_id' => $orderId,
            'summary' => $summary,
            'csrf' => $this->security->csrfToken(),
            'flash' => $this->session->flash('message'),
        ]);
    }
}
