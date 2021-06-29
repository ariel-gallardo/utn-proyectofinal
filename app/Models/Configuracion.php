<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Configuracion extends Model
{
    use HasFactory, SoftDeletes;
    public $timestamps = false;
    protected $softDelete = true;
    protected $fillable = [
        'id',
        'cantidadCocineros',
        'emailEmpresa',
        'tokenMercadoPago',
    ];
}
