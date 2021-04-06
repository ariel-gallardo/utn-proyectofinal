<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Persona extends Model
{
    use HasFactory, SoftDeletes;
    public $timestamps = false;
    protected $fillable=[
        'nombre',
        'apellido',
        'telefono',
        'domicilio_id'
    ];
    protected $hidden=[
        'id',
        'deleted_at',
        'domicilio_id'
    ];
    public function domicilio(){
        return $this->belongsTo(Domicilio::class);
    }
}
