<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    private Category $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
    }

    /** GET /admin/category/list */
    public function list(): void
    {
        $this->requireAdmin();
        $categories = $this->categoryModel->allWithCount();
        $this->render('admin.categories.list', [
            'categories' => $categories,
            'flash'      => $this->getFlash(),
        ], 'admin');
    }

    /** GET /admin/category/create */
    public function create(): void
    {
        $this->requireAdmin();
        $this->render('admin.categories.form', [
            'category' => null,
            'flash'    => $this->getFlash(),
        ], 'admin');
    }

    /** POST /admin/category/store */
    public function store(): void
    {
        $this->requireAdmin();

        $name = trim($this->post('name', ''));
        if (!$name) {
            $this->flash('error', 'Category name is required.');
            $this->redirect('admin/category/create');
        }

        $data = ['Name' => $name];

        if (!empty($_FILES['image']['name'])) {
            $path = $this->uploadImage($_FILES['image']);
            if ($path) $data['Image'] = $path;
        }

        $this->categoryModel->insert($data);
        $this->flash('success', 'Category created.');
        $this->redirect('admin/category/list');
    }

    /** GET /admin/category/edit/<id> */
    public function edit(int $id = 0): void
    {
        $this->requireAdmin();
        $category = $this->categoryModel->find($id);
        $this->render('admin.categories.form', [
            'category' => $category,
            'flash'    => $this->getFlash(),
        ], 'admin');
    }

    /** POST /admin/category/update/<id> */
    public function update(int $id = 0): void
    {
        $this->requireAdmin();

        $data = ['Name' => trim($this->post('name', ''))];

        if (!empty($_FILES['image']['name'])) {
            $path = $this->uploadImage($_FILES['image']);
            if ($path) $data['Image'] = $path;
        }

        $this->categoryModel->update($data, 'Category_ID = ?', [$id]);
        $this->flash('success', 'Category updated.');
        $this->redirect('admin/category/list');
    }

    /** POST /admin/category/delete/<id> */
    public function delete(int $id = 0): void
    {
        $this->requireAdmin();
        $this->categoryModel->deleteById($id);
        $this->flash('success', 'Category deleted.');
        $this->redirect('admin/category/list');
    }

    private function uploadImage(array $file): string|false
    {
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array(mime_content_type($file['tmp_name']), $allowed, true)) return false;

        $ext  = pathinfo($file['name'], PATHINFO_EXTENSION);
        $name = 'cat_' . uniqid() . '.' . $ext;
        $dest = STORAGE_PATH . '/categories/' . $name;

        if (!is_dir(STORAGE_PATH . '/categories')) {
            mkdir(STORAGE_PATH . '/categories', 0755, true);
        }

        return move_uploaded_file($file['tmp_name'], $dest) ? 'categories/' . $name : false;
    }
}
