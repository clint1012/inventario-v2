<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RegimenLaboral extends Migration
{
    public function up()
    {
        //
        $this->forge->addField([
            'id'=> [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'regimen_laboral'=>[
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'descripcion'=>[
                'type' => 'TINYTEXT',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id',true);
        $this->forge->createTable('regimen_laboral');
    }

    public function down()
    {
        //
        $this->forge->dropTable('regimen_laboral');
    }
}
