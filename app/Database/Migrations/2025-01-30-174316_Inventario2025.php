<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Inventario2025 extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'=> [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_personas'=>[
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'id_departamentos'=>[
                'type' => 'varchar',
                'constraint' => 200,
            ],
            'id_locales'=>[
                'type' => 'varchar',
                'constraint' => 200,
            ],
            'id_bienes'=>[
                'type' => 'varchar',
                'constraint' => 200,
            ],
        ]);
        $this->forge->addKey('id',true);
        $this->forge->createTable('inventario2025');
    }

    public function down()
    {
        $this->forge->dropTable('inventario2025');
    }
}
