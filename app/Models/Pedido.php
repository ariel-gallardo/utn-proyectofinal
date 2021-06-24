<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pedido extends Model
{
    use HasFactory, SoftDeletes;
    public $timestamps = false;
    protected $softDelete = true;

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'id',
        'fecha',
        'estado',
        'horaEstimadaFin',
        'tipoEnvio',
        'total',
        'usuario_id',
    ];

    public function detallePedidosManufacturados (){
        //return $this->hasManyThrough(ArticuloManufacturado::class,DetallePedido::class,'pedido_id','id','id', 'articulo_manufacturado_id')->with('detalles');
        return $this->belongsToMany(ArticuloManufacturado::class,DetallePedido::class)->withPivot(['cantidad as cantidad','subtotal as subtotal', 'pedido_id as pedido_id', 'articulo_manufacturado_id as articulo_manufacturado_id', 'deleted_at as borrado'])->select(['pedido_id','articulo_manufacturado_id','denominacion','cantidad', 'subtotal', 'precioVenta', 'imagen']);
    }

    public function detallePedidosArticulos(){
        return $this->belongsToMany(ArticuloInsumo::class, DetallePedido::class)->withPivot(['cantidad as cantidad','subtotal as subtotal', 'pedido_id as pedido_id', 'articulo_insumo_id as articulo_insumo_id', 'deleted_at as borrado'])->select(['pedido_id','articulo_manufacturado_id','denominacion','cantidad', 'subtotal', 'precioVenta']);
        //return $this->hasManyThrough(ArticuloInsumo::class, DetallePedido::class,'pedido_id','id','id', 'articulo_insumo_id')->with('detalles');
    }
}
