<?php

namespace App\Models;

use CodeIgniter\Model;

class DailyWorkLogModel extends Model
{
    protected $table         = 'daily_work_logs';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['user_id', 'work_date', 'title', 'notes', 'is_done', 'sort_order'];
    protected $useTimestamps = true;
}
