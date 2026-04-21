<?php

namespace App\Models;

use CodeIgniter\Model;

class VentaDetalleModel extends Model
{
    protected $table            = 'venta_detalles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'venta_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'bonificado',
        'descripcion_extra',
    ];

    protected $useTimestamps = false;
}