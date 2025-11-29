<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVistoBuenoSupervisorToEvidencia extends Migration
{
    public function up()
    {
        $this->forge->addColumn('evidencia', [
            'visto_bueno_supervisor' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 0,
                'after'      => 'coordenada_y',
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('evidencia', 'visto_bueno_supervisor');
    }
} 