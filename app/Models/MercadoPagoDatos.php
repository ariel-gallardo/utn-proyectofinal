<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MercadoPagoDatos extends Model
{
    use HasFactory, SoftDeletes;
    public $timestamps = false;

    protected $primaryKey = 'identificadorPago';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'identificadorPago',
        'fechaCreacion',
        'fechaAprobacion',
        'formaPago',
        'metodoPago',
        'nroTarjeta',
        'estado',
        'deleted_at'
    ];
}
