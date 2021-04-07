<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RubroArticulo extends Model
{
    use HasFactory, SoftDeletes;
    public $timestamps = false;

    protected $fillable = [
        'rubro_articulo_id',
        'denominacion'
    ];

    protected $hidden = [
        'deleted_at'
    ];

    public function rubroArticulos(){
        return $this->hasMany(RubroArticulo::class);
    }

    public function articuloInsumos(){
        return $this->belongsToMany(ArticuloInsumo::class);
    }
}
