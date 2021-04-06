<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticuloManufacturado extends Model
{
    use HasFactory, SoftDeletes;
    public $timestamps = false;

    public function rubroGeneral(){
        return $this->hasOne(RubroGeneral::class);
    }

    public function articuloManufacturadoDetalles(){
        return $this->belongsToMany(ArticuloManufacturadoDetalle::class);
    }
}
