<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVentaDetalles extends Migration
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
            'venta_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'producto_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'cantidad' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ],
            'precio_unitario' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0,
            ],
            'subtotal' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0,
            ],
            'bonificado' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'descripcion_extra' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('venta_id');
        $this->forge->addKey('producto_id');

        $this->forge->addForeignKey('venta_id', 'ventas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('producto_id', 'productos', 'id', 'CASCADE', 'RESTRICT');

        $this->forge->createTable('venta_detalles');
    }

    public function down()
    {
        $this->forge->dropTable('venta_detalles');
    }
}