<?php

namespace App\Controllers;

use App\Models\MaintenanceLogModel;
use App\Models\AppModel;
use App\Models\CategoryModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $user = auth()->user();
        $logModel = new MaintenanceLogModel();
        $appModel = new AppModel();
        $categoryModel = new CategoryModel();

        // Jika punya admin.access → statistik semua log, jika tidak → hanya log milik sendiri
        $isAdmin = $user->can('admin.access');

        if ($isAdmin) {
            $baseQuery = $logModel;
        } else {
            $baseQuery = $logModel->where('created_by', $user->id);
        }

        // Hitung statistik per status
        $totalLogs     = (clone $baseQuery)->countAllResults(false);
        $pendingLogs   = (clone $baseQuery)->where('status', 'Pending')->countAllResults(false);
        $progressLogs  = (clone $baseQuery)->where('status', 'On Progress')->countAllResults(false);
        $testingLogs   = (clone $baseQuery)->where('status', 'Testing')->countAllResults(false);
        $completedLogs = (clone $baseQuery)->where('status', 'Completed')->countAllResults(false);
        $downtimeLogs  = (clone $baseQuery)->where('has_downtime', 1)->countAllResults(false);

        // Total aplikasi & kategori
        $totalApps       = $appModel->countAllResults();
        $totalCategories = $categoryModel->countAllResults();

        // 5 log terbaru
        if ($isAdmin) {
            $recentLogs = (new MaintenanceLogModel())
                ->select('maintenance_logs.*, apps.name as app_name, categories.name as category_name')
                ->join('apps', 'apps.id = maintenance_logs.app_id', 'left')
                ->join('categories', 'categories.id = maintenance_logs.category_id', 'left')
                ->orderBy('maintenance_logs.created_at', 'DESC')
                ->limit(5)
                ->find();
        } else {
            $recentLogs = (new MaintenanceLogModel())
                ->select('maintenance_logs.*, apps.name as app_name, categories.name as category_name')
                ->join('apps', 'apps.id = maintenance_logs.app_id', 'left')
                ->join('categories', 'categories.id = maintenance_logs.category_id', 'left')
                ->where('maintenance_logs.created_by', $user->id)
                ->orderBy('maintenance_logs.created_at', 'DESC')
                ->limit(5)
                ->find();
        }

        $data = [
            'title'           => 'Dashboard',
            'page_title'      => 'Dashboard',
            'user'            => $user,
            'userGroups'      => $user->getGroups(),
            'isAdmin'         => $isAdmin,
            'totalLogs'       => $totalLogs,
            'pendingLogs'     => $pendingLogs,
            'progressLogs'    => $progressLogs,
            'testingLogs'     => $testingLogs,
            'completedLogs'   => $completedLogs,
            'downtimeLogs'    => $downtimeLogs,
            'totalApps'       => $totalApps,
            'totalCategories' => $totalCategories,
            'recentLogs'      => $recentLogs,
        ];

        return $this->renderView('dashboard/index', $data);
    }
}
