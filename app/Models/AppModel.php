<?php

namespace App\Models;

use CodeIgniter\Model;

class AppModel extends Model
{
    protected $table         = 'apps';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['name', 'url', 'tech_stack', 'pic', 'description'];
    protected $useTimestamps = true;
}
