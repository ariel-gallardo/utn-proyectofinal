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
        //return $this->hasManyThrough(ArticuloInsumo::class,ArticuloManufacturadoDetalle::class,'articulo_manufacturado_id','id','id', 'articulo_insumo_id')->withTrashedParents();
        //return $this->hasManyThrough(ArticuloManufacturadoDetalle::class,ArticuloInsumo::class, 'id', 'articulo_manufacturado_id', 'id', 'id')->withTrashedParents();
        //return $this->belongsToMany(ArticuloInsumo::class,'articulo_manufacturado_detalles', 'articulo_manufacturado_id', 'articulo_insumo_id', 'id', 'id');
        //return $this->with()->get();
        return $this->belongsToMany(ArticuloInsumo::class, ArticuloManufacturadoDetalle::class)->withPivot('cantidad')->withTrashed()->select(array('denominacion','articulo_insumos.unidadMedida'));
    }

    public function ingredientes(){
        return $this->belongsToMany(ArticuloInsumo::class, ArticuloManufacturadoDetalle::class)->withPivot('cantidad')->select(array('denominacion', 'articulo_insumos.unidadMedida'));
    }
}


/*

*/
