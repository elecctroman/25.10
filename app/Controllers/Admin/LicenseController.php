<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Security;
use App\Core\Session;
use App\Core\View;
use App\Models\LicenseKey;
use App\Models\Product;
use App\Services\LicenseService;
use App\Services\ProductService;

class LicenseController extends Controller
{
    private ProductService $productService;
    private LicenseService $licenseService;

    public function __construct(View $view, Session $session, Security $security, ProductService $productService, LicenseService $licenseService)
    {
        parent::__construct($view, $session, $security);
        $this->productService = $productService;
        $this->licenseService = $licenseService;
    }

    public function index(): void
    {
        view('admin/licenses', [
            'title' => 'Lisans Anahtarları',
            'products' => Product::all(),
            'keys' => LicenseKey::paginate(50),
            'flash' => $this->session->flash('message'),
            'csrf' => $this->security->csrfToken(),
        ]);
    }

    public function import(): void
    {
        if (!$this->security->verifyCsrf($_POST['_token'] ?? '')) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Geçersiz oturum.']);
            $this->redirect('/admin/licenses');
        }
        $productId = (int) ($_POST['product_id'] ?? 0);
        $codes = preg_split('/\r?\n/', $_POST['codes'] ?? '');
        $count = $this->productService->importKeys($productId, $codes, auth_user()['id'] ?? null);
        $this->session->flash('message', ['type' => 'success', 'text' => $count . ' anahtar içe aktarıldı.']);
        $this->redirect('/admin/licenses');
    }

    public function bulkStatus(): void
    {
        if (!$this->security->verifyCsrf($_POST['_token'] ?? '')) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Geçersiz oturum.']);
            $this->redirect('/admin/licenses');
        }
        $ids = array_map('intval', $_POST['ids'] ?? []);
        if (empty($ids) && !empty($_POST['id_list'])) {
            $ids = array_map('intval', array_filter(array_map('trim', explode(',', (string) $_POST['id_list']))));
        }
        $status = $_POST['status'] ?? 'available';
        $this->licenseService->bulkUpdateStatus($ids, $status, auth_user()['id'] ?? null);
        $this->session->flash('message', ['type' => 'success', 'text' => 'Durum güncellendi.']);
        $this->redirect('/admin/licenses');
    }
}
