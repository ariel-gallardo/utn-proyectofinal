<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;


class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'correo',
        'clave',
        'imagen',
        'rol_id',
        'persona_id',
    ];

    protected $hidden = [
        'clave',
        'deleted_at',
        'persona_id',
    ];

    public function persona(){
        return $this->belongsTo(Persona::class);
    }

    public function rol(){
        return $this->belongsTo(Rol::class);
    }

    public function facturas(){
        return $this->hasManyThrough(Factura::class,Pedido::class);
    }


}
