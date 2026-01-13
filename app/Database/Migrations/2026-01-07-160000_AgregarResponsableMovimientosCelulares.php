<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AgregarResponsableMovimientosCelulares extends Migration
{
    public function up()
    {
        $this->forge->addColumn('movimientos_celulares', [
            'responsable_nombre' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'observaciones'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('movimientos_celulares', 'responsable_nombre');
    }
}
