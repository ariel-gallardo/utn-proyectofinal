<?php

namespace App\Http\Controllers;

use App\Models\DetallePedido;
use Illuminate\Http\Request;

class DetallePedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $dP = DetallePedido::create([
            'articulo_insumo_id' => $request->articulo_insumo_id,
            'articulo_manufacturado_id' => $request->articulo_manufacturado_id,
            'cantidad' => $request->cantidad,
            'pedido_id' => $request->pedido_id,
            'subtotal' => $request->subtotal
        ]);
        return response($dP->cantidad, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DetallePedido  $detallePedido
     * @return \Illuminate\Http\Response
     */
    public function show(DetallePedido $detallePedido)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        $detallePedido = null;

        if ($request->articulo_manufacturado_id !== null) {

            $detallePedido = DetallePedido::where(
                'articulo_manufacturado_id',
                $request->articulo_manufacturado_id
            )
            ->where('pedido_id', $request->pedido_id)
            ->first();

        } else if ($request->articulo_insumo_id !== null) {

            $detallePedido = DetallePedido::where(
                'articulo_insumo_id',
                $request->articulo_insumo_id
            )
            ->where('pedido_id', $request->pedido_id)
            ->first();

        }

        if ($detallePedido !== null) {
            $detallePedido->cantidad = $request->cantidad;
            $detallePedido->subtotal = $request->subtotal;
            $detallePedido->save();
            return response($detallePedido->subtotal, 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DetallePedido  $detallePedido
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $detallePedido = null;

        if ($request->articulo_manufacturado_id !== null) {

            $detallePedido = DetallePedido::where(
                'articulo_manufacturado_id',
                $request->articulo_manufacturado_id
            )
                ->where('pedido_id', $request->pedido_id)
                ->first();

        } else if ($request->articulo_insumo_id !== null) {

            $detallePedido = DetallePedido::where(
                'articulo_insumo_id',
                $request->articulo_insumo_id
            )
                ->where('pedido_id', $request->pedido_id)
                ->first();
        }

        if($detallePedido !== null){

            $detallePedido->delete();
            return response('Borrado',200);

        }else{

            return response('No encontrado', 405);

        }

    }
}
