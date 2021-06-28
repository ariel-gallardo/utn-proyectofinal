<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Factura extends Model
{
    use HasFactory, SoftDeletes;
    public $timestamps = false;
    protected $softDelete = true;
    protected $fillable = [
        'id',
        'fecha',
        'montoDescuento',
        'formaPago',
        'nroTarjeta',
        'totalVenta',
        'pedido_id'
    ];

    public function detallesFactura (){
        return $this->hasMany(DetalleFactura::class);
    }

    public function pedido(){
        return $this->hasOne(Pedido::class, 'id', 'pedido_id');
    }
}
