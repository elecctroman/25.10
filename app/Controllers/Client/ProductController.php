<?php

declare(strict_types=1);

namespace App\Controllers\Client;

use App\Core\Controller;
use App\Core\Security;
use App\Core\Session;
use App\Core\View;
use App\Models\ClientEvent;
use App\Services\CategoryService;
use App\Services\ProductService;

class ProductController extends Controller
{
    private ProductService $productService;
    private CategoryService $categoryService;

    public function __construct(View $view, Session $session, Security $security, ProductService $productService, CategoryService $categoryService)
    {
        parent::__construct($view, $session, $security);
        $this->productService = $productService;
        $this->categoryService = $categoryService;
    }

    public function index(): void
    {
        $filters = [
            'q' => $_GET['q'] ?? null,
            'min_price' => $_GET['min_price'] ?? null,
            'max_price' => $_GET['max_price'] ?? null,
            'type' => $_GET['type'] ?? null,
        ];
        $page = (int) ($_GET['page'] ?? 1);
        $products = $this->productService->list($filters, max(1, $page));
        $menu = $this->categoryService->menuTree();
        ClientEvent::create([
            'event_type' => 'page_view',
            'payload' => json_encode(['path' => '/products', 'filters' => $filters], JSON_UNESCAPED_UNICODE),
            'created_at' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
        view('client/products', [
            'title' => 'Ürünler',
            'products' => $products,
            'menu' => $menu,
            'filters' => $filters,
            'csrf' => $this->security->csrfToken(),
            'flash' => $this->session->flash('message'),
        ]);
    }

    public function category(string $slug): void
    {
        $category = $this->categoryService->findBySlug($slug);
        if (!$category) {
            http_response_code(404);
            echo 'Kategori bulunamadı';
            return;
        }
        $filters = [
            'category_id' => $category['id'],
            'q' => $_GET['q'] ?? null,
            'min_price' => $_GET['min_price'] ?? null,
            'max_price' => $_GET['max_price'] ?? null,
            'type' => $_GET['type'] ?? null,
        ];
        $page = (int) ($_GET['page'] ?? 1);
        $products = $this->productService->list($filters, max(1, $page));
        ClientEvent::create([
            'event_type' => 'category_view',
            'payload' => json_encode(['category' => $slug, 'filters' => $filters], JSON_UNESCAPED_UNICODE),
            'created_at' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
        view('client/products', [
            'title' => $category['name'],
            'products' => $products,
            'menu' => $this->categoryService->menuTree(),
            'filters' => $filters,
            'category' => $category,
            'csrf' => $this->security->csrfToken(),
            'flash' => $this->session->flash('message'),
        ]);
    }

    public function show(string $slug): void
    {
        $product = $this->productService->detail($slug);
        if (!$product) {
            http_response_code(404);
            echo 'Ürün bulunamadı';
            return;
        }
        $related = $this->productService->related((int) $product['category_id'], (int) $product['id']);
        ClientEvent::create([
            'event_type' => 'product_view',
            'payload' => json_encode(['product' => $slug], JSON_UNESCAPED_UNICODE),
            'created_at' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
        view('client/product_detail', [
            'title' => $product['name'],
            'product' => $product,
            'related' => $related,
            'csrf' => $this->security->csrfToken(),
            'flash' => $this->session->flash('message'),
        ]);
    }
}
