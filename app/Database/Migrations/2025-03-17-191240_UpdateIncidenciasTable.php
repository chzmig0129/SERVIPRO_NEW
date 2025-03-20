<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateIncidenciasTable extends Migration
{
    public function up()
    {
        // Comprobar si existe la columna id_trampa
        if ($this->db->fieldExists('id_trampa', 'incidencias')) {
            // Cambiar el nombre de la columna de id_trampa a trampa_id
            $this->forge->modifyColumn('incidencias', [
                'id_trampa' => [
                    'name' => 'trampa_id',
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => false
                ]
            ]);
        }
    }

    public function down()
    {
        // Comprobar si existe la columna trampa_id
        if ($this->db->fieldExists('trampa_id', 'incidencias')) {
            // Cambiar el nombre de la columna de trampa_id a id_trampa
            $this->forge->modifyColumn('incidencias', [
                'trampa_id' => [
                    'name' => 'id_trampa',
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => false
                ]
            ]);
        }
    }
}
