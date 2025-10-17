<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Personas extends Migration
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
            'nombre'=>[
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'descripcion'=>[
                'type' => 'TINYTEXT',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id',true);
        $this->forge->createTable('personas');
    }

    public function down()
    {
        //
        $this->forge->dropTable('personas');
    }
}
