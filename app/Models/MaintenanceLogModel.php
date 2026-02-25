<?php

namespace App\Models;

use CodeIgniter\Model;

class MaintenanceLogModel extends Model
{
    protected $table         = 'maintenance_logs';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'app_id',
        'category_id',
        'maintenance_date',
        'title',
        'problem_description',
        'root_cause',
        'action_taken',
        'status',
        'technician_id',
        'has_downtime',
        'downtime_duration',
        'attachment',
        'created_by',
    ];
    protected $useTimestamps = true;

    /**
     * Get log dengan relasi aplikasi dan kategori
     */
    public function getLogsWithRelations()
    {
        return $this->select('maintenance_logs.*, apps.name as app_name, categories.name as category_name')
                    ->join('apps', 'apps.id = maintenance_logs.app_id', 'left')
                    ->join('categories', 'categories.id = maintenance_logs.category_id', 'left')
                    ->orderBy('maintenance_logs.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get single log dengan relasi
     */
    public function getLogWithRelations(int $id)
    {
        return $this->select('maintenance_logs.*, apps.name as app_name, categories.name as category_name')
                    ->join('apps', 'apps.id = maintenance_logs.app_id', 'left')
                    ->join('categories', 'categories.id = maintenance_logs.category_id', 'left')
                    ->where('maintenance_logs.id', $id)
                    ->first();
    }
}
