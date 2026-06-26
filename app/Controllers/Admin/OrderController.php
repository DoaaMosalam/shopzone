<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\Order;
use App\Models\Payment;

class OrderController extends Controller
{
    private Order   $orderModel;
    private Payment $paymentModel;

    public function __construct()
    {
        $this->orderModel   = new Order();
        $this->paymentModel = new Payment();
    }

    /** GET /admin/order/list */
    public function list(): void
    {
        $this->requireAdmin();

        $page   = max(1, (int) $this->get('page', 1));
        $status = $this->get('status', '');
        $result = $this->orderModel->allWithCustomer($page, 20, $status);

        $this->render('admin.orders.list', [
            'paginator'     => $result,
            'statusFilter'  => $status,
            'flash'         => $this->getFlash(),
        ], 'admin');
    }

    /** GET /admin/order/detail/<id> */
    public function detail(int $id = 0): void
    {
        $this->requireAdmin();

        $order = $this->orderModel->findWithDetails($id);
        if (!$order) {
            $this->flash('error', 'Order not found.');
            $this->redirect('admin/order/list');
        }

        $this->render('admin.orders.detail', [
            'order' => $order,
            'flash' => $this->getFlash(),
        ], 'admin');
    }

    /** POST /admin/order/update-status/<id> */
    public function updateStatus(int $id = 0): void
    {
        $this->requireAdmin();

        $status   = $this->post('status', '');
        $allowed  = ['Pending','Processing','Shipped','Delivered','Cancelled','Refunded'];

        if (!in_array($status, $allowed, true)) {
            $this->flash('error', 'Invalid status.');
            $this->redirect('admin/order/detail/' . $id);
        }

        $this->orderModel->updateStatus($id, $status);

        if ($status === 'Delivered') {
            $payment = $this->paymentModel->findByOrder($id);
            if ($payment && !$payment['Is_Paid']) {
                $this->paymentModel->markPaid((int) $payment['Payment_ID']);
            }
        }

        $this->flash('success', 'Order status updated to ' . $status . '.');
        $this->redirect('admin/order/detail/' . $id);
    }
}
