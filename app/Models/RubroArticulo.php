<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RubroArticulo extends Model
{
    use HasFactory, SoftDeletes;
    public $timestamps = false;

    public function rubroArticulos(){
        return $this->belongsToMany(RubroArticulo::class);
    }

    public function articuloInsumos(){
        return $this->belongsToMany(ArticuloInsumo::class);
    }
}
