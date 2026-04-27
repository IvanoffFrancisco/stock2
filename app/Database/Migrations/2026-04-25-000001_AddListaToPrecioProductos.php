<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddListaToPrecioProductos extends Migration
{
    public function up()
    {
        $fields = [
            'lista' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'General',
                'after'      => 'producto_id',
            ],
        ];

        $this->forge->addColumn('precio_productos', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('precio_productos', 'lista');
    }
}