<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    public function getDatosPersona(){
        $pedido = Pedido::where('usuario_id', Auth::id())
            ->whereBetween('estado', [0, 6])
            ->first();

        if(isset($pedido)){
            $pedido->with('cliente');
            $pedido->cliente->load('persona');
            $pedido->cliente->persona->load('domicilio');
            return response($pedido,200);
        }else{
            return response('No se encuentra algun pedido para ese usuario',404);
        }

    }

    public function pedidoActual (){
        $pedido = Pedido::where('usuario_id', Auth::id())
                    ->whereBetween('estado', [0, 6])
                    ->first();
        if($pedido){
            $pedido->with('detallePedidosManufacturados');
            $pedido->with('detallePedidosArticulos');

            $man = $pedido->detallePedidosManufacturados->whereNull('borrado');
            $art = $pedido-> detallePedidosArticulos->whereNull('borrado');

            $tiempoEstimado = 0;
            $totalPrecio = 0;

            if(count($man) > 0 && count($art) > 0){
                $array = array();
                foreach($art as $a){
                    $totalPrecio += $a->precioVenta;
                    $array[] = $a;
                }
                foreach($man as $m){
                    $tiempoEstimado += $m->tiempoEstimadoCocina;
                    $totalPrecio += $m->precioVenta;
                    $array[] = $m;
                }
                return response([
                    'numero' => $pedido->id,
                    'carrito' => $array,
                    'total' => $totalPrecio,
                    'tiempoEstimado' => $tiempoEstimado
                ],200);
            }else if(count($man) > 0 && count($art) == 0){
                return response([
                    'numero' => $pedido->id,
                    'carrito' => $man,
                    'total' => $totalPrecio,
                    'tiempoEstimado' => $tiempoEstimado
                ],200);
            }else if(count($man) == 0 && count($art) > 0){
                return response(
                    [
                        'numero' => $pedido->id,
                        'carrito'=>$art,
                        'total' => $totalPrecio,
                        'tiempoEstimado' => $tiempoEstimado
                    ]
                    ,200);
            }else{
                return response([
                    'numero' => $pedido->id,
                    'carrito' => [],
                    'total' => 0,
                    'tiempoEstimado' => 0
                ],200);
            }

        }else{
            return response('No hay un pedido abierto',405);
        }
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Armado Cajero Cocina Cocinado Listo Busqueda Delivery Facturado
        $pedido = Pedido::where('usuario_id', Auth::id())
        ->whereBetween('estado', [0,6])
        ->first();

        if(isset($pedido)){
            return response($pedido->id,200);
        }else{
            $pedido = Pedido::create([
                'usuario_id' => Auth::id()
            ]);
            return response($pedido->id, 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pedido  $pedido
     * @return \Illuminate\Http\Response
     */
    public function show(Pedido $pedido)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pedido  $pedido
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pedido $pedido)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pedido  $pedido
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pedido $pedido)
    {
        //
    }
}
