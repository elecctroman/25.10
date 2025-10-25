<?php

declare(strict_types=1);

use App\Core\Auth;
use App\Core\Autoloader;
use App\Core\Config;
use App\Core\Database;
use App\Core\RateLimiter;
use App\Core\Router;
use App\Core\Security;
use App\Core\Session;
use App\Core\View;
use App\Controllers\Admin\AuthController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\CategoryController;
use App\Controllers\Admin\ProductController;
use App\Controllers\Admin\LicenseController;
use App\Controllers\Admin\OrderController;
use App\Controllers\Admin\CustomerController;
use App\Controllers\Admin\CouponController;
use App\Controllers\Admin\SettingController;
use App\Controllers\Admin\ProviderController;
use App\Core\Mailer;
use App\Services\ProductService;
use App\Services\OrderService;
use App\Services\LicenseService;
use App\Services\CouponService;
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

$GLOBALS['container'] = [
    'config' => $config,
    'session' => $session,
    'security' => $security,
    'view' => $view,
    'db' => $database,
    'auth' => $auth,
    'mailer' => $mailer,
    'rateLimiter' => $rateLimiter,
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

$authController = new AuthController($view, $session, $security, $auth, $rateLimiter);
$dashboardController = new DashboardController($view, $session, $security);
$categoryController = new CategoryController($view, $session, $security);
$productController = new ProductController($view, $session, $security, new ProductService());
$licenseController = new LicenseController($view, $session, $security, new ProductService(), new LicenseService());
$orderController = new OrderController($view, $session, $security, new OrderService());
$customerController = new CustomerController($view, $session, $security);
$couponController = new CouponController($view, $session, $security, new CouponService());
$settingController = new SettingController($view, $session, $security, new SettingService(), $mailer);
$providerController = new ProviderController($view, $session, $security);

$router->get('/', function () use ($auth, $dashboardController) {
    if ($auth->check()) {
        return $dashboardController->index();
    }
    header('Location: /login');
    exit;
});
$router->get('/login', fn() => $authController->showLogin());
$router->post('/login', fn() => $authController->login());
$router->post('/logout', fn() => $authController->logout());

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

$router->get('/admin/products', function () use ($productController) {
    require_auth();
    return $productController->index();
});
$router->get('/admin/products/create', function () use ($productController) {
    require_auth();
    return $productController->form();
});
$router->post('/admin/products/store', function () use ($productController) {
    require_auth();
    return $productController->store();
});
$router->get('/admin/products/edit', function () use ($productController) {
    require_auth();
    return $productController->form((int) ($_GET['id'] ?? 0));
});
$router->post('/admin/products/update', function () use ($productController) {
    require_auth();
    return $productController->update((int) ($_POST['id'] ?? 0));
});
$router->post('/admin/products/delete', function () use ($productController) {
    require_auth();
    return $productController->delete((int) ($_POST['id'] ?? 0));
});

$router->get('/admin/licenses', function () use ($licenseController) {
    require_auth();
    return $licenseController->index();
});
$router->post('/admin/licenses/import', function () use ($licenseController) {
    require_auth();
    return $licenseController->import();
});
$router->post('/admin/licenses/status', function () use ($licenseController) {
    require_auth();
    return $licenseController->bulkStatus();
});

$router->get('/admin/orders', function () use ($orderController) {
    require_auth();
    return $orderController->index();
});
$router->get('/admin/orders/view', function () use ($orderController) {
    require_auth();
    return $orderController->view((int) ($_GET['id'] ?? 0));
});
$router->post('/admin/orders/status', function () use ($orderController) {
    require_auth();
    return $orderController->status();
});
$router->post('/admin/orders/delivery', function () use ($orderController) {
    require_auth();
    return $orderController->delivery();
});
$router->post('/admin/orders/create', function () use ($orderController) {
    require_auth();
    return $orderController->create();
});

$router->get('/admin/customers', function () use ($customerController) {
    require_auth();
    return $customerController->index();
});
$router->post('/admin/customers/store', function () use ($customerController) {
    require_auth();
    return $customerController->store();
});

$router->get('/admin/coupons', function () use ($couponController) {
    require_auth();
    return $couponController->index();
});
$router->post('/admin/coupons/store', function () use ($couponController) {
    require_auth();
    return $couponController->store();
});
$router->post('/admin/coupons/update', function () use ($couponController) {
    require_auth();
    return $couponController->update();
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
