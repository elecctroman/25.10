<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Security;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;

class ProductController extends Controller
{
    private ProductService $productService;

    public function __construct(View $view, Session $session, Security $security, ProductService $productService)
    {
        parent::__construct($view, $session, $security);
        $this->productService = $productService;
    }

    public function index(): void
    {
        view('admin/products_list', [
            'title' => 'Ürünler',
            'products' => Product::withCategory(),
            'flash' => $this->session->flash('message'),
            'csrf' => $this->security->csrfToken(),
        ]);
    }

    public function form(int $id = 0): void
    {
        $product = $id ? Product::findWithRelations($id) : null;
        view('admin/product_form', [
            'title' => $id ? 'Ürün Düzenle' : 'Yeni Ürün',
            'product' => $product,
            'categories' => Category::all(),
            'csrf' => $this->security->csrfToken(),
        ]);
    }

    public function store(): void
    {
        if (!$this->security->verifyCsrf($_POST['_token'] ?? '')) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Geçersiz oturum.']);
            $this->redirect('/admin/products');
        }
        $errors = Validator::required($_POST, [
            'name' => 'Ürün adı zorunludur.',
            'price' => 'Fiyat zorunludur.',
        ]);
        if ($errors) {
            $this->session->flash('message', ['type' => 'danger', 'text' => implode(' ', $errors)]);
            $this->redirect('/admin/products/create');
        }
        $variants = $this->parseVariants($_POST);
        $user = auth_user();
        $this->productService->create([
            'category_id' => $_POST['category_id'] !== '' ? (int) $_POST['category_id'] : null,
            'type' => $_POST['type'] ?? 'license',
            'sku' => $_POST['sku'] ?? '',
            'name' => $_POST['name'],
            'slug' => strtolower(preg_replace('/[^a-z0-9-]+/i', '-', $_POST['name'])),
            'description' => $_POST['description'] ?? '',
            'price' => (float) $_POST['price'],
            'currency' => $_POST['currency'] ?? 'TRY',
            'tax_rate' => (float) ($_POST['tax_rate'] ?? 0),
            'status' => $_POST['status'] ?? 'active',
            'created_by' => $user['id'] ?? null,
        ], $variants);
        $this->session->flash('message', ['type' => 'success', 'text' => 'Ürün oluşturuldu.']);
        $this->redirect('/admin/products');
    }

    public function update(int $id): void
    {
        if (!$this->security->verifyCsrf($_POST['_token'] ?? '')) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Geçersiz oturum.']);
            $this->redirect('/admin/products');
        }
        $variants = $this->parseVariants($_POST);
        $user = auth_user();
        $this->productService->update($id, [
            'category_id' => $_POST['category_id'] !== '' ? (int) $_POST['category_id'] : null,
            'type' => $_POST['type'] ?? 'license',
            'sku' => $_POST['sku'] ?? '',
            'name' => $_POST['name'],
            'slug' => strtolower(preg_replace('/[^a-z0-9-]+/i', '-', $_POST['name'])),
            'description' => $_POST['description'] ?? '',
            'price' => (float) $_POST['price'],
            'currency' => $_POST['currency'] ?? 'TRY',
            'tax_rate' => (float) ($_POST['tax_rate'] ?? 0),
            'status' => $_POST['status'] ?? 'active',
            'updated_by' => $user['id'] ?? null,
        ], $variants);
        $this->session->flash('message', ['type' => 'success', 'text' => 'Ürün güncellendi.']);
        $this->redirect('/admin/products');
    }

    public function delete(int $id): void
    {
        if ($id && $this->security->verifyCsrf($_POST['_token'] ?? '')) {
            Product::delete($id);
            $this->session->flash('message', ['type' => 'success', 'text' => 'Ürün silindi.']);
        }
        $this->redirect('/admin/products');
    }

    private function parseVariants(array $input): array
    {
        $variants = [];
        $names = $input['variant_name'] ?? [];
        $ids = $input['variant_id'] ?? [];
        $priceDeltas = $input['variant_price'] ?? [];
        $skus = $input['variant_sku'] ?? [];

        foreach ($names as $index => $name) {
            if (trim((string) $name) === '') {
                continue;
            }
            $variants[] = [
                'id' => isset($ids[$index]) && $ids[$index] !== '' ? (int) $ids[$index] : null,
                'name' => $name,
                'price_delta' => isset($priceDeltas[$index]) ? (float) $priceDeltas[$index] : 0,
                'sku' => $skus[$index] ?? '',
            ];
        }
        return $variants;
    }
}
