<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePrecioProductos extends Migration
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
            'cantidad_desde' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'cantidad_hasta' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'precio_unitario' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('producto_id');
        $this->forge->addForeignKey('producto_id', 'productos', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('precio_productos');
    }

    public function down()
    {
        $this->forge->dropTable('precio_productos');
    }
}