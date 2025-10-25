<?php

declare(strict_types=1);

namespace App\Controllers\Client;

use App\Core\Controller;
use App\Core\Security;
use App\Core\Session;
use App\Core\View;
use App\Services\CartService;

class CartController extends Controller
{
    private CartService $cartService;

    public function __construct(View $view, Session $session, Security $security, CartService $cartService)
    {
        parent::__construct($view, $session, $security);
        $this->cartService = $cartService;
    }

    public function index(): void
    {
        view('client/cart', [
            'title' => 'Sepetim',
            'cart' => $this->cartService->summary(),
            'csrf' => $this->security->csrfToken(),
            'flash' => $this->session->flash('message'),
        ]);
    }

    public function add(): void
    {
        $this->requireJsonCsrf();
        try {
            $productId = (int) ($_POST['product_id'] ?? 0);
            $variantId = $_POST['variant_id'] !== '' ? (int) $_POST['variant_id'] : null;
            $qty = max(1, (int) ($_POST['qty'] ?? 1));
            $item = $this->cartService->add($productId, $variantId, $qty);
            $summary = $this->cartService->summary();
        $this->respondJson(['success' => true, 'item' => $item, 'summary' => $summary]);
        } catch (\Throwable $e) {
            $this->respondJson(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function update(): void
    {
        $this->requireJsonCsrf();
        $key = $_POST['key'] ?? '';
        $qty = max(0, (int) ($_POST['qty'] ?? 1));
        $this->cartService->updateQuantity($key, $qty);
        $this->respondJson(['success' => true, 'summary' => $this->cartService->summary()]);
    }

    public function remove(): void
    {
        $this->requireJsonCsrf();
        $key = $_POST['key'] ?? '';
        $this->cartService->remove($key);
        $this->respondJson(['success' => true, 'summary' => $this->cartService->summary()]);
    }

    public function applyCoupon(): void
    {
        $this->requireJsonCsrf();
        try {
            $coupon = $this->cartService->applyCoupon(trim($_POST['code'] ?? ''));
            $this->respondJson(['success' => true, 'coupon' => $coupon, 'summary' => $this->cartService->summary()]);
        } catch (\Throwable $e) {
            $this->respondJson(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    private function requireJsonCsrf(): void
    {
        if (!$this->security->verifyCsrf($_POST['_token'] ?? '')) {
            $this->respondJson(['success' => false, 'message' => 'Oturum doğrulaması başarısız.'], 419);
        }
    }

    private function respondJson(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        if ($status !== 204) {
            $payload['token'] = $this->security->csrfToken();
        }
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
