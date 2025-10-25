<?php

declare(strict_types=1);

namespace App\Controllers\Client;

use App\Core\Controller;
use App\Core\Security;
use App\Core\Session;
use App\Core\View;
use App\Models\Setting;
use App\Services\CategoryService;
use App\Services\CouponService;
use App\Services\ProductService;

class HomeController extends Controller
{
    private ProductService $productService;
    private CategoryService $categoryService;
    private CouponService $couponService;

    public function __construct(View $view, Session $session, Security $security, ProductService $productService, CategoryService $categoryService, CouponService $couponService)
    {
        parent::__construct($view, $session, $security);
        $this->productService = $productService;
        $this->categoryService = $categoryService;
        $this->couponService = $couponService;
    }

    public function index(): void
    {
        $featured = $this->productService->featured();
        $categories = $this->categoryService->topCategories();
        $banners = Setting::get('home_banners', []);
        view('client/home', [
            'title' => 'Dijital Ürünler',
            'featured' => $featured,
            'categories' => $categories,
            'banners' => is_array($banners) ? $banners : [],
            'csrf' => $this->security->csrfToken(),
            'flash' => $this->session->flash('message'),
        ]);
    }
}
