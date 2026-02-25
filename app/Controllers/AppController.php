<?php

namespace App\Controllers;

use App\Models\AppModel;

class AppController extends BaseController
{
    protected AppModel $appModel;

    public function __construct()
    {
        $this->appModel = new AppModel();
    }

    /**
     * Daftar semua aplikasi
     */
    public function index()
    {
        $data = [
            'title'      => 'Master Aplikasi',
            'page_title' => 'Daftar Aplikasi',
            'apps'       => $this->appModel->orderBy('name', 'ASC')->findAll(),
        ];

        return $this->renderView('apps/index', $data);
    }

    /**
     * Form tambah aplikasi baru
     */
    public function create()
    {
        $data = [
            'title'      => 'Tambah Aplikasi',
            'page_title' => 'Tambah Aplikasi Baru',
        ];

        return $this->renderView('apps/create', $data);
    }

    /**
     * Simpan aplikasi baru
     */
    public function store()
    {
        $rules = [
            'name'       => 'required|max_length[255]',
            'url'        => 'permit_empty|max_length[500]',
            'tech_stack' => 'permit_empty|max_length[255]',
            'pic'        => 'permit_empty|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->appModel->save([
            'name'        => $this->request->getPost('name'),
            'url'         => $this->request->getPost('url'),
            'tech_stack'  => $this->request->getPost('tech_stack'),
            'pic'         => $this->request->getPost('pic'),
            'description' => $this->request->getPost('description'),
        ]);

        return redirect()->to('/admin/apps')->with('success', 'Aplikasi berhasil ditambahkan.');
    }

    /**
     * Form edit aplikasi
     */
    public function edit(int $id)
    {
        $app = $this->appModel->find($id);

        if (! $app) {
            return redirect()->to('/admin/apps')->with('error', 'Aplikasi tidak ditemukan.');
        }

        $data = [
            'title'      => 'Edit Aplikasi',
            'page_title' => 'Edit Aplikasi',
            'app'        => $app,
        ];

        return $this->renderView('apps/edit', $data);
    }

    /**
     * Update aplikasi
     */
    public function update(int $id)
    {
        $app = $this->appModel->find($id);

        if (! $app) {
            return redirect()->to('/admin/apps')->with('error', 'Aplikasi tidak ditemukan.');
        }

        $rules = [
            'name'       => 'required|max_length[255]',
            'url'        => 'permit_empty|max_length[500]',
            'tech_stack' => 'permit_empty|max_length[255]',
            'pic'        => 'permit_empty|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->appModel->update($id, [
            'name'        => $this->request->getPost('name'),
            'url'         => $this->request->getPost('url'),
            'tech_stack'  => $this->request->getPost('tech_stack'),
            'pic'         => $this->request->getPost('pic'),
            'description' => $this->request->getPost('description'),
        ]);

        return redirect()->to('/admin/apps')->with('success', 'Aplikasi berhasil diperbarui.');
    }

    /**
     * Hapus aplikasi
     */
    public function delete(int $id)
    {
        $app = $this->appModel->find($id);

        if (! $app) {
            return redirect()->to('/admin/apps')->with('error', 'Aplikasi tidak ditemukan.');
        }

        $this->appModel->delete($id);

        return redirect()->to('/admin/apps')->with('success', 'Aplikasi berhasil dihapus.');
    }
}
