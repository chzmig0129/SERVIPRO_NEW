<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQuejasTable extends Migration
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
            'fecha' => [
                'type'       => 'DATETIME',
                'null'       => false,
            ],
            'insecto' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'ubicacion' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'lineas' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('quejas');
    }

    public function down()
    {
        $this->forge->dropTable('quejas');
    }
} 