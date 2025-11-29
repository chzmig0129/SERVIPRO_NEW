<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVentasTable extends Migration
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
                'type'       => 'DATE',
                'null'       => false,
            ],
            'cliente' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'producto' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'cantidad' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'precio_unitario' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
            ],
            'total' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
            ],
            'forma_pago' => [
                'type'       => 'ENUM',
                'constraint' => ['Efectivo', 'Tarjeta', 'Transferencia'],
                'default'    => 'Efectivo',
                'null'       => false,
            ],
            'sede_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('sede_id', 'sedes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('ventas');
    }

    public function down()
    {
        $this->forge->dropTable('ventas');
    }
} 