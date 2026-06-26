<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
    private Product  $productModel;
    private Category $categoryModel;

    public function __construct()
    {
        $this->productModel  = new Product();
        $this->categoryModel = new Category();
    }

    /** GET / */
    public function index(): void
    {
        $featured   = $this->productModel->featured(8);
        $categories = $this->categoryModel->allWithCount();
        $newArrivals = $this->productModel->search('', 0, 'Release_Date', 'DESC', 1, 4)['data'];

        $this->render('home.index', [
            'featured'    => $featured,
            'categories'  => $categories,
            'newArrivals' => $newArrivals,
            'flash'       => $this->getFlash(),
        ]);
    }
}
