<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAuditoriaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'usuario_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'usuario_nombre' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'accion' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'comment' => 'CREAR, EDITAR, ELIMINAR, LOGIN, LOGOUT, etc.',
            ],
            'modulo' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'comment' => 'Bienes, Movimientos, Usuarios, etc.',
            ],
            'registro_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'ID del registro afectado',
            ],
            'detalles' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON o texto con detalles adicionales',
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => '45',
                'null' => true,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('usuario_id');
        $this->forge->addKey('modulo');
        $this->forge->addKey('accion');
        $this->forge->addKey('created_at');

        $this->forge->createTable('auditoria');
    }

    public function down()
    {
        $this->forge->dropTable('auditoria');
    }
}
