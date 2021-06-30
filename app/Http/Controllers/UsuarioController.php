<?php

namespace App\Http\Controllers;

use App\Models\Domicilio;
use App\Models\Persona;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                'mensaje' => 'usuario registrado correctamente',
                'token' => $usuario->createToken('BuenSabor')->plainTextToken
            ],
            201
        );
    }

    public function getFacturas(Request $request){
        $usuario = Usuario::find(Auth::id());
        if(isset($usuario)){
            $usuario->load('facturas');
            if(count($usuario->facturas) > 0){
                return response($usuario->facturas,200);
            }else{
                return response([],200);
            }
        }
    }

    public function logGoogle(Request $request){
        $usuario = Usuario::where('correo', $request->email)->first();

        if(isset($usuario)){
            $usuario->load('persona');
            $usuario->persona->load('domicilio');
            $usuario->load('rol');
            $nombre = $usuario->persona->nombre;
            $apellido = $usuario->persona->apellido;
            return response([
                'nombre' => "$nombre $apellido",
                'token' => $usuario->createToken('BuenSabor')->plainTextToken
            ], 200);
        }else{
            $usuario = Usuario::create([
                'correo' => $request->email,
                'imagen' => $request->imagen,
                'clave' => Hash::make('buensabor'),
                'persona_id' => Persona::create([
                    'nombre' => $request->nombre,
                    'apellido' => $request->apellido,
                    'telefono' => 1111111111,
                    'domicilio_id' => Domicilio::create([
                        'calle' => 'Sin Calle',
                        'numero' => 1111,
                        'localidad' => 'Sin localidad'
                    ])->id
                ])->id
            ]);
            $nombre = $request->nombre;
            $apellido = $request->apellido;
            return response([
                'nombre' => "$nombre $apellido",
                'token' => $usuario->createToken('BuenSabor')->plainTextToken
            ], 200);
        }
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

                $nombre = $usuario->persona->nombre;
                $apellido = $usuario->persona->apellido;
                return response([
                    'nombre' => "$nombre $apellido",
                    'token' => $usuario->createToken('BuenSabor')->plainTextToken
                ], 200);
            }else{
                return response([
                    'mensaje' => 'correo /contraseña incorrecta'
                ], 405);
            }
        }else{
            return response([
                'mensaje' => 'correo /contraseña incorrecta'
            ], 405);
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

                $usuario->correo = $request->correo;
                $usuario->clave = Hash::make($request->clave);
                $usuario->imagen = $request->imagen;

                $usuario->push();

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
        return response(['mensaje' => 'falta el id'], 405);
    }

    public function ver(Request $request){
        $usuario = Usuario::find($request->user()->id);
        $usuario->load('persona');
        $usuario->persona->load('domicilio');
        $usuario->load('rol');
        return response(['usuario' => $usuario],200);
    }

    public function getAllUsuarios(Request $request){
        $usuarios = Usuario::all();
        return response($usuarios,200);
    }

    public function cambiarRolUsuario(Request $request){
        $usuario = Usuario::find($request->id);
        if(isset($usuario)){
            $usuario->rol_id = $request->rol_id;
            return response('Rol cambiado con exito',200);
        }else{
            return response('Usuario no encontrado');
        }
    }

    public function getRankingComidas(Request $request)
    {
        $consulta = DB::select(
            "
        SELECT articulo_manufacturados.denominacion as Manufacturado,
        articulo_insumos.denominacion as Insumo, sum(cantidad) as cantidad from facturas
        INNER JOIN detalle_facturas ON factura_id = facturas.id
        LEFT JOIN articulo_insumos ON articulo_insumos.id = articulo_insumo_id
        LEFT JOIN articulo_manufacturados ON
        articulo_manufacturado_id = articulo_manufacturados.id
        WHERE fecha BETWEEN '$request->inicial' AND '$request->final' GROUP BY articulo_manufacturado_id,
        articulo_insumo_id ORDER BY sum(cantidad)"
        );
        if (isset($consulta)) {
            return response($consulta, 200);
        }
    }

    public function getRecaudacionesDia(Request $request){
        $consulta = DB::select("select DATE(fecha) as dia, sum(totalVenta) as Total
        from facturas WHERE fecha BETWEEN '$request->inicial' and '$request->final' GROUP BY fecha");
        if(isset($consulta)){
            return response($consulta,200);
        }
    }

    public function getRecaudacionMensual(Request $request)
    {
        $consulta = DB::select("select MONTH(fecha) as Mes, sum(totalVenta) as Total from facturas WHERE fecha BETWEEN '$request->inicial' AND '$request->final' GROUP BY MONTH(fecha);");
        if(isset($consulta)){
            return response($consulta, 200);
        }

    }

    public function getPedidosByCliente(Request $request){
        $consulta = DB::select(
            "select usuario_id as cliente_id, CONCAT(personas.nombre, ' ', personas.apellido) as nombre, count(pedidos.id) as pedidos from pedidos INNER JOIN usuarios ON usuarios.id = usuario_id INNER JOIN personas ON personas.id = usuarios.persona_id WHERE fecha BETWEEN '$request->inicial' AND '$request->final' GROUP BY usuario_id;");
        if(isset($consulta)){
            return response($consulta,200);
        }
    }

    public function getMontoGanancia(Request $request){
        $consulta = DB::select(
            "select SUM(totalVenta)-SUM(totalVenta*0.50) as Ganancias from facturas WHERE facturas.deleted_at IS NULL AND fecha BETWEEN '$request->inicio' AND '$request->final';"
        );
        if(isset($consulta)){
            return response($consulta,200);
        }
    }

    public function getRolUsuario(Request $request){
        $usuario = Auth::user()->rol_id;
        return response($usuario,200);
    }
}
