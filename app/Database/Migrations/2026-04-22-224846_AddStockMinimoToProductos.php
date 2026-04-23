<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStockMinimoToProductos extends Migration
{
    public function up()
    {
        $fields = [
            'stock_minimo' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 10,
                'after'      => 'stock_unidades',
            ],
        ];

        $this->forge->addColumn('productos', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('productos', 'stock_minimo');
    }
}