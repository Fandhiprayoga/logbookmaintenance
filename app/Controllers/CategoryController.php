<?php

namespace App\Controllers;

use App\Models\CategoryModel;

class CategoryController extends BaseController
{
    protected CategoryModel $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }

    /**
     * Daftar semua kategori
     */
    public function index()
    {
        $data = [
            'title'      => 'Master Kategori',
            'page_title' => 'Daftar Kategori',
            'categories' => $this->categoryModel->orderBy('name', 'ASC')->findAll(),
        ];

        return $this->renderView('categories/index', $data);
    }

    /**
     * Form tambah kategori baru
     */
    public function create()
    {
        $data = [
            'title'      => 'Tambah Kategori',
            'page_title' => 'Tambah Kategori Baru',
        ];

        return $this->renderView('categories/create', $data);
    }

    /**
     * Simpan kategori baru
     */
    public function store()
    {
        $rules = [
            'name' => 'required|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->categoryModel->save([
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ]);

        return redirect()->to('/admin/categories')->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Form edit kategori
     */
    public function edit(int $id)
    {
        $category = $this->categoryModel->find($id);

        if (! $category) {
            return redirect()->to('/admin/categories')->with('error', 'Kategori tidak ditemukan.');
        }

        $data = [
            'title'      => 'Edit Kategori',
            'page_title' => 'Edit Kategori',
            'category'   => $category,
        ];

        return $this->renderView('categories/edit', $data);
    }

    /**
     * Update kategori
     */
    public function update(int $id)
    {
        $category = $this->categoryModel->find($id);

        if (! $category) {
            return redirect()->to('/admin/categories')->with('error', 'Kategori tidak ditemukan.');
        }

        $rules = [
            'name' => 'required|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->categoryModel->update($id, [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ]);

        return redirect()->to('/admin/categories')->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Hapus kategori
     */
    public function delete(int $id)
    {
        $category = $this->categoryModel->find($id);

        if (! $category) {
            return redirect()->to('/admin/categories')->with('error', 'Kategori tidak ditemukan.');
        }

        $this->categoryModel->delete($id);

        return redirect()->to('/admin/categories')->with('success', 'Kategori berhasil dihapus.');
    }
}
