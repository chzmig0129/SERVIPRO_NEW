<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPlanoIdToIncidenciasTable extends Migration
{
    public function up()
    {
        $fields = [
            'plano_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'           => true,
                'after'          => 'sede_id',
            ],
        ];

        $this->forge->addColumn('incidencias', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('incidencias', 'plano_id');
    }
}

