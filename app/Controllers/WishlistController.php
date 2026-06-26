<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Wishlist;

class WishlistController extends Controller
{
    private Wishlist $wishlistModel;

    public function __construct()
    {
        $this->wishlistModel = new Wishlist();
    }

    /** GET /wishlist/index */
    public function index(): void
    {
        $this->requireCustomer();

        $wl    = $this->wishlistModel->getOrCreate((int) $_SESSION['customer_id']);
        $items = $this->wishlistModel->getItems((int) $wl['Wishlist_ID']);

        $this->render('wishlist.index', [
            'wishlist' => $wl,
            'items'    => $items,
            'flash'    => $this->getFlash(),
        ]);
    }

    /** POST /wishlist/toggle */
    public function toggle(): void
    {
        $this->requireCustomer();

        $productId = (int) $this->post('product_id', 0);
        $wl        = $this->wishlistModel->getOrCreate((int) $_SESSION['customer_id']);

        if ($this->wishlistModel->hasProduct((int) $wl['Wishlist_ID'], $productId)) {
            $this->wishlistModel->removeProduct((int) $wl['Wishlist_ID'], $productId);
            $this->flash('success', 'Removed from wishlist.');
        } else {
            $this->wishlistModel->addProduct((int) $wl['Wishlist_ID'], $productId);
            $this->flash('success', 'Added to wishlist.');
        }

        $this->redirectBack();
    }

    /** POST /wishlist/remove */
    public function remove(): void
    {
        $this->requireCustomer();

        $productId = (int) $this->post('product_id', 0);
        $wl        = $this->wishlistModel->getOrCreate((int) $_SESSION['customer_id']);
        $this->wishlistModel->removeProduct((int) $wl['Wishlist_ID'], $productId);

        $this->flash('success', 'Removed from wishlist.');
        $this->redirect('wishlist/index');
    }
}
