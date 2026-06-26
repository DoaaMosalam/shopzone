<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\Customer;

class UserController extends Controller
{
    private Customer $customerModel;

    public function __construct()
    {
        $this->customerModel = new Customer();
    }

    /** GET /admin/user/list */
    public function list(): void
    {
        $this->requireAdmin();

        $page   = max(1, (int) $this->get('page', 1));
        $result = $this->customerModel->allWithUser($page, 20);

        $this->render('admin.users.list', [
            'paginator' => $result,
            'flash'     => $this->getFlash(),
        ], 'admin');
    }

    /** GET /admin/user/detail/<id> */
    public function detail(int $id = 0): void
    {
        $this->requireAdmin();

        $customer = $this->customerModel->findWithUser($id);
        if (!$customer) {
            $this->flash('error', 'Customer not found.');
            $this->redirect('admin/user/list');
        }

        $this->render('admin.users.detail', [
            'customer' => $customer,
            'flash'    => $this->getFlash(),
        ], 'admin');
    }

    /** POST /admin/user/set-status/<id> */
    public function setStatus(int $id = 0): void
    {
        $this->requireAdmin();

        $status  = $this->post('status', 'Active');
        $allowed = ['Active', 'Suspended', 'Banned'];

        if (!in_array($status, $allowed, true)) {
            $this->flash('error', 'Invalid status.');
            $this->redirect('admin/user/detail/' . $id);
            return;
        }

        $banUntil = null;

        if ($status === 'Banned') {
            // Admin can specify number of ban days OR an exact date
            $banDays = (int) $this->post('ban_days', 0);
            $banDate = trim($this->post('ban_until_date', ''));

            if ($banDays > 0) {
                // Convert days to datetime
                $banUntil = date('Y-m-d H:i:s', strtotime("+{$banDays} days"));
            } elseif ($banDate !== '') {
                // Exact date provided (YYYY-MM-DD from date input → set to end of that day)
                $timestamp = strtotime($banDate);
                if ($timestamp !== false && $timestamp > time()) {
                    $banUntil = date('Y-m-d 23:59:59', $timestamp);
                }
                // If date is in the past or invalid, leave banUntil as null (permanent ban)
            }
            // If neither provided → permanent ban (Ban_Until = NULL)
        }

        $this->customerModel->setStatus($id, $status, $banUntil);
        $this->flash('success', 'Customer status updated successfully.');
        $this->redirect('admin/user/detail/' . $id);
    }
}
