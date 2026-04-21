<?php

namespace App\Models;

use CodeIgniter\Model;

class VentaModel extends Model
{
    protected $table            = 'ventas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'pedido_id',
        'cliente_id',
        'usuario_id',
        'fecha_venta',
        'fecha_entrega',
        'forma_pago',
        'estado_entrega',
        'subtotal',
        'descuento',
        'total',
        'observacion',
        'created_at',
    ];

    protected $useTimestamps = false;
}