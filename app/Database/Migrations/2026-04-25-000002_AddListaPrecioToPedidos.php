<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddListaPrecioToPedidos extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pedidos', [
            'lista_precio' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'General',
                'after'      => 'usuario_id',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pedidos', 'lista_precio');
    }
}