<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddArchivoFieldToQuejasTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('quejas', [
            'archivo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'lineas'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('quejas', 'archivo');
    }
} 