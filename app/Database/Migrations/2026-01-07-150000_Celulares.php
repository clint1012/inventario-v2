<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Celulares extends Migration
{
    public function up()
    {
        // Tabla para equipos celulares
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'numero_serie' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'imei' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'modelo' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'descripcion' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'estado' => [
                'type' => 'ENUM',
                'constraint' => ['disponible', 'asignado', 'baja'],
                'default' => 'disponible',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('imei');
        $this->forge->createTable('celulares');

        // Tabla para movimientos de celulares (entregas y devoluciones)
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_celular' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_personas' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'id_departamentos' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'id_locales' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'tipo_movimiento' => [
                'type' => 'ENUM',
                'constraint' => ['entrega', 'devolucion'],
                'default' => 'entrega',
            ],
            'fecha_movimiento' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'observaciones' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'lote' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'anulado' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'motivo_anulacion' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_celular', 'celulares', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('movimientos_celulares');
    }

    public function down()
    {
        $this->forge->dropTable('movimientos_celulares');
        $this->forge->dropTable('celulares');
    }
}
