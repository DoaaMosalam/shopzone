<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller
{
    private Cart    $cartModel;
    private Product $productModel;

    public function __construct()
    {
        $this->cartModel    = new Cart();
        $this->productModel = new Product();
    }

    /** GET /cart/index */
    public function index(): void
    {
        $this->requireCustomer();

        $cart  = $this->cartModel->getOrCreate((int) $_SESSION['customer_id']);
        $items = $this->cartModel->getItems((int) $cart['Cart_ID']);
        $subtotal = $this->cartModel->getSubtotal((int) $cart['Cart_ID']);

        $this->render('cart.index', [
            'cart'     => $cart,
            'items'    => $items,
            'subtotal' => $subtotal,
            'flash'    => $this->getFlash(),
        ]);
    }

    /** POST /cart/add */
    public function add(): void
    {
        $this->requireCustomer();

        $productId = (int) $this->post('product_id', 0);
        $qty       = max(1, (int) $this->post('qty', 1));

        $product = $this->productModel->find($productId);

        if (!$product || $product['Product_Quantity'] < $qty) {
            $this->flash('error', 'Product unavailable or insufficient stock.');
            $this->redirectBack();
        }

        $cart = $this->cartModel->getOrCreate((int) $_SESSION['customer_id']);
        $this->cartModel->addItem((int) $cart['Cart_ID'], $productId, $qty);

        $this->flash('success', 'Item added to cart.');
        $this->redirectBack();
    }

    /** POST /cart/update */
    public function update(): void
    {
        $this->requireCustomer();

        $productId = (int) $this->post('product_id', 0);
        $qty       = (int) $this->post('qty', 1);

        $cart = $this->cartModel->getOrCreate((int) $_SESSION['customer_id']);
        $this->cartModel->updateItem((int) $cart['Cart_ID'], $productId, $qty);

        $this->flash('success', 'Cart updated.');
        $this->redirect('cart/index');
    }

    /** POST /cart/remove */
    public function remove(): void
    {
        $this->requireCustomer();

        $productId = (int) $this->post('product_id', 0);
        $cart      = $this->cartModel->getOrCreate((int) $_SESSION['customer_id']);
        $this->cartModel->removeItem((int) $cart['Cart_ID'], $productId);

        $this->flash('success', 'Item removed.');
        $this->redirect('cart/index');
    }
}
