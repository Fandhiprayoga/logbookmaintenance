<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddClosedFieldsToMaintenanceLogs extends Migration
{
    public function up()
    {
        $this->forge->addColumn('maintenance_logs', [
            'closed_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
                'comment' => 'Waktu log diselesaikan (status → Completed)',
                'after'   => 'created_by',
            ],
            'closed_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
                'comment'    => 'User ID yang menutup/menyelesaikan log',
                'after'      => 'closed_at',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('maintenance_logs', ['closed_at', 'closed_by']);
    }
}
