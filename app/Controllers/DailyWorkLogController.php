<?php

namespace App\Controllers;

use App\Models\DailyWorkLogModel;

class DailyWorkLogController extends BaseController
{
    protected DailyWorkLogModel $dailyWorkLogModel;

    public function __construct()
    {
        $this->dailyWorkLogModel = new DailyWorkLogModel();
    }

    public function index()
    {
        $userId = (int) auth()->id();
        $selectedDate = (string) ($this->request->getGet('date') ?? date('Y-m-d'));

        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $selectedDate)) {
            $selectedDate = date('Y-m-d');
        }

        $items = $this->dailyWorkLogModel
            ->where('user_id', $userId)
            ->where('work_date', $selectedDate)
            ->orderBy('is_done', 'ASC')
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        $doneCount = 0;
        foreach ($items as $item) {
            if ((int) $item['is_done'] === 1) {
                $doneCount++;
            }
        }

        $data = [
            'title'        => 'Log Kerjaan Harian',
            'page_title'   => 'Log Kerjaan Harian',
            'selectedDate' => $selectedDate,
            'items'        => $items,
            'totalCount'   => count($items),
            'doneCount'    => $doneCount,
        ];

        return $this->renderView('daily_work_logs/index', $data);
    }

    public function store()
    {
        $rules = [
            'work_date' => 'required|valid_date[Y-m-d]',
            'title'     => 'required|max_length[255]',
            'notes'     => 'permit_empty',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $lastOrder = $this->dailyWorkLogModel
            ->select('sort_order')
            ->where('user_id', (int) auth()->id())
            ->where('work_date', (string) $this->request->getPost('work_date'))
            ->orderBy('sort_order', 'DESC')
            ->first();

        $nextOrder = $lastOrder ? ((int) $lastOrder['sort_order'] + 1) : 1;

        $this->dailyWorkLogModel->insert([
            'user_id'   => (int) auth()->id(),
            'work_date' => (string) $this->request->getPost('work_date'),
            'title'     => (string) $this->request->getPost('title'),
            'notes'     => (string) ($this->request->getPost('notes') ?? ''),
            'is_done'   => 0,
            'sort_order'=> $nextOrder,
        ]);

        return redirect()->to('/daily-work-logs?date=' . urlencode((string) $this->request->getPost('work_date')))
            ->with('success', 'Task harian berhasil ditambahkan.');
    }

    public function toggle(int $id)
    {
        $item = $this->dailyWorkLogModel->where('id', $id)->where('user_id', (int) auth()->id())->first();

        if (! $item) {
            return redirect()->back()->with('error', 'Task tidak ditemukan.');
        }

        $this->dailyWorkLogModel->update($id, [
            'is_done' => ((int) $item['is_done'] === 1) ? 0 : 1,
        ]);

        return redirect()->to('/daily-work-logs?date=' . urlencode((string) $item['work_date']))
            ->with('success', 'Status task berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $item = $this->dailyWorkLogModel->where('id', $id)->where('user_id', (int) auth()->id())->first();

        if (! $item) {
            return redirect()->back()->with('error', 'Task tidak ditemukan.');
        }

        $this->dailyWorkLogModel->delete($id);

        return redirect()->to('/daily-work-logs?date=' . urlencode((string) $item['work_date']))
            ->with('success', 'Task berhasil dihapus.');
    }

    public function reorder()
    {
        $userId = (int) auth()->id();
        $workDate = (string) $this->request->getPost('work_date');
        $orderedIds = $this->request->getPost('ordered_ids');

        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $workDate)) {
            return redirect()->back()->with('error', 'Tanggal tidak valid.');
        }

        if (! is_array($orderedIds) || empty($orderedIds)) {
            return redirect()->back()->with('error', 'Data urutan task tidak valid.');
        }

        $position = 1;
        foreach ($orderedIds as $taskId) {
            $taskId = (int) $taskId;
            if ($taskId <= 0) {
                continue;
            }

            $row = $this->dailyWorkLogModel
                ->where('id', $taskId)
                ->where('user_id', $userId)
                ->where('work_date', $workDate)
                ->first();

            if (! $row) {
                continue;
            }

            $this->dailyWorkLogModel->update($taskId, ['sort_order' => $position]);
            $position++;
        }

        return redirect()->to('/daily-work-logs?date=' . urlencode($workDate))
            ->with('success', 'Urutan task berhasil diperbarui.');
    }
}
