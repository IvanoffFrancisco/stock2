<?php

namespace App\Models;

use CodeIgniter\Model;

class PedidoModel extends Model
{
    protected $table            = 'pedidos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'cliente_id',
        'usuario_id',
        'fecha_pedido',
        'fecha_entrega',
        'forma_pago',
        'estado',
        'subtotal',
        'descuento',
        'total',
        'observacion',
        'created_at',
    ];

    protected $useTimestamps = false;
}