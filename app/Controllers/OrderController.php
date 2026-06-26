<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    private Order $orderModel;

    public function __construct()
    {
        $this->orderModel = new Order();
    }

    /** GET /order/list */
    public function list(): void
    {
        $this->requireCustomer();

        $page   = max(1, (int) $this->get('page', 1));
        $result = $this->orderModel->forCustomer((int) $_SESSION['customer_id'], $page);

        $this->render('order.list', [
            'paginator' => $result,
            'flash'     => $this->getFlash(),
        ]);
    }

    /** GET /order/detail/<id> */
    public function detail(int $orderId = 0): void
    {
        $this->requireCustomer();

        $order = $this->orderModel->findWithDetails($orderId);

        if (!$order || (int) $order['Customer_ID'] !== (int) $_SESSION['customer_id']) {
            http_response_code(403);
            $this->render('errors.404');
            return;
        }

        $this->render('order.detail', [
            'order' => $order,
            'flash' => $this->getFlash(),
        ]);
    }

    /** POST /order/cancel/<id> */
    public function cancel(int $orderId = 0): void
    {
        $this->requireCustomer();

        $order = $this->orderModel->find($orderId);

        if (!$order || (int) $order['Customer_ID'] !== (int) $_SESSION['customer_id']) {
            $this->flash('error', 'Order not found.');
            $this->redirect('order/list');
        }

        if ($order['Order_Status'] !== 'Pending') {
            $this->flash('error', 'Only pending orders can be cancelled.');
            $this->redirect('order/detail/' . $orderId);
        }

        $this->orderModel->updateStatus($orderId, 'Cancelled');
        $this->flash('success', 'Order cancelled.');
        $this->redirect('order/detail/' . $orderId);
    }
}
