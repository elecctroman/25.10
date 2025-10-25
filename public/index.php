<?php

declare(strict_types=1);

use App\Core\Auth;
use App\Core\Autoloader;
use App\Core\Cache;
use App\Core\Config;
use App\Core\Database;
use App\Core\Mailer;
use App\Core\RateLimiter;
use App\Core\Router;
use App\Core\Security;
use App\Core\Session;
use App\Core\View;
use App\Controllers\Admin\AuthController as AdminAuthController;
use App\Controllers\Admin\CategoryController;
use App\Controllers\Admin\CouponController;
use App\Controllers\Admin\CustomerController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\LicenseController as AdminLicenseController;
use App\Controllers\Admin\OrderController as AdminOrderController;
use App\Controllers\Admin\ProductController as AdminProductController;
use App\Controllers\Admin\ProviderController;
use App\Controllers\Admin\SettingController;
use App\Controllers\Client\AccountController;
use App\Controllers\Client\AuthController as ClientAuthController;
use App\Controllers\Client\CartController;
use App\Controllers\Client\CheckoutController;
use App\Controllers\Client\HomeController;
use App\Controllers\Client\ProductController as ClientProductController;
use App\Controllers\Client\SupportController;
use App\Services\CartService;
use App\Services\CategoryService;
use App\Services\CouponService;
use App\Services\CustomerService;
use App\Services\LicenseService;
use App\Services\OrderService;
use App\Services\ProductService;
use App\Services\SettingService;

require dirname(__DIR__) . '/app/Core/Autoloader.php';

(new Autoloader(dirname(__DIR__)))->register();

$basePath = dirname(__DIR__);
$config = new Config($basePath . '/config');
$session = new Session();
$security = new Security($session);
$view = new View($basePath . '/app/Views/');
$database = new Database($config->get('database'));
$mailer = new Mailer($config->get('mail'));
$auth = new Auth($session);
$router = new Router();
$rateLimiter = new RateLimiter($session);
$cache = new Cache($basePath . '/storage/cache');

$GLOBALS['container'] = [
    'config' => $config,
    'session' => $session,
    'security' => $security,
    'view' => $view,
    'db' => $database,
    'auth' => $auth,
    'mailer' => $mailer,
    'rateLimiter' => $rateLimiter,
    'cache' => $cache,
];

function view(string $template, array $data = []): void
{
    global $container;
    /** @var View $view */
    $view = $container['view'];
    $view->render($template, $data);
}

function csrf_token(): string
{
    global $container;
    /** @var Security $security */
    $security = $container['security'];
    return $security->csrfToken();
}

function auth_user(): ?array
{
    global $container;
    /** @var Auth $auth */
    $auth = $container['auth'];
    return $auth->user();
}

function require_auth(array $roles = ['admin', 'staff']): void
{
    global $container;
    /** @var Auth $auth */
    $auth = $container['auth'];
    if (!$auth->check() || !in_array($auth->user()['role'], $roles, true)) {
        header('Location: /login');
        exit;
    }
}

function customer_user(): ?array
{
    $user = auth_user();
    return $user && $user['role'] === 'customer' ? $user : null;
}

function require_customer_auth(): void
{
    if (!customer_user()) {
        header('Location: /account/login');
        exit;
    }
}

function url(string $path = ''): string
{
    global $container;
    /** @var Config $config */
    $config = $container['config'];
    $base = rtrim((string) $config->get('app.base_url', ''), '/');
    $path = '/' . ltrim($path, '/');
    return ($base ?: '') . $path;
}

$productService = new ProductService();
$orderService = new OrderService();
$licenseService = new LicenseService();
$couponService = new CouponService();
$settingService = new SettingService();
$categoryService = new CategoryService();
$customerService = new CustomerService($mailer);
$cartService = new CartService($session, $couponService);

$adminAuthController = new AdminAuthController($view, $session, $security, $auth, $rateLimiter);
$dashboardController = new DashboardController($view, $session, $security);
$categoryController = new CategoryController($view, $session, $security);
$adminProductController = new AdminProductController($view, $session, $security, $productService);
$adminLicenseController = new AdminLicenseController($view, $session, $security, $productService, $licenseService);
$adminOrderController = new AdminOrderController($view, $session, $security, $orderService);
$customerController = new CustomerController($view, $session, $security);
$adminCouponController = new CouponController($view, $session, $security, $couponService);
$settingController = new SettingController($view, $session, $security, $settingService, $mailer);
$providerController = new ProviderController($view, $session, $security);

$clientAuthController = new ClientAuthController($view, $session, $security, $auth, $rateLimiter, $customerService);
$homeController = new HomeController($view, $session, $security, $productService, $categoryService, $couponService);
$clientProductController = new ClientProductController($view, $session, $security, $productService, $categoryService);
$cartController = new CartController($view, $session, $security, $cartService);
$checkoutController = new CheckoutController($view, $session, $security, $cartService, $orderService, $auth);
$accountController = new AccountController($view, $session, $security, $auth, $customerService);
$supportController = new SupportController($view, $session, $security, $mailer);

$router->get('/', fn() => $homeController->index());
$router->get('/products', fn() => $clientProductController->index());
$router->get('/kategori/{slug}', fn($slug) => $clientProductController->category($slug));
$router->get('/urun/{slug}', fn($slug) => $clientProductController->show($slug));

