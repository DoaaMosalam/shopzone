<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Review;
use App\Models\Product;

class ReviewController extends Controller
{
    private Review  $reviewModel;
    private Product $productModel;

    public function __construct()
    {
        $this->reviewModel  = new Review();
        $this->productModel = new Product();
    }

    /** POST /review/submit */
    public function submit(): void
    {
        $this->requireCustomer();

        $productId  = (int) $this->post('product_id', 0);
        $comment    = trim($this->post('comment', ''));
        $customerId = (int) $_SESSION['customer_id'];

        if (!$productId || !$comment) {
            $this->flash('error', 'Review cannot be empty.');
            $this->redirectBack();
        }

        $product = $this->productModel->find($productId);
        if (!$product) {
            $this->flash('error', 'Product not found.');
            $this->redirectBack();
        }

        $this->reviewModel->submitReview($customerId, $productId, $comment);
        $this->productModel->refreshRating($productId);

        $this->flash('success', 'Review submitted.');
        $this->redirect('product/detail/' . $productId);
    }
}
