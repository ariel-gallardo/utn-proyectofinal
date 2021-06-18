<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RubroArticulo extends Model
{
    use HasFactory, SoftDeletes;
    public $timestamps = false;
    protected $fillable=[
        'id',
        'denominacion'
    ];

    public function articulos()
    {
        return $this->hasMany(ArticuloInsumo::class, 'rubro_articulos_id');
    }
}
