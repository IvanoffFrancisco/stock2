<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMolinoToProductos extends Migration
{
    public function up()
    {
        $fields = [
            'molino' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
                'after'      => 'nombre',
            ],
        ];

        $this->forge->addColumn('productos', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('productos', 'molino');
    }
}