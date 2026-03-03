<?php

namespace App\Controllers;

use App\Models\MaintenanceLogModel;
use App\Models\AppModel;
use App\Models\CategoryModel;
use CodeIgniter\Shield\Models\UserModel;

class MaintenanceLogController extends BaseController
{
    protected MaintenanceLogModel $logModel;
    protected AppModel $appModel;
    protected CategoryModel $categoryModel;

    public function __construct()
    {
        $this->logModel      = new MaintenanceLogModel();
        $this->appModel      = new AppModel();
        $this->categoryModel = new CategoryModel();
    }

    /**
     * Daftar semua log maintenance
     */
    public function index()
    {
        $logs = $this->logModel->getLogsWithRelations();

        // Ambil nama teknisi untuk setiap log
        $userModel = new UserModel();
        foreach ($logs as &$log) {
            if (! empty($log['technician_id'])) {
                $tech = $userModel->findById($log['technician_id']);
                $log['technician_name'] = $tech ? $tech->username : '-';
            } else {
                $log['technician_name'] = '-';
            }
            if (! empty($log['created_by'])) {
                $creator = $userModel->findById($log['created_by']);
                $log['creator_name'] = $creator ? $creator->username : '-';
            } else {
                $log['creator_name'] = '-';
            }
        }

        $data = [
            'title'      => 'Log Maintenance',
            'page_title' => 'Daftar Log Maintenance',
            'logs'       => $logs,
        ];

        return $this->renderView('maintenance_logs/index', $data);
    }

    /**
     * Form tambah log baru
     */
    public function create()
    {
        $userModel = new UserModel();

        $data = [
            'title'      => 'Tambah Log Maintenance',
            'page_title' => 'Tambah Log Maintenance',
            'apps'       => $this->appModel->orderBy('name', 'ASC')->findAll(),
            'categories' => $this->categoryModel->orderBy('name', 'ASC')->findAll(),
            'users'      => $userModel->findAll(),
        ];

        return $this->renderView('maintenance_logs/create', $data);
    }

    /**
     * Simpan log baru
     */
    public function store()
    {
        $rules = [
            'app_id'              => 'required|integer',
            'category_id'         => 'required|integer',
            'maintenance_date'    => 'required',
            'title'               => 'required|max_length[255]',
            'problem_description' => 'permit_empty',
            'technician_id'       => 'permit_empty|integer',
            'has_downtime'        => 'permit_empty|in_list[0,1]',
            'downtime_duration'   => 'permit_empty|integer',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle file attachment
        $attachmentPath = null;
        $file = $this->request->getFile('attachment');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/maintenance', $newName);
            $attachmentPath = 'maintenance/' . $newName;
        }

        $this->logModel->save([
            'app_id'              => $this->request->getPost('app_id'),
            'category_id'         => $this->request->getPost('category_id'),
            'maintenance_date'    => $this->request->getPost('maintenance_date'),
            'title'               => $this->request->getPost('title'),
            'problem_description' => $this->request->getPost('problem_description'),
            'root_cause'          => $this->request->getPost('root_cause'),
            'action_taken'        => $this->request->getPost('action_taken'),
            'status'              => $this->request->getPost('status') ?: 'Pending',
            'technician_id'       => $this->request->getPost('technician_id') ?: null,
            'has_downtime'        => $this->request->getPost('has_downtime') ?: 0,
            'downtime_duration'   => $this->request->getPost('has_downtime') ? $this->request->getPost('downtime_duration') : null,
            'attachment'          => $attachmentPath,
            'created_by'          => auth()->id(),
        ]);

        return redirect()->to('/maintenance-logs')->with('success', 'Log maintenance berhasil ditambahkan.');
    }

    /**
     * Detail log maintenance
     */
    public function show(int $id)
    {
        $log = $this->logModel->getLogWithRelations($id);

        if (! $log) {
            return redirect()->to('/maintenance-logs')->with('error', 'Log tidak ditemukan.');
        }

        // Ambil nama teknisi dan creator
        $userModel = new UserModel();
        if (! empty($log['technician_id'])) {
            $tech = $userModel->findById($log['technician_id']);
            $log['technician_name'] = $tech ? $tech->username : '-';
        } else {
            $log['technician_name'] = '-';
        }
        if (! empty($log['created_by'])) {
            $creator = $userModel->findById($log['created_by']);
            $log['creator_name'] = $creator ? $creator->username : '-';
        } else {
            $log['creator_name'] = '-';
        }

        $data = [
            'title'      => 'Detail Log Maintenance',
            'page_title' => 'Detail Log Maintenance',
            'log'        => $log,
        ];

        return $this->renderView('maintenance_logs/show', $data);
    }

