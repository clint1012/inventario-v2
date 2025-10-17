<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Bienes extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'=>[
                'type'=>'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment'=>true,
            ],
            'cod_patrimonial'=>[
                'type'=>'VARCHAR',
                'constraint' => 12,
                'unique'=>true,
            ],
            'descripcion'=>[
                'type'=>'TINYTEXT',
            ],
            'marca'=>[
                'type'=>'VARCHAR',
                'constraint' => 50,
            ],
            'modelo'=>[
                'type'=>'VARCHAR',
                'constraint' => 50,
            ],
            'serie'=>[
                'type'=>'VARCHAR',
                'constraint' => 50,
            ],
            'procesador'=>[
                'type'=>'VARCHAR',
                'constraint' => 50,
            ],
            'memoria'=>[
                'type'=>'VARCHAR',
                'constraint' => 50,
            ],
            'sistema_operativo'=>[
                'type'=>'VARCHAR',
                'constraint' => 50,
            ],
            'estado'=>[
                'type'=>'VARCHAR',
                'constraint' => 50,
            ],
            'fecha_adquisicion'=>[
                'type'=>'DATE',
            ],
            'id_departamento'=>[
                'type'=>'INT',
                'constraint' => 5,
                'unsigned' => true
            ],


            ]);
            $this->forge->addKey('id',true);
            $this->forge->addforeignKey('id_departamento','departamentos','id');

            $this->forge->createTable('bienes');
    }

    public function down()
    {
        $this->forge->dropTable('bienes');
    }
}
