<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddResueltoFieldsToEvidenciaTable extends Migration
{
    public function up()
    {
        $forge = \Config\Database::forge();
        
        // Agregar campos para imagen resuelta y estado
        $fields = [
            'imagen_resuelta' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'comment'    => 'Ruta de la imagen de la evidencia resuelta'
            ],
            'estado' => [
                'type'       => 'ENUM',
                'constraint' => ['Pendiente', 'Resuelta'],
                'default'    => 'Pendiente',
                'comment'    => 'Estado de la evidencia'
            ],
            'fecha_resolucion' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'comment'    => 'Fecha en que se resolvió la evidencia'
            ]
        ];
        
        $forge->addColumn('evidencia', $fields);
    }

    public function down()
    {
        $forge = \Config\Database::forge();
        
        // Eliminar los campos agregados
        $forge->dropColumn('evidencia', ['imagen_resuelta', 'estado', 'fecha_resolucion']);
    }
} 