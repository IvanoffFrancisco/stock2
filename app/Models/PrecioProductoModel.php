<?php

namespace App\Models;

use CodeIgniter\Model;

class PrecioProductoModel extends Model
{
    protected $table            = 'precio_productos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'producto_id',
        'cantidad_desde',
        'cantidad_hasta',
        'precio_unitario',
        'created_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = false;
}