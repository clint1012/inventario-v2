<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AgregaFechasBienes extends Migration
{
    public function up()
    {
        //
        $this->forge->addColumn('bienes',[
            'created_at'=>[
                'type' => 'DATETIME',
                'after' => 'id_departamento'
            ],
            'updated_at'=>[
                'type' => 'DATETIME',
                'after' => 'created_at'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('bienes','updated_at');
        $this->forge->dropColumn('bienes','created_at');
    }
}
