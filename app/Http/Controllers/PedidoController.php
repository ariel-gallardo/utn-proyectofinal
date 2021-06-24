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

    public function pedidoActual (){
        $pedido = Pedido::where('usuario_id', Auth::id())
                    ->whereBetween('estado', [0, 6])
                    ->first();
        if($pedido){
            $pedido->with('detallePedidosManufacturados');
            $pedido->with('detallePedidosArticulos');

            $man = $pedido->detallePedidosManufacturados->whereNull('borrado');
            $art = $pedido-> detallePedidosArticulos->whereNull('borrado');


            if(count($man) > 0 && count($art) > 0){
                $array = array();
                foreach($art as $a){
                    $array[] = $a;
                }
                foreach($man as $m){
                    $array[] = $m;
                }
                return response($array,200);
            }else if(count($man) > 0 && count($art) == 0){
                return response($man,200);
            }else if(count($man) == 0 && count($art) > 0){
                return response($art,200);
            }else{
                return response([],200);
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
