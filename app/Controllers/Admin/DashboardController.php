<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\Admin;

class DashboardController extends Controller
{
    private Admin $adminModel;

    public function __construct()
    {
        $this->adminModel = new Admin();
    }

    /** GET /admin/dashboard */
    public function index(): void
    {
        $this->requireAdmin();

        $stats        = $this->adminModel->getDashboardStats();
        $recentOrders = $this->adminModel->getRecentOrders(10);

        $this->render('admin.dashboard', [
            'stats'        => $stats,
            'recentOrders' => $recentOrders,
            'flash'        => $this->getFlash(),
        ], 'admin');
    }
}
