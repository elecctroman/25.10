<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Security;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;
use App\Models\Order;
use App\Models\User;

class CustomerController extends Controller
{
    public function __construct(View $view, Session $session, Security $security)
    {
        parent::__construct($view, $session, $security);
    }

    public function index(): void
    {
        $customers = $this->getCustomers();
        $ordersByCustomer = [];
        foreach ($customers as $customer) {
            $ordersByCustomer[$customer['id']] = $this->customerOrders((int) $customer['id']);
        }
        view('admin/customers', [
            'title' => 'Müşteriler',
            'customers' => $customers,
            'ordersByCustomer' => $ordersByCustomer,
            'csrf' => $this->security->csrfToken(),
            'flash' => $this->session->flash('message'),
        ]);
    }

    public function store(): void
    {
        if (!$this->security->verifyCsrf($_POST['_token'] ?? '')) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Geçersiz oturum.']);
            $this->redirect('/admin/customers');
        }
        $errors = Validator::required($_POST, [
            'email' => 'E-posta zorunludur.',
            'name' => 'Ad zorunludur.',
        ]);
        if ($errors) {
            $this->session->flash('message', ['type' => 'danger', 'text' => implode(' ', $errors)]);
            $this->redirect('/admin/customers');
        }
        $password = password_hash($_POST['password'] ?: 'Geçici123!', PASSWORD_DEFAULT);
        $id = User::create([
            'email' => $_POST['email'],
            'password_hash' => $password,
            'name' => $_POST['name'],
            'role' => 'customer',
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        \App\Core\AuditLogger::log(auth_user()['id'] ?? null, 'customer.create', 'user', $id);
        $this->session->flash('message', ['type' => 'success', 'text' => 'Müşteri eklendi.']);
        $this->redirect('/admin/customers');
    }

    private function getCustomers(): array
    {
        global $container;
        /** @var \App\Core\Database $database */
        $database = $container['db'];
        $stmt = $database->pdo()->prepare('SELECT * FROM users WHERE role = "customer" ORDER BY created_at DESC');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function customerOrders(int $customerId): array
    {
        global $container;
        /** @var \App\Core\Database $database */
        $database = $container['db'];
        $stmt = $database->pdo()->prepare('SELECT * FROM orders WHERE customer_id = :id ORDER BY created_at DESC');
        $stmt->execute(['id' => $customerId]);
        return $stmt->fetchAll();
    }
}
