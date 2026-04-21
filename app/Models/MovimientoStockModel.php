<?php

namespace App\Models;

use CodeIgniter\Model;

class MovimientoStockModel extends Model
{
    protected $table            = 'movimientos_stock';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'producto_id',
        'usuario_id',
        'tipo_movimiento',
        'cantidad',
        'motivo',
        'observacion',
        'created_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = false;
}