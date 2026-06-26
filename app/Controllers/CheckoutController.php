<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Coupon;

class CheckoutController extends Controller
{
    private Cart    $cartModel;
    private Order   $orderModel;
    private Payment $paymentModel;
    private Coupon  $couponModel;

    public function __construct()
    {
        $this->cartModel    = new Cart();
        $this->orderModel   = new Order();
        $this->paymentModel = new Payment();
        $this->couponModel  = new Coupon();
    }

    /** GET /checkout/index */
    public function index(): void
    {
        $this->requireCustomer();

        $customerId = (int) $_SESSION['customer_id'];
        $cart       = $this->cartModel->getOrCreate($customerId);
        $items      = $this->cartModel->getItems((int) $cart['Cart_ID']);

        if (empty($items)) {
            $this->flash('error', 'Your cart is empty.');
            $this->redirect('cart/index');
        }

        $subtotal = $this->cartModel->getSubtotal((int) $cart['Cart_ID']);

        $this->render('checkout.index', [
            'items'    => $items,
            'subtotal' => $subtotal,
            'flash'    => $this->getFlash(),
        ]);
    }

    /** POST /checkout/place */
    public function place(): void
    {
        $this->requireCustomer();

        $customerId = (int) $_SESSION['customer_id'];
        $cart       = $this->cartModel->getOrCreate($customerId);
        $items      = $this->cartModel->getItems((int) $cart['Cart_ID']);

        if (empty($items)) {
            $this->flash('error', 'Cart is empty.');
            $this->redirect('cart/index');
        }

        $address       = trim($this->post('address', ''));
        $paymentMethod = $this->post('payment_method', 'CashOnDelivery');
        $couponCode    = trim($this->post('coupon_code', '')) ?: null;
        $shippingFees  = 50.00; // flat rate

        if (!$address) {
            $this->flash('error', 'Delivery address is required.');
            $this->redirect('checkout/index');
        }

        $subtotal = $this->cartModel->getSubtotal((int) $cart['Cart_ID']);
        $discount = 0.0;

        // Validate coupon
        if ($couponCode) {
            $coupon = $this->couponModel->validate($couponCode, $subtotal);
            if ($coupon) {
                $discount = (float) $coupon['Discount_Value'];
                $this->couponModel->redeem($couponCode, $customerId);
            } else {
                $this->flash('error', 'Invalid or expired coupon.');
                $this->redirect('checkout/index');
            }
        }

        $total = max(0, $subtotal - $discount + $shippingFees);

        // Build order items array
        $orderItems = array_map(fn($item) => [
            'product_id' => $item['Product_ID'],
            'price'      => $item['Price'],
            'qty'        => $item['Quantity'],
        ], $items);

        try {
            $orderId = $this->orderModel->placeOrder(
                $customerId,
                $address,
                $shippingFees,
                $total,
                $couponCode,
                $orderItems
            );

            $this->paymentModel->createForOrder($orderId, $paymentMethod, $total);
            $this->cartModel->clear((int) $cart['Cart_ID']);

            $this->flash('success', 'Order placed successfully!');
            $this->redirect('order/detail/' . $orderId);
        } catch (\Throwable $e) {
            $this->flash('error', 'Could not place order. Please try again.');
            $this->redirect('checkout/index');
        }
    }

    /** POST /checkout/apply-coupon (AJAX-friendly form post) */
    public function applyCoupon(): void
    {
        $this->requireCustomer();

        $code       = trim($this->post('coupon_code', ''));
        $customerId = (int) $_SESSION['customer_id'];
        $cart       = $this->cartModel->getOrCreate($customerId);
        $subtotal   = $this->cartModel->getSubtotal((int) $cart['Cart_ID']);

        $coupon = $this->couponModel->validate($code, $subtotal);

        if ($coupon) {
            $_SESSION['coupon'] = $coupon;
            $this->flash('success', 'Coupon applied: -' . $coupon['Discount_Value'] . ' EGP');
        } else {
            $this->flash('error', 'Coupon is invalid or not applicable.');
        }

        $this->redirect('checkout/index');
    }
}
