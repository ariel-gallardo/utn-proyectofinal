<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticuloInsumo extends Model
{
    use HasFactory, SoftDeletes;
    public $timestamps = false;

    public function articuloManufacturadoDetalles(){
        return $this->belongsToMany(ArticuloManufacturadoDetalle::class);
    }

    public function rubroArticulo(){
        return $this->hasOne(RubroArticulo::class);
    }
}
