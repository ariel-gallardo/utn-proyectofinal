<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticuloManufacturado extends Model
{
    use HasFactory, SoftDeletes;
    public $timestamps = false;

    protected $fillable = [
        'tiempoEstimadoCocina',
        'denominacion',
        'precioVenta',
        'imagen',
        'id',
        'rubro_generals_id'
    ];

    public function ingredientesTrashed(){
        return $this->hasManyThrough(ArticuloInsumo::class,ArticuloManufacturadoDetalle::class,'articulo_manufacturado_id','id','id', 'articulo_insumo_id')->withTrashedParents();
    }

    public function ingredientes(){

    }
}


/*

*/