    /**
     * Form edit log
     */
    public function edit(int $id)
    {
        $log = $this->logModel->find($id);

        if (! $log) {
            return redirect()->to('/maintenance-logs')->with('error', 'Log tidak ditemukan.');
        }

        $userModel = new UserModel();

        $data = [
            'title'      => 'Edit Log Maintenance',
            'page_title' => 'Edit Log Maintenance',
            'log'        => $log,
            'apps'       => $this->appModel->orderBy('name', 'ASC')->findAll(),
            'categories' => $this->categoryModel->orderBy('name', 'ASC')->findAll(),
            'users'      => $userModel->findAll(),
        ];

        return $this->renderView('maintenance_logs/edit', $data);
    }

    /**
     * Update log
     */
    public function update(int $id)
    {
        $log = $this->logModel->find($id);

        if (! $log) {
            return redirect()->to('/maintenance-logs')->with('error', 'Log tidak ditemukan.');
        }

        $rules = [
            'app_id'              => 'required|integer',
            'category_id'         => 'required|integer',
            'maintenance_date'    => 'required',
            'title'               => 'required|max_length[255]',
            'problem_description' => 'permit_empty',
            'technician_id'       => 'permit_empty|integer',
            'has_downtime'        => 'permit_empty|in_list[0,1]',
            'downtime_duration'   => 'permit_empty|integer',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'app_id'              => $this->request->getPost('app_id'),
            'category_id'         => $this->request->getPost('category_id'),
            'maintenance_date'    => $this->request->getPost('maintenance_date'),
            'title'               => $this->request->getPost('title'),
            'problem_description' => $this->request->getPost('problem_description'),
            'root_cause'          => $this->request->getPost('root_cause'),
            'action_taken'        => $this->request->getPost('action_taken'),
            'status'              => $this->request->getPost('status') ?: 'Pending',
            'technician_id'       => $this->request->getPost('technician_id') ?: null,
            'has_downtime'        => $this->request->getPost('has_downtime') ?: 0,
            'downtime_duration'   => $this->request->getPost('has_downtime') ? $this->request->getPost('downtime_duration') : null,
        ];

        // Handle file attachment
        $file = $this->request->getFile('attachment');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            // Hapus attachment lama jika ada
            if (! empty($log['attachment']) && file_exists(WRITEPATH . 'uploads/' . $log['attachment'])) {
                unlink(WRITEPATH . 'uploads/' . $log['attachment']);
            }
            $newName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/maintenance', $newName);
            $updateData['attachment'] = 'maintenance/' . $newName;
        }

        $this->logModel->update($id, $updateData);

        return redirect()->to('/maintenance-logs')->with('success', 'Log maintenance berhasil diperbarui.');
    }

    /**
     * Serve attachment file
     */
    public function attachment(int $id)
    {
        $log = $this->logModel->find($id);

        if (! $log || empty($log['attachment'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Attachment tidak ditemukan.');
        }

        $filePath = WRITEPATH . 'uploads/' . $log['attachment'];

        if (! file_exists($filePath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File tidak ditemukan.');
        }

        $mime = mime_content_type($filePath);

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'inline; filename="' . basename($log['attachment']) . '"')
            ->setBody(file_get_contents($filePath));
    }

    /**
     * Update status log (untuk review)
     */
    public function updateStatus(int $id)
    {
        $log = $this->logModel->find($id);

        if (! $log) {
            return redirect()->to('/maintenance-logs')->with('error', 'Log tidak ditemukan.');
        }

        $status = $this->request->getPost('status');

        if (! in_array($status, ['Pending', 'On Progress', 'Testing', 'Completed'])) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

        $this->logModel->update($id, ['status' => $status]);

        return redirect()->to('/maintenance-logs/show/' . $id)->with('success', 'Status berhasil diperbarui menjadi ' . $status . '.');
    }

    /**
     * Hapus log
     */
    public function delete(int $id)
    {
        $log = $this->logModel->find($id);

        if (! $log) {
            return redirect()->to('/maintenance-logs')->with('error', 'Log tidak ditemukan.');
        }

        // Hapus attachment jika ada
        if (! empty($log['attachment']) && file_exists(WRITEPATH . 'uploads/' . $log['attachment'])) {
            unlink(WRITEPATH . 'uploads/' . $log['attachment']);
        }

        $this->logModel->delete($id);

        return redirect()->to('/maintenance-logs')->with('success', 'Log maintenance berhasil dihapus.');
    }
}
