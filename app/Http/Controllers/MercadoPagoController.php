<?php

namespace App\Http\Controllers;

use App\Models\MercadoPagoDatos;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Carbon\Carbon as Carbon;
require base_path('/vendor/autoload.php');
use MercadoPago\Preference as Preference;
use MercadoPago\Item as Item;
use MercadoPago\SDK as SDK;
use Illuminate\Support\Facades\Auth;

class MercadoPagoController extends Controller
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

        $pedido = Pedido::where('usuario_id', Auth::id())
            ->whereBetween('estado', [0, 6])
            ->first();

        if(!isset($pedido->identificadorPago)){
            //MP_PUBLIC_KEY=TEST-d0b63ad6-fb0d-4b3a-9c89-a1f3ee4a7d62
            //MP_ACCESS_TOKEN=TEST-5574394918063588-062518-77996b72bada49d4a63cab4c2f8e25b3-150521903
            SDK::setAccessToken(config('services.mercadopago.token'));

            $pedido->estado = 1;
            $pedido->fecha = Carbon::now();
            $pedido->horaEstimadaFin = new \DateTime($request->horaEstimadaFin);
            $pedido->tipoEnvio = $request->tipoEnvio;
            $pedido->total = $request->total;

            $preference = new Preference();
            $preference->back_urls = array(
                "success" => "http://localhost:3000/pedido/pagado",
                "failure" => "http://localhost:3000/checkout",
                "pending" => "http://localhost:3000/checkout"
            );
            $preference->auto_return = "approved";

            $carrito = array();

            foreach ($request->carrito as $producto) {
                $item = new Item();
                $item->title = $producto['denominacion'];
                $item->quantity = $producto['cantidad'];
                $item->currency_id = "ARS";
                $item->unit_price = $producto['precioVenta'];
                array_push($carrito, $item);
            }
            $preference->items = $carrito;

            if (count($preference->items) > 0) {

                $preference->save();

                MercadoPagoDatos::create([
                    'identificadorPago' => $preference->id,
                    'fechaCreacion' => Carbon::now(),
                    'estado' => 'Pendiente'
                ]);

                $pedido->identificadorPago = $preference->id;
                $pedido->save();

                return response([
                    "link" => $preference->init_point
                ], 200);
                //return response([config('services.mercadopago.token')],200);
            }//1238040560
        }else{
            return response([
                "link" => "https://www.mercadopago.com.ar/checkout/v1/redirect?pref_id=$pedido->identificadorPago"
            ],200);
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MercadoPagoDatos  $mercadoPagoDatos
     * @return \Illuminate\Http\Response
     */
    public function show(MercadoPagoDatos $mercadoPagoDatos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MercadoPagoDatos  $mercadoPagoDatos
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MercadoPagoDatos $mercadoPagoDatos)
    {
        $pedido = Pedido::where('usuario_id', Auth::id())
            ->whereBetween('estado', [0, 1])
            ->first();

        $pedido->estado = $request->estado;
        $pedido->save();

        $mpdatos = MercadoPagoDatos::find($pedido->identificadorPago);
        $mpdatos->fechaAprobacion = Carbon::now();
        $mpdatos->formaPago = 'tarjeta';
        $mpdatos->metodoPago = 'MercadoPago Test';
        $mpdatos->nroTarjeta = $request->nroTarjeta;
        $mpdatos->estado = $request->estado;
        $mpdatos->save();
    }

    public function actualizar(Request $request)
    {
        $pedido = Pedido::where('usuario_id', Auth::id())
            ->whereBetween('estado', [0, 1])
            ->first();

        $pedido->estado = $request->estado;
        $pedido->save();

        $mpdatos = MercadoPagoDatos::find($pedido->identificadorPago);
        $mpdatos->fechaAprobacion = Carbon::now();
        $mpdatos->formaPago = 'tarjeta';
        $mpdatos->metodoPago = 'MercadoPago Test';
        $mpdatos->nroTarjeta = $request->nroTarjeta;
        $mpdatos->estado = $request->estado;
        $mpdatos->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MercadoPagoDatos  $mercadoPagoDatos
     * @return \Illuminate\Http\Response
     */
    public function destroy(MercadoPagoDatos $mercadoPagoDatos)
    {
        //
    }
}
