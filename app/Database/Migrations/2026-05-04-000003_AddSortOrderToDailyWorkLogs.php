<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSortOrderToDailyWorkLogs extends Migration
{
    public function up()
    {
        $this->forge->addColumn('daily_work_logs', [
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'after'      => 'is_done',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('daily_work_logs', 'sort_order');
    }
}
