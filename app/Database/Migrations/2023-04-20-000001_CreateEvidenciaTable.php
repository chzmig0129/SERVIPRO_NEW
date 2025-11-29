<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEvidenciaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_plano' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'           => false,
            ],
            'ubicacion' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
                'null'           => false,
            ],
            'descripcion' => [
                'type'           => 'TEXT',
                'null'           => true,
            ],
            'fecha_registro' => [
                'type'           => 'DATETIME',
                'null'           => true,
                'default'        => null,
            ],
            'imagen_evidencia' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
                'null'           => true,
            ],
            'coordenada_x' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'null'           => false,
            ],
            'coordenada_y' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'null'           => false,
            ],
            'visto_bueno_supervisor' => [
                'type'           => 'TINYINT',
                'constraint'     => 1,
                'null'           => false,
                'default'        => 0,
            ],
            'created_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
                'default'        => null,
            ],
            'updated_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
                'default'        => null,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_plano', 'planos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('evidencia');
    }

    public function down()
    {
        $this->forge->dropTable('evidencia');
    }
} 