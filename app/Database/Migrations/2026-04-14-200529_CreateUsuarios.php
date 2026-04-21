<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsuarios extends Migration
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
    'nombre' => [
        'type'       => 'VARCHAR',
        'constraint' => 100,
    ],
    'email' => [
        'type'       => 'VARCHAR',
        'constraint' => 100,
    ],
    'password' => [
        'type'       => 'VARCHAR',
        'constraint' => 255,
    ],
    'rol' => [
        'type'       => 'VARCHAR',
        'constraint' => 20,
        'default'    => 'consultor',
    ],
    'created_at' => [
        'type' => 'DATETIME',
        'null' => true,
    ],
    ]);

    $this->forge->addKey('id', true);
    $this->forge->addUniqueKey('email');
    $this->forge->createTable('usuarios');
}

    public function down()
    {
        //
    }
}
