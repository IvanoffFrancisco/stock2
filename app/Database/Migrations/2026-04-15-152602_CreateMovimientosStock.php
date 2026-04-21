<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMovimientosStock extends Migration
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
            'producto_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'usuario_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'tipo_movimiento' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'cantidad' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'motivo' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'observacion' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('producto_id');
        $this->forge->addKey('usuario_id');

        $this->forge->addForeignKey('producto_id', 'productos', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('usuario_id', 'usuarios', 'id', 'CASCADE', 'RESTRICT');

        $this->forge->createTable('movimientos_stock');
    }

    public function down()
    {
        $this->forge->dropTable('movimientos_stock');
    }
}