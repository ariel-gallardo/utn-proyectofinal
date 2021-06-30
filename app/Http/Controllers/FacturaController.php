<?php

namespace App\Http\Controllers;

use Carbon\Carbon as Carbon;
use App\Models\ArticuloInsumo;
use App\Models\ArticuloManufacturado;
use App\Models\Pedido;
use App\Models\DetalleFactura;
use App\Models\Factura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\Correo;

class FacturaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getFacturas(Request $request){
        $facturas = Factura::all();
        return response($facturas, 200);
    }

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
        $f = Factura::where('pedido_id', $request->pedido_id)->first();

        if($f == null){
            $factura = Factura::create([
                'montoDescuento' => $request->montoDescuento,
                'formaPago' => $request->formaPago,
                'nroTarjeta' => $request->nroTarjeta,
                'totalVenta' => $request->totalVenta,
                'pedido_id' => $request->pedido_id
            ]);


            foreach ($request->carrito as $producto) {

                DetalleFactura::create([
                    'articulo_insumo_id' => isset($producto['articulo_insumo_id']) ? $producto['articulo_insumo_id'] : null,
                    'articulo_manufacturado_id' => isset($producto['articulo_manufacturado_id']) ? $producto['articulo_manufacturado_id'] : null,
                    'cantidad' => $producto['cantidad'],
                    'subtotal' => $producto['subtotal'],
                    'factura_id' => $factura->id
                ]);
            }

            $pedido = Pedido::where('id', $request->pedido_id)->first();
            $pedido->with('cliente');

            $this->enviarCorreo($factura->id, $pedido->cliente->correo);
            return response([
                'factura_id' => $factura->id
            ], 200);
        }else{
            return response([
                'factura_id' => $f->id
            ], 200);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Factura  $factura
     * @return \Illuminate\Http\Response
     */
    public function show(Factura $factura)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Factura  $factura
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Factura $factura)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Factura  $factura
     * @return \Illuminate\Http\Response
     */
    public function destroy(Factura $factura)
    {
        //
    }

    public function getFactura(Request $request){
        $factura = Factura::find($request->id);
        if($factura){
            $factura->load("detallesFactura");
            $factura->load('pedido');
            $factura->pedido->load('cliente');
            $factura->pedido->cliente->load('persona');
            $factura->pedido->cliente->persona->load('domicilio');

            $nombre = $factura->pedido->cliente->persona->nombre.' '.$factura->pedido->cliente->persona->apellido;

            $productos = array();

            foreach($factura->detallesFactura as $producto){
                if(isset($producto['articulo_insumo_id'])){
                    $art = ArticuloInsumo::find($producto['articulo_insumo_id']);
                    array_push($productos,[
                        'denominacion' => $art['denominacion'],
                        'cantidad' => $producto['cantidad'],
                        'subtotal' => $producto['subtotal']
                    ]);
                }else{
                    $art = ArticuloManufacturado::find($producto['articulo_manufacturado_id']);
                    array_push($productos, [
                        'denominacion' => $art['denominacion'],
                        'cantidad' => $producto['cantidad'],
                        'subtotal' => $producto['subtotal']
                    ]);
                }
            }

            return response([
                'id' => $factura->id,
                'nombre' =>  $nombre,
                'telefono' => $factura->pedido->cliente->persona->telefono,
                'domicilio' => $factura->pedido->cliente->persona->domicilio,
                'detalles_factura' => $productos,
                'total' => $factura->totalVenta,
                'descuento' => $factura->montoDescuento,
                'fechaEntrega' => $factura->fecha,
                'fechaEstimada' => $factura->pedido->horaEstimadaFin,
                'formaPago' => $factura->formaPago
            ],200);
        }else{
            return response('No se encontro la factura', 404);
        }

    }

    public function borrarFactura(Request $request){
        $factura = Factura::where('id',$request->id)->first();
        if(isset($factura)){
            $factura->deleted_at = Carbon::now();
            $factura->save();
            return response('Factura eliminada',200);
        }else{
            return response('No se encuentra',404);
        }
    }

    public function enviarCorreo($numero, $correo)
    {
        $details = [
            'title' => "Buen Sabor - Factura n° $numero",
            'numero' => $numero
        ];

        Mail::to($correo)->send(new Correo($details));
    }
}
