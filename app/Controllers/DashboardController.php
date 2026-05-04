<?php

namespace App\Controllers;

use App\Models\MaintenanceLogModel;
use App\Models\AppModel;
use App\Models\CategoryModel;
use CodeIgniter\Shield\Models\UserModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $user = auth()->user();
        $appModel = new AppModel();
        $categoryModel = new CategoryModel();

        // Jika punya admin.access -> statistik semua ticket, jika tidak -> hanya ticket milik sendiri.
        $isAdmin = $user->can('admin.access');

        $countLogs = static function (bool $isAdmin, int $userId, ?string $status = null, ?int $hasDowntime = null): int {
            $query = new MaintenanceLogModel();

            if (! $isAdmin) {
                $query->where('created_by', $userId);
            }

            if ($status !== null) {
                $query->where('status', $status);
            }

            if ($hasDowntime !== null) {
                $query->where('has_downtime', $hasDowntime);
            }

            return $query->countAllResults();
        };

        // Statistik utama ticketing
        $totalLogs     = $countLogs($isAdmin, (int) $user->id);
        $pendingLogs   = $countLogs($isAdmin, (int) $user->id, 'Pending');
        $progressLogs  = $countLogs($isAdmin, (int) $user->id, 'On Progress');
        $testingLogs   = $countLogs($isAdmin, (int) $user->id, 'Testing');
        $completedLogs = $countLogs($isAdmin, (int) $user->id, 'Completed');
        $downtimeLogs  = $countLogs($isAdmin, (int) $user->id, null, 1);
        $activeLogs    = $pendingLogs + $progressLogs + $testingLogs;
        $completionRate = $totalLogs > 0 ? (int) round(($completedLogs / $totalLogs) * 100) : 0;

        // Total aplikasi & kategori
        $totalApps       = $appModel->countAllResults();
        $totalCategories = $categoryModel->countAllResults();
        $totalUsers      = $user->can('users.list') ? (new UserModel())->countAllResults() : null;

        // Ticket terbaru
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
            'activeLogs'      => $activeLogs,
            'completionRate'  => $completionRate,
            'downtimeLogs'    => $downtimeLogs,
            'totalApps'       => $totalApps,
            'totalCategories' => $totalCategories,
            'totalUsers'      => $totalUsers,
            'recentLogs'      => $recentLogs,
        ];

        return $this->renderView('dashboard/index', $data);
    }
}
