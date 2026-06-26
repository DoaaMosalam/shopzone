<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\Coupon;

class CouponController extends Controller
{
    private Coupon $couponModel;

    public function __construct()
    {
        $this->couponModel = new Coupon();
    }

    /** GET /admin/coupon/list */
    public function list(): void
    {
        $this->requireAdmin();

        $page   = max(1, (int) $this->get('page', 1));
        $result = $this->couponModel->allWithAdmin($page, 20);

        $this->render('admin.coupons.list', [
            'paginator' => $result,
            'flash'     => $this->getFlash(),
        ], 'admin');
    }

    /** GET /admin/coupon/create */
    public function create(): void
    {
        $this->requireAdmin();
        $this->render('admin.coupons.form', [
            'coupon' => null,
            'flash'  => $this->getFlash(),
        ], 'admin');
    }

    /** POST /admin/coupon/store */
    public function store(): void
    {
        $this->requireAdmin();

        $code = strtoupper(trim($this->post('coupon_code', '')));
        if (!$code) {
            $this->flash('error', 'Coupon code is required.');
            $this->redirect('admin/coupon/create');
        }

        $this->couponModel->insert([
            'Coupon_Code'     => $code,
            'Discount_Value'  => (float) $this->post('discount', 0),
            'Start_Date'      => $this->post('start_date', date('Y-m-d')),
            'End_Date'        => $this->post('end_date', date('Y-m-d', strtotime('+30 days'))),
            'Status'          => 'Active',
            'Min_Order_Value' => (float) $this->post('min_order', 0),
            'Usage_Limit'     => (int) $this->post('usage_limit', 100),
            'Used_Count'      => 0,
            'Admin_ID'        => (int) $_SESSION['user_id'],
        ]);

        $this->flash('success', 'Coupon created.');
        $this->redirect('admin/coupon/list');
    }

    /** POST /admin/coupon/toggle/<code> */
    public function toggle(string $code = ''): void
    {
        $this->requireAdmin();

        $coupon = $this->couponModel->find($code);
        if (!$coupon) {
            $this->flash('error', 'Coupon not found.');
            $this->redirect('admin/coupon/list');
        }

        $newStatus = $coupon['Status'] === 'Active' ? 'Inactive' : 'Active';
        $this->couponModel->update(['Status' => $newStatus], 'Coupon_Code = ?', [$code]);

        $this->flash('success', 'Coupon status toggled.');
        $this->redirect('admin/coupon/list');
    }

    /** POST /admin/coupon/delete/<code> */
    public function delete(string $code = ''): void
    {
        $this->requireAdmin();
        $this->couponModel->delete('Coupon_Code = ?', [$code]);
        $this->flash('success', 'Coupon deleted.');
        $this->redirect('admin/coupon/list');
    }
}
