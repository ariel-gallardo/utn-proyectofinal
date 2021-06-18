<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticuloInsumo extends Model
{
    use HasFactory, SoftDeletes;
    public $timestamps = false;

    protected $fillable=[
        'id',
        'denominacion',
        'precioCompra',
        'precioVenta',
        'stockActual',
        'stockMinimo',
        'unidadMedida',
        'esInsumo',
        'rubro_articulos_id'
    ];
}
