<?php

namespace App\Models;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RubroArticulo extends Model
{
    use HasFactory, SoftDeletes;
    public $timestamps = false;
    protected $dates = ['deleted_at'];
    protected $fillable=[
        'id',
        'denominacion',
        'rubro_articulo_id',
        'deleted_at'
    ];

    public function articulos()
    {
        return $this->hasMany(ArticuloInsumo::class, 'rubro_articulos_id');
    }

    public function articulosTrashed()
    {
        return $this->hasMany(ArticuloInsumo::class, 'rubro_articulos_id')->withTrashed();
    }

    public function subRubroArticulosTrashed()
    {
        return $this->hasMany(RubroArticulo::class, 'rubro_articulo_id')->withTrashed();
    }

    public function subRubroArticulos(){
        return $this->hasMany(RubroArticulo::class, 'rubro_articulo_id');
    }

}

/*
    RubroArticulo
    Bebidas
        Alcoholicas
            Whiskeys
            Cervezas
            Licores
            Vinos
        Gaseosas -> Coca Cola -> Sprite -> Seven UP -> Levite
        Calientes -> Cafe -> Te -> Capuchino -> Moca
    Ingredientes
        Especias -> Pimenton -> Perejil -> Ajo
        Carnes -> Pollo -> Vacuna -> Cerdo
        Verduras -> Lechuga -> Tomate -> Zanahoria -> Limon
    Postres
        Helados
            Palito -> Bombon Chocolate
            Pote -> Tricolor
            Caja -> Tricolor
            Torta
        Frutas
            Tartas
            Ensalada
*/
