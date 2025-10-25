<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Security;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;
use App\Models\Delivery;
use App\Models\LicenseKey;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\OrderService;

class OrderController extends Controller
{
    private OrderService $orderService;

    public function __construct(View $view, Session $session, Security $security, OrderService $orderService)
    {
        parent::__construct($view, $session, $security);
        $this->orderService = $orderService;
    }

    public function index(): void
    {
        view('admin/orders', [
            'title' => 'Siparişler',
            'orders' => Order::paginate(25),
            'products' => Product::all(),
            'csrf' => $this->security->csrfToken(),
            'flash' => $this->session->flash('message'),
        ]);
    }

    public function view(int $id): void
    {
        $order = Order::findWithItems($id);
        view('admin/order_view', [
            'title' => 'Sipariş Detayı',
            'order' => $order,
            'deliveries' => $order ? array_map(fn($item) => Delivery::byOrderItem((int) $item['id']), $order['items']) : [],
            'csrf' => $this->security->csrfToken(),
        ]);
    }

    public function status(): void
    {
        if (!$this->security->verifyCsrf($_POST['_token'] ?? '')) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Geçersiz oturum.']);
            $this->redirect('/admin/orders');
        }
        $orderId = (int) ($_POST['order_id'] ?? 0);
        $status = $_POST['status'] ?? 'pending';
        $this->orderService->updateStatus($orderId, $status, auth_user()['id'] ?? null);
        $this->session->flash('message', ['type' => 'success', 'text' => 'Sipariş durumu güncellendi.']);
        $this->redirect('/admin/orders/view?id=' . $orderId);
    }

    public function delivery(): void
    {
        if (!$this->security->verifyCsrf($_POST['_token'] ?? '')) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Geçersiz oturum.']);
            $this->redirect('/admin/orders');
        }
        $orderItemId = (int) ($_POST['order_item_id'] ?? 0);
        $type = $_POST['type'] ?? 'inline_text';
        $payload = $_POST['payload'] ?? '';
        $this->orderService->addDelivery($orderItemId, $type, $payload, auth_user()['id'] ?? null);
        $this->session->flash('message', ['type' => 'success', 'text' => 'Teslimat kaydedildi.']);
        $this->redirect('/admin/orders/view?id=' . ((int) $_POST['order_id']));
    }

    public function create(): void
    {
        if (!$this->security->verifyCsrf($_POST['_token'] ?? '')) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Geçersiz oturum.']);
            $this->redirect('/admin/orders');
        }
        $productId = (int) $_POST['product_id'];
        $qty = (int) $_POST['qty'];
        $product = Product::find($productId);
        $variantId = $_POST['variant_id'] !== '' ? (int) $_POST['variant_id'] : null;
        $unitPrice = $product ? (float) $product['price'] : 0;
        $taxRate = $product ? (float) $product['tax_rate'] : 0;
        $orderNo = 'ORD' . time();
        $orderId = $this->orderService->create([
            'order_no' => $orderNo,
            'customer_id' => null,
            'email' => $_POST['email'] ?? 'manual@example.com',
            'total_net' => 0,
            'total_tax' => 0,
            'total_gross' => 0,
            'currency' => $product['currency'] ?? 'TRY',
            'status' => $_POST['status'] ?? 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'delivery_strategy' => $_POST['delivery_strategy'] ?? 'auto_key',
        ], [[
            'product_id' => $productId,
            'variant_id' => $variantId,
            'qty' => $qty,
            'unit_price' => $unitPrice,
            'tax_rate' => $taxRate,
            'total' => ($unitPrice + ($unitPrice * $taxRate / 100)) * $qty,
        ]], auth_user()['id'] ?? null);
        $this->session->flash('message', ['type' => 'success', 'text' => 'Manuel sipariş #' . $orderNo . ' oluşturuldu.']);
        $this->redirect('/admin/orders/view?id=' . $orderId);
    }
}
