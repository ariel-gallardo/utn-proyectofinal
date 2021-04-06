<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{

    public function registrar(Request $request){
        $request->validate([
            'correo' => 'required | email | unique:usuarios',
            'clave' => 'required | confirmed'
        ]);

        $usuario = Usuario::create([
            'correo' => $request->correo,
            'clave' => Hash::make($request->clave)
        ]);

        return response([
            'usuario' => $usuario,
            'token' => $usuario->createToken('BuenSabor')->plainTextToken
        ],
        201);
    }

    public function loguear(Request $request){
        $request->validate([
            'correo' => 'required | email',
            'clave' => 'required | string'
        ]);

        $usuario = Usuario::where('correo',$request->correo)->first();

        if(isset($usuario)){
            if (Hash::check($request->clave, $usuario->clave)) {
                return response([
                    'usuario' => $usuario,
                    'token' => $usuario->createToken('BuenSabor')->plainTextToken
                ], 200);
            }
        }

    }

    public function desloguear(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response(['mensaje' => 'sesion cerrada'],200);
    }

    public function borrar(Request $request){
        $usuario = Usuario::find($request->user()->id);
        if(isset($usuario)){
            $token = $request->user()->currentAccessToken();
            if(isset($token)){
                $token->delete();
                $usuario->delete();
                return response(['mensaje' => 'usuario eliminado'], 200);
            }else{
                return response(['mensaje' => 'falta el token'], 405);
            }
        }
    }

}
