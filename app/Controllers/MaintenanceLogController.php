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
        $data = [
            'title'      => 'Ticketing Maintenance',
            'page_title' => 'Daftar Ticket',
        ];

        return $this->renderView('maintenance_logs/index', $data);
    }

    /**
     * Endpoint data AJAX untuk DataTables
     */
    public function data()
    {
        $logs = $this->logModel->getLogsWithRelations();

        $userModel = new UserModel();
        $rows = [];

        foreach ($logs as $log) {
            if (! empty($log['technician_id'])) {
                $tech = $userModel->findById($log['technician_id']);
                $technicianName = $tech ? $tech->username : '-';
            } else {
                $technicianName = '-';
            }

            $rows[] = [
                'id'                => (int) $log['id'],
                'maintenance_date_raw' => $log['maintenance_date'] ?? null,
                'maintenance_date'  => date('d/m/Y H:i', strtotime($log['maintenance_date'])),
                'app_name'          => $log['app_name'] ?? '-',
                'category_name'     => $log['category_name'] ?? '-',
                'title'             => $log['title'] ?? '-',
                'technician_name'   => $technicianName,
                'status'            => $log['status'] ?? '-',
                'has_downtime'      => (bool) ($log['has_downtime'] ?? false),
                'downtime_duration' => $log['downtime_duration'] ?? null,
                'show_url'          => base_url('maintenance-logs/show/' . $log['id']),
                'edit_url'          => base_url('maintenance-logs/edit/' . $log['id']),
                'close_url'         => base_url('maintenance-logs/close-ticket/' . $log['id']),
                'reopen_url'        => base_url('maintenance-logs/reopen-ticket/' . $log['id']),
                'delete_url'        => base_url('maintenance-logs/delete/' . $log['id']),
                'closed_at'         => $log['closed_at'] ?? null,
            ];
        }

        return $this->response->setJSON(['data' => $rows]);
    }

    /**
     * Form tambah log baru
     */
    public function create()
    {
        $userModel = new UserModel();

        $data = [
            'title'      => 'Tambah Ticket',
            'page_title' => 'Tambah Ticket',
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
            'status'              => 'permit_empty|in_list[Pending,On Progress,Testing]',
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

        return redirect()->to('/maintenance-logs')->with('success', 'Ticket berhasil ditambahkan.');
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

        if (! empty($log['closed_by'])) {
            $closer = $userModel->findById($log['closed_by']);
            $log['closer_name'] = $closer ? $closer->username : '-';
        } else {
            $log['closer_name'] = null;
        }

        $data = [
            'title'      => 'Detail Ticket',
            'page_title' => 'Detail Ticket',
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
            'title'      => 'Edit Ticket',
            'page_title' => 'Edit Ticket',
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
            'status'              => 'permit_empty|in_list[Pending,On Progress,Testing]',
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

        return redirect()->to('/maintenance-logs')->with('success', 'Ticket berhasil diperbarui.');
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

        if (! in_array($status, ['Pending', 'On Progress', 'Testing'])) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

        if (($log['status'] ?? null) === 'Completed') {
            return redirect()->back()->with('error', 'Ticket yang sudah ditutup tidak bisa diubah statusnya dari form ini.');
        }

        $updateData = [
            'status'    => $status,
            'closed_at' => null,
            'closed_by' => null,
        ];

        $this->logModel->update($id, $updateData);

        return redirect()->to('/maintenance-logs/show/' . $id)->with('success', 'Status ticket berhasil diperbarui menjadi ' . $status . '.');
    }

    /**
     * Close ticket dengan opsi backdate waktu selesai
     */
    public function closeTicket(int $id)
    {
        $log = $this->logModel->find($id);

        if (! $log) {
            return redirect()->to('/maintenance-logs')->with('error', 'Ticket tidak ditemukan.');
        }

        if (($log['status'] ?? null) === 'Completed') {
            return redirect()->back()->with('error', 'Ticket ini sudah ditutup sebelumnya.');
        }

        $closedAtInput = trim((string) $this->request->getPost('closed_at'));

        if ($closedAtInput === '') {
            $closedAt = date('Y-m-d H:i:s');
        } else {
            $normalized = str_replace('T', ' ', $closedAtInput);
            if (strlen($normalized) === 16) {
                $normalized .= ':00';
            }

            $timestamp = strtotime($normalized);
            if ($timestamp === false) {
                return redirect()->back()->withInput()->with('error', 'Format tanggal close ticket tidak valid.');
            }

            $closedAt = date('Y-m-d H:i:s', $timestamp);

            if (! empty($log['maintenance_date']) && strtotime($closedAt) < strtotime($log['maintenance_date'])) {
                return redirect()->back()->withInput()->with('error', 'Tanggal close ticket tidak boleh lebih awal dari tanggal ticket.');
            }
        }

        $this->logModel->update($id, [
            'status'    => 'Completed',
            'closed_at' => $closedAt,
            'closed_by' => auth()->id(),
        ]);

        return redirect()->to('/maintenance-logs/show/' . $id)->with('success', 'Ticket berhasil ditutup.');
    }

    /**
     * Reopen ticket yang sudah ditutup
     */
    public function reopenTicket(int $id)
    {
        $log = $this->logModel->find($id);

        if (! $log) {
            return redirect()->to('/maintenance-logs')->with('error', 'Ticket tidak ditemukan.');
        }

        if (($log['status'] ?? null) !== 'Completed') {
            return redirect()->back()->with('error', 'Ticket ini belum berstatus selesai.');
        }

        $status = (string) $this->request->getPost('status');
        if ($status === '') {
            $status = 'On Progress';
        }

        if (! in_array($status, ['Pending', 'On Progress', 'Testing'], true)) {
            return redirect()->back()->withInput()->with('error', 'Status reopen tidak valid.');
        }

        $this->logModel->update($id, [
            'status'    => $status,
            'closed_at' => null,
            'closed_by' => null,
        ]);

        return redirect()->to('/maintenance-logs/show/' . $id)->with('success', 'Ticket berhasil dibuka kembali.');
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

        return redirect()->to('/maintenance-logs')->with('success', 'Ticket berhasil dihapus.');
    }
}
