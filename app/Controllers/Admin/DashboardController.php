<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Security;
use App\Core\Session;
use App\Core\View;
use App\Models\AuditLog;
use App\Models\ErrorLog;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    public function __construct(View $view, Session $session, Security $security)
    {
        parent::__construct($view, $session, $security);
    }

    public function index(): void
    {
        $stats = Order::stats();
        $topProducts = Product::topSelling();
        $recentOrders = Order::recent();
        $recentErrors = ErrorLog::paginate(10)['data'];
        $recentAudits = AuditLog::paginate(10)['data'];

        view('admin/dashboard', [
            'title' => 'Yönetim Paneli',
            'stats' => $stats,
            'topProducts' => $topProducts,
            'recentOrders' => $recentOrders,
            'recentErrors' => $recentErrors,
            'recentAudits' => $recentAudits,
        ]);
    }
}
