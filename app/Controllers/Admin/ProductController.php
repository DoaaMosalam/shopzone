<?php

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Database;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    private Product  $productModel;
    private Category $categoryModel;
    private Database $db;

    public function __construct()
    {
        $this->productModel  = new Product();
        $this->categoryModel = new Category();
        $this->db            = Database::getInstance();
    }

    /** GET /admin/product/list */
    public function list(): void
    {
        $this->requireAdmin();

        $page   = max(1, (int) $this->get('page', 1));
        $result = $this->productModel->search(
            $this->get('q', ''),
            (int) $this->get('category', 0),
            'Product_ID', 'DESC', $page, 20
        );

        $categories = $this->categoryModel->allWithCount();

        $this->render('admin.products.list', [
            'paginator'  => $result,
            'categories' => $categories,
            'flash'      => $this->getFlash(),
        ], 'admin');
    }

    /** GET /admin/product/create */
    public function create(): void
    {
        $this->requireAdmin();
        $categories = $this->categoryModel->all('Name ASC');
        $this->render('admin.products.form', [
            'product'    => null,
            'categories' => $categories,
            'flash'      => $this->getFlash(),
        ], 'admin');
    }

    /** POST /admin/product/store */
    public function store(): void
    {
        $this->requireAdmin();

        $data = $this->collectProductData();

        if (!empty($_FILES['image']['name'])) {
            $imgPath = $this->uploadProductImage($_FILES['image']);
            if ($imgPath) $data['Image_URL'] = $imgPath;
        }

        $productId = $this->productModel->insert($data);

        $this->saveSpecs($productId);
        $this->logManages((int) $_SESSION['user_id'], $productId);

        $this->flash('success', 'Product created successfully.');
        $this->redirect('admin/product/list');
    }

    /** GET /admin/product/edit/<id> */
    public function edit(int $id = 0): void
    {
        $this->requireAdmin();

        $product    = $this->productModel->findDetail($id);
        $categories = $this->categoryModel->all('Name ASC');

        if (!$product) {
            $this->flash('error', 'Product not found.');
            $this->redirect('admin/product/list');
        }

        $this->render('admin.products.form', [
            'product'    => $product,
            'categories' => $categories,
            'flash'      => $this->getFlash(),
        ], 'admin');
    }

    /** POST /admin/product/update/<id> */
    public function update(int $id = 0): void
    {
        $this->requireAdmin();

        $data = $this->collectProductData();

        if (!empty($_FILES['image']['name'])) {
            $imgPath = $this->uploadProductImage($_FILES['image']);
            if ($imgPath) $data['Image_URL'] = $imgPath;
        }

        $this->productModel->update($data, 'Product_ID = ?', [$id]);
        $this->saveSpecs($id, true);

        $this->flash('success', 'Product updated.');
        $this->redirect('admin/product/list');
    }

    /** POST /admin/product/delete/<id> */
    public function delete(int $id = 0): void
    {
        $this->requireAdmin();
        $this->productModel->deleteById($id);
        $this->flash('success', 'Product deleted.');
        $this->redirect('admin/product/list');
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    private function collectProductData(): array
    {
        return [
            'Name'             => trim($this->post('name', '')),
            'Price'            => (float) $this->post('price', 0),
            'Description'      => trim($this->post('description', '')),
            'Product_Quantity' => (int) $this->post('quantity', 0),
            'Brand'            => trim($this->post('brand', '')),
            'Release_Date'     => $this->post('release_date', null) ?: null,
            'Category_ID'      => ($cat = (int) $this->post('category_id', 0)) ? $cat : null,
        ];
    }

    private function saveSpecs(int $productId, bool $replace = false): void
    {
        if ($replace) {
            $this->db->query(
                "DELETE FROM Specification WHERE Product_ID = ?",
                [$productId]
            );
        }

        $keys   = $_POST['spec_key']   ?? [];
        $values = $_POST['spec_value'] ?? [];

        foreach ($keys as $i => $key) {
            $key = trim($key);
            $val = trim($values[$i] ?? '');
            if ($key && $val) {
                $this->db->query(
                    "INSERT INTO Specification (Product_ID, Spec_Key, Spec_Value) VALUES (?, ?, ?)",
                    [$productId, $key, $val]
                );
            }
        }
    }

    private function uploadProductImage(array $file): string|false
    {
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array(mime_content_type($file['tmp_name']), $allowed, true)) return false;
        if ($file['size'] > 5 * 1024 * 1024) return false;

        $ext  = pathinfo($file['name'], PATHINFO_EXTENSION);
        $name = 'product_' . uniqid() . '.' . $ext;
        $dir  = STORAGE_PATH . '/products/';

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return move_uploaded_file($file['tmp_name'], $dir . $name) ? 'products/' . $name : false;
    }

    private function logManages(int $adminId, int $productId): void
    {
        $this->db->query(
            "INSERT IGNORE INTO Manages (Admin_ID, Product_ID) VALUES (?, ?)",
            [$adminId, $productId]
        );
    }
}
