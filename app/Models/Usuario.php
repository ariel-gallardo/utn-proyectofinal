<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'correo',
        'clave',
        'rol_id',
        'persona_id'
    ];

    protected $hidden = [
        'clave',
        'deleted_at'
    ];

    public function persona(){
        return $this->hasOne(Persona::class);
    }

    public function rol(){
        return $this->hasOne(Rol::class);
    }
}
