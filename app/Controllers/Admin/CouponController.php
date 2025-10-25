<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Security;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;
use App\Models\Coupon;
use App\Services\CouponService;

class CouponController extends Controller
{
    private CouponService $couponService;

    public function __construct(View $view, Session $session, Security $security, CouponService $couponService)
    {
        parent::__construct($view, $session, $security);
        $this->couponService = $couponService;
    }

    public function index(): void
    {
        view('admin/coupons', [
            'title' => 'Kuponlar',
            'coupons' => Coupon::paginate(50),
            'csrf' => $this->security->csrfToken(),
            'flash' => $this->session->flash('message'),
        ]);
    }

    public function store(): void
    {
        if (!$this->security->verifyCsrf($_POST['_token'] ?? '')) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Geçersiz oturum.']);
            $this->redirect('/admin/coupons');
        }
        $errors = Validator::required($_POST, [
            'code' => 'Kupon kodu zorunludur.',
            'type' => 'Kupon tipi zorunludur.',
            'value' => 'Değer zorunludur.',
        ]);
        if ($errors) {
            $this->session->flash('message', ['type' => 'danger', 'text' => implode(' ', $errors)]);
            $this->redirect('/admin/coupons');
        }
        $this->couponService->create([
            'code' => strtoupper($_POST['code']),
            'type' => $_POST['type'],
            'value' => (float) $_POST['value'],
            'min_total' => (float) ($_POST['min_total'] ?? 0),
            'max_discount' => (float) ($_POST['max_discount'] ?? 0),
            'start_at' => $_POST['start_at'] ?? null,
            'end_at' => $_POST['end_at'] ?? null,
            'usage_limit' => (int) ($_POST['usage_limit'] ?? 0),
            'status' => $_POST['status'] ?? 'active',
        ], auth_user()['id'] ?? null);
        $this->session->flash('message', ['type' => 'success', 'text' => 'Kupon oluşturuldu.']);
        $this->redirect('/admin/coupons');
    }

    public function update(): void
    {
        if (!$this->security->verifyCsrf($_POST['_token'] ?? '')) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Geçersiz oturum.']);
            $this->redirect('/admin/coupons');
        }
        $id = (int) $_POST['id'];
        $this->couponService->update($id, [
            'code' => strtoupper($_POST['code']),
            'type' => $_POST['type'],
            'value' => (float) $_POST['value'],
            'min_total' => (float) ($_POST['min_total'] ?? 0),
            'max_discount' => (float) ($_POST['max_discount'] ?? 0),
            'start_at' => $_POST['start_at'] ?? null,
            'end_at' => $_POST['end_at'] ?? null,
            'usage_limit' => (int) ($_POST['usage_limit'] ?? 0),
            'status' => $_POST['status'] ?? 'active',
        ], auth_user()['id'] ?? null);
        $this->session->flash('message', ['type' => 'success', 'text' => 'Kupon güncellendi.']);
        $this->redirect('/admin/coupons');
    }
}
