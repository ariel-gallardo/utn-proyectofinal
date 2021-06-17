<?php

namespace App\Http\Controllers;

use App\Models\Domicilio;
use App\Models\Persona;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{

    public function registrar(Request $request)
    {
        $request->validate([
            'correo' => 'required | email | unique:usuarios',
            'clave' => 'required | confirmed',
            'persona.nombre' => 'required | string',
            'persona.apellido' => 'required | string',
            'persona.telefono' => 'required | numeric',
            'domicilio.calle' => 'required | string',
            'domicilio.numero' => 'required | numeric',
            'domicilio.localidad' => 'required | string',
            'imagen' => 'sometimes| base64image'
        ]);

        $usuario = Usuario::create([
            'correo' => $request->correo,
            'imagen' => $request->imagen,
            'clave' => Hash::make($request->clave),
            'persona_id' => Persona::create([
                'nombre' => $request->persona['nombre'],
                'apellido' => $request->persona['apellido'],
                'telefono' => $request->persona['telefono'],
                'domicilio_id' => Domicilio::create([
                    'calle' => $request->domicilio['calle'],
                    'numero' => $request->domicilio['numero'],
                    'localidad' => $request->domicilio['localidad']
                ])->id
            ])->id
        ]);

        return response(
            [
                //'usuario' => $usuario,
                'token' => $usuario->createToken('BuenSabor')->plainTextToken
            ],
            201
        );
    }

    public function loguear(Request $request)
    {
        $request->validate([
            'correo' => 'required | email',
            'clave' => 'required | string'
        ]);

        $usuario = Usuario::where('correo', $request->correo)->first();

        if (isset($usuario)) {
            if (Hash::check($request->clave, $usuario->clave)) {

                $usuario->load('persona');
                $usuario->persona->load('domicilio');
                $usuario->load('rol');

                return response([
                    //'usuario' => $usuario,
                    'token' => $usuario->createToken('BuenSabor')->plainTextToken
                ], 200);
            }
        }
    }

    public function desloguear(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response(['mensaje' => 'sesion cerrada'], 200);
    }

    public function borrar(Request $request)
    {
        $usuario = Usuario::find($request->user()->id);
        if (isset($usuario)) {
            $token = $request->user()->currentAccessToken();
            if (isset($token)) {
                $token->delete();
                $usuario->delete();
                return response(['mensaje' => 'usuario eliminado'], 200);
            } else {
                return response(['mensaje' => 'falta el token'], 405);
            }
        }
    }

    public function modificar(Request $request)
    {
        $id = $request->id;
        if (isset($id)) {
            $usuario = Usuario::find($id);
            if (isset($usuario)) {
                
                

    
                
                $usuario->load('persona');
                $usuario->persona->load('domicilio');
                $usuario->load('rol');


                foreach ($request->persona as $campo => $valor) {
                    $usuario->persona[$campo] = $valor;
                }
                foreach ($request->domicilio as $campo => $valor) {
                    $usuario->persona->domicilio[$campo] = $valor;
                }
  
                $usuario->push();
                    $usuario->correo = $request->correo;
                    $usuario->clave = $request->clave;
                    $usuario->imagen = $request->imagen;
                $usuario->save();
                
                $token = null;
                if(isset($request->correo) || isset($request->clave)){
                    $request->user()->currentAccessToken()->delete();
                    $token = $usuario->createToken('BuenSabor')->plainTextToken;
                }


                return response(
                    [
                        'mensaje' => 'Usuario modificado satisfactoriamente',
                        'usuario' => $usuario,
                        'token' => $token != null ? $token : 'No ha cambiado'
                    ],
                    200
                );
            }
        }
        return response(['mensaje' => 'falta el token'], 405);
    }

    public function ver(Request $request){
        $usuario = Usuario::find($request->user()->id);
        $usuario->load('persona');
        $usuario->persona->load('domicilio');
        $usuario->load('rol');
        return response(['usuario' => $usuario],200);
    }
}