$router->get('/cart', fn() => $cartController->index());
$router->post('/cart/add', fn() => $cartController->add());
$router->post('/cart/update', fn() => $cartController->update());
$router->post('/cart/remove', fn() => $cartController->remove());
$router->post('/cart/apply-coupon', fn() => $cartController->applyCoupon());

$router->get('/checkout', fn() => $checkoutController->index());
$router->post('/checkout', fn() => $checkoutController->process());

$router->get('/account/login', fn() => $clientAuthController->showLogin());
$router->post('/account/login', fn() => $clientAuthController->login());
$router->get('/account/register', fn() => $clientAuthController->showRegister());
$router->post('/account/register', fn() => $clientAuthController->register());
$router->get('/account/forgot', fn() => $clientAuthController->showForgot());
$router->post('/account/forgot', fn() => $clientAuthController->sendReset());
$router->get('/account/reset', fn() => $clientAuthController->showReset());
$router->post('/account/reset', fn() => $clientAuthController->reset());
$router->post('/account/logout', fn() => $clientAuthController->logout());

$router->get('/account', fn() => $accountController->dashboard());
$router->get('/account/orders', fn() => $accountController->orders());
$router->get('/account/orders/{orderNo}', fn($orderNo) => $accountController->orderDetail($orderNo));
$router->post('/account/profile', fn() => $accountController->updateProfile());
$router->post('/account/password', fn() => $accountController->changePassword());

$router->get('/destek', fn() => $supportController->form());
$router->post('/destek', fn() => $supportController->submit());

$router->get('/login', fn() => $adminAuthController->showLogin());
$router->post('/login', fn() => $adminAuthController->login());
$router->post('/logout', fn() => $adminAuthController->logout());
$router->get('/admin', function () use ($auth, $dashboardController) {
    if ($auth->check() && in_array($auth->user()['role'], ['admin', 'staff'], true)) {
        return $dashboardController->index();
    }
    header('Location: /login');
    exit;
});

$router->get('/admin/dashboard', function () use ($dashboardController) {
    require_auth();
    return $dashboardController->index();
});
$router->get('/admin/categories', function () use ($categoryController) {
    require_auth();
    return $categoryController->index();
});
$router->post('/admin/categories/create', function () use ($categoryController) {
    require_auth();
    return $categoryController->create();
});
$router->post('/admin/categories/update', function () use ($categoryController) {
    require_auth();
    return $categoryController->update();
});
$router->post('/admin/categories/delete', function () use ($categoryController) {
    require_auth();
    return $categoryController->delete();
});

$router->get('/admin/products', function () use ($adminProductController) {
    require_auth();
    return $adminProductController->index();
});
$router->get('/admin/products/create', function () use ($adminProductController) {
    require_auth();
    return $adminProductController->form();
});
$router->post('/admin/products/store', function () use ($adminProductController) {
    require_auth();
    return $adminProductController->store();
});
$router->get('/admin/products/edit', function () use ($adminProductController) {
    require_auth();
    return $adminProductController->form((int) ($_GET['id'] ?? 0));
});
$router->post('/admin/products/update', function () use ($adminProductController) {
    require_auth();
    return $adminProductController->update((int) ($_POST['id'] ?? 0));
});
$router->post('/admin/products/delete', function () use ($adminProductController) {
    require_auth();
    return $adminProductController->delete((int) ($_POST['id'] ?? 0));
});

$router->get('/admin/licenses', function () use ($adminLicenseController) {
    require_auth();
    return $adminLicenseController->index();
});
$router->post('/admin/licenses/import', function () use ($adminLicenseController) {
    require_auth();
    return $adminLicenseController->import();
});
$router->post('/admin/licenses/status', function () use ($adminLicenseController) {
    require_auth();
    return $adminLicenseController->bulkStatus();
});

$router->get('/admin/orders', function () use ($adminOrderController) {
    require_auth();
    return $adminOrderController->index();
});
$router->get('/admin/orders/view', function () use ($adminOrderController) {
    require_auth();
    return $adminOrderController->view((int) ($_GET['id'] ?? 0));
});
$router->post('/admin/orders/status', function () use ($adminOrderController) {
    require_auth();
    return $adminOrderController->status();
});
$router->post('/admin/orders/delivery', function () use ($adminOrderController) {
    require_auth();
    return $adminOrderController->delivery();
});
$router->post('/admin/orders/create', function () use ($adminOrderController) {
    require_auth();
    return $adminOrderController->create();
});

$router->get('/admin/customers', function () use ($customerController) {
    require_auth();
    return $customerController->index();
});
$router->post('/admin/customers/store', function () use ($customerController) {
    require_auth();
    return $customerController->store();
});

$router->get('/admin/coupons', function () use ($adminCouponController) {
    require_auth();
    return $adminCouponController->index();
});
$router->post('/admin/coupons/store', function () use ($adminCouponController) {
    require_auth();
    return $adminCouponController->store();
});
$router->post('/admin/coupons/update', function () use ($adminCouponController) {
    require_auth();
    return $adminCouponController->update();
});

$router->get('/admin/settings', function () use ($settingController) {
    require_auth(['admin']);
    return $settingController->index();
});
$router->post('/admin/settings/save', function () use ($settingController) {
    require_auth(['admin']);
    return $settingController->save();
});
$router->post('/admin/settings/test-email', function () use ($settingController) {
    require_auth(['admin']);
    return $settingController->testEmail();
});

$router->get('/admin/providers', function () use ($providerController) {
    require_auth();
    return $providerController->index();
});
$router->post('/admin/providers/store', function () use ($providerController) {
    require_auth();
    return $providerController->store();
});

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
