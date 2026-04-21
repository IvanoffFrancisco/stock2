<?php

namespace App\Models;

use CodeIgniter\Model;

class PedidoDetalleModel extends Model
{
    protected $table            = 'pedido_detalles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'pedido_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'bonificado',
        'descripcion_extra',
    ];

    protected $useTimestamps = false;
}