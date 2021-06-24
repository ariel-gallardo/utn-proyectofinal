<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetallePedido extends Model
{
    use HasFactory, SoftDeletes;
    public $timestamps = false;
    protected $softDelete = true;
    protected $fillable = [
        'articulo_insumo_id',
        'articulo_manufacturado_id',
        'cantidad',
        'pedido_id',
        'subtotal'
    ];
}
