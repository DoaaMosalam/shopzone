<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Review;

class ProductController extends Controller
{
    private Product  $productModel;
    private Category $categoryModel;
    private Review   $reviewModel;

    public function __construct()
    {
        $this->productModel  = new Product();
        $this->categoryModel = new Category();
        $this->reviewModel   = new Review();
    }

    /** GET /product/list */
    public function list(): void
    {
        $keyword    = trim($this->get('q', ''));
        $categoryId = (int) $this->get('category', 0);
        $sortBy     = $this->get('sort', 'Product_ID');
        $sortDir    = $this->get('dir', 'DESC');
        $page       = max(1, (int) $this->get('page', 1));

        $result     = $this->productModel->search($keyword, $categoryId, $sortBy, $sortDir, $page, 12);
        $categories = $this->categoryModel->allWithCount();

        $this->render('product.list', [
            'paginator'    => $result,
            'categories'   => $categories,
            'keyword'      => $keyword,
            'categoryId'   => $categoryId,
            'sortBy'       => $sortBy,
            'sortDir'      => $sortDir,
            'flash'        => $this->getFlash(),
        ]);
    }

    /** GET /product/detail/<id> */
    public function detail(int $id = 0): void
    {
        $product = $this->productModel->findDetail($id);

        if (!$product) {
            http_response_code(404);
            $this->render('errors.404');
            return;
        }

        $reviews = $this->reviewModel->forProduct($id);
        $related = $this->productModel->search('', (int) ($product['Category_ID'] ?? 0), 'Rating_No', 'DESC', 1, 4)['data'];

        $userReviewed = false;
        if (!empty($_SESSION['customer_id'])) {
            $userReviewed = $this->reviewModel->exists((int) $_SESSION['customer_id'], $id);
        }

        $this->render('product.detail', [
            'product'      => $product,
            'reviews'      => $reviews,
            'related'      => $related,
            'userReviewed' => $userReviewed,
            'flash'        => $this->getFlash(),
        ]);
    }
}
