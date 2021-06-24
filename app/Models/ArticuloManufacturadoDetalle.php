<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticuloManufacturadoDetalle extends Model
{
    protected $softDelete = true;
    use HasFactory, SoftDeletes;
    public $timestamps = false;
    protected $fillable = [
        'cantidad',
        'unidadMedida',
        'articulo_insumo_id',
        'articulo_manufacturado_id'
    ];

}
