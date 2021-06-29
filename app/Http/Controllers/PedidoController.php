<?php

namespace App\Http\Controllers;

use App\Mail\Correo;
use App\Models\Pedido;
use App\Models\Configuracion;
use App\Models\MercadoPagoDatos;
use App\Models\ArticuloInsumo;
use App\Models\ArticuloManufacturado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use MercadoPago;
MercadoPago\SDK::setAccessToken(config('services.mercadopago.token'));
use Illuminate\Support\Facades\Mail;

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


    public function pedidoActual (Request $request){

        if(isset($request->pcliente)){
            $pedido = Pedido::where('usuario_id', Auth::id())
                ->whereBetween('estado', [0, 7])
                ->first();
        }else{
            $pedido = Pedido::where('usuario_id', Auth::id())
                ->whereBetween('estado', [0, 6])
                ->first();
        }


        if($pedido){
            $pedido->with('detallePedidosManufacturados');
            $pedido->with('detallePedidosArticulos');

            $man = $pedido->detallePedidosManufacturados->whereNull('borrado');
            $art = $pedido-> detallePedidosArticulos->whereNull('borrado');

            $tiempoEstimado = 0;
            $totalPrecio = 0;
            $cantidad = 0;

            $configuracion = Configuracion::find(1);
            $numCocineros = $configuracion->cantidadCocineros;

            if(count($man) > 0 && count($art) > 0){
                $array = array();
                foreach($art as $a){
                    $cantidad += $a->cantidad;
                    $totalPrecio += $a->precioVenta;
                    $array[] = $a;
                }
                foreach($man as $m){
                    $cantidad += $m->cantidad;
                    $tiempoEstimado += $m->tiempoEstimadoCocina;
                    $totalPrecio += $m->precioVenta;
                    $array[] = $m;
                }
                return response([
                    'cantidad' => $cantidad,
                    'numero' => $pedido->id,
                    'carrito' => $array,
                    'total' => $totalPrecio,
                    'tiempoEstimado' => $tiempoEstimado + ($tiempoEstimado/$numCocineros),
                    'estado' => $pedido->estado,
                    'tipoEnvio' => $pedido->tipoEnvio
                ],200);
            }else if(count($man) > 0 && count($art) == 0){
                foreach ($man as $m) {
                    $cantidad += $m->cantidad;
                    $tiempoEstimado += $m->tiempoEstimadoCocina;
                    $totalPrecio += $m->precioVenta;
                    $array[] = $m;
                }
                return response([
                    'cantidad' => $cantidad,
                    'numero' => $pedido->id,
                    'carrito' => $man,
                    'total' => $totalPrecio,
                    'tiempoEstimado' => $tiempoEstimado + ($tiempoEstimado / $numCocineros),
                    'estado' => $pedido->estado,
                    'tipoEnvio' => $pedido->tipoEnvio
                ],200);
            }else if(count($man) == 0 && count($art) > 0){
                foreach ($art as $a) {
                    $cantidad += $a->cantidad;
                    $totalPrecio += $a->precioVenta;
                    $array[] = $a;
                }
                return response(
                    [
                        'cantidad' => $cantidad,
                        'numero' => $pedido->id,
                        'carrito'=>$art,
                        'total' => $totalPrecio,
                        'tiempoEstimado' => $tiempoEstimado,
                        'estado' => $pedido->estado,
                        'tipoEnvio' => $pedido->tipoEnvio
                    ]
                    ,200);
            }else{
                return response([
                    'cantidad' => 0,
                    'numero' => $pedido->id,
                    'carrito' => [],
                    'total' => 0,
                    'tiempoEstimado' => 0,
                    'estado' => $pedido->estado,
                    'tipoEnvio' => $pedido->tipoEnvio
                ],200);
            }

        }else{

            if(Auth::id() != null){
                $pedido = Pedido::create([
                    'usuario_id' => Auth::id()
                ]);
                return response([
                    'cantidad' => 0,
                    'numero' => $pedido->id,
                    'carrito' => [],
                    'total' => 0,
                    'tiempoEstimado' => 0,
                    'estado' => 0,
                ], 200);
            }
        }
    }

    public function pagarEfectivo(Request $request){
        $pedido = Pedido::find($request->numero);
        if(isset($pedido)){
            $pedido->horaEstimadaFin = $request->horaEstimadaFin;
            $pedido->tipoEnvio = $request->tipoEnvio;
            $pedido->total = $request->total;
            $pedido->estado = $request->estado;
            $pedido->save();
        }else{
            return response('Pedido no encontrado',404);
        }
    }


//CAJERO
    //Armado Pagado Cocina Cocinado Listo Busqueda Delivery Facturado
    public function getPendientes(){
        $pedidos = Pedido::where('estado',1)->get(['id','total', 'horaEstimadaFin']);
        if(isset($pedidos)){
            foreach($pedidos as $pedido){
                $pedido->load('detallePedidosManufacturados');
                $pedido->load('detallePedidosArticulos');
            }
            return response($pedidos,200);
        }else{
            return response([], 200);
        }
    }

    //Armado Pagado Cocina Cocinado Listo Busqueda Delivery Facturado
    public function getListos()
    {
        $pedidos = Pedido::where('estado', 4)->get(['id', 'total', 'horaEstimadaFin']);
        if (isset($pedidos)) {
            foreach ($pedidos as $pedido) {
                $pedido->load('detallePedidosManufacturados');
                $pedido->load('detallePedidosArticulos');

            }
            return response($pedidos, 200);
        } else {
            return response([], 200);
        }
    }

    public function setCocinero(Request $request){

        $pedido = Pedido::where('id', $request->id)->first();

        if ($pedido) {
            $pedido->estado = 2;
            $pedido->save();
            return response("pedido n° $pedido->id enviado para cocinar.", 200);
        } else {
            return response("No se encuentra el pedido n°$request->id", 404);
        }
    }

    public function setBusqueda(Request $request)
    {
        $pedido = Pedido::where('id', $request->id)->first();
        if ($pedido) {
            if($pedido->tipoEnvio == 0){
                $pedido->estado = 7;
                $pedido->save();
                return response("pedido n° $pedido->id entregado al cliente", 200);
            }else{
                $pedido->estado = 6;
                $pedido->save();
                return response("pedido n° $pedido->id entregado al delivery", 200);
            }
        } else {
            return response("No se encuentra el pedido n°$request->id", 404);
        }
    }

    public function setDelivery(Request $request)
    {
        $pedido = Pedido::where('id', $request->id)->first();
        if ($pedido) {
            $pedido->estado = 6;
            $pedido->save();
            return response("pedido n° $pedido->id entregado", 200);
        } else {
            return response("No se encuentra el pedido n°$request->id", 404);
        }
    }

    //Armado Pagado Cocina Cocinado Listo Busqueda Delivery Facturado
    public function getDeliveries()
    {
        $pedidos = Pedido::where('estado', 6)->get();
        if (isset($pedidos)) {
            return response($pedidos, 200);
        } else {
            return response([], 200);
        }
    }

    public function setEntregado(Request $request){
        $pedido = Pedido::where('id',$request->id)->first();
        if($pedido){
            $pedido->estado = 7;
            $pedido->save();

            return response("pedido n° $pedido->id entregado al cliente",200);
        }else{
            return response("No se encuentra el pedido n°$request->id",404);
        }

    }

    //Cocinero
    //Armado Pagado Cocina Cocinado Listo Busqueda Delivery Facturado
    public function getCocinar()
    {
        $pedidos = Pedido::where('estado', 2)->get(['id', 'total', 'horaEstimadaFin']);
        if (isset($pedidos)) {
            foreach ($pedidos as $pedido) {
                $pedido->load('detallePedidosManufacturados');
                $pedido->load('detallePedidosArticulos');
            }
            return response($pedidos, 200);
        } else {
            return response([], 200);
        }
    }

    //Armado Pagado Cocina Cocinado Listo Busqueda Delivery Facturado
    public function getCocinandose()
    {
        $pedidos = Pedido::where('estado', 3)->get(['id', 'total', 'horaEstimadaFin']);
        if (isset($pedidos)) {
            foreach ($pedidos as $pedido) {
                $pedido->load('detallePedidosManufacturados');
                $pedido->load('detallePedidosArticulos');
            }
            return response($pedidos, 200);
        } else {
            return response([], 200);
        }
    }

    //Armado Pagado Cocina Cocinado Listo Busqueda Delivery Facturado
    public function setPACajero(Request $request)
    {
        $pedido = Pedido::where('id', $request->id)->first();
        if ($pedido) {
            $pedido->estado = 4;
            $pedido->save();
            return response("pedido n° $pedido->id enviado al cajero", 200);
        } else {
            return response("No se encuentra el pedido n°$request->id", 404);
        }
    }

    public function consumirIngredientes($numPedido){
        $pedido = Pedido::find($numPedido);
        $pedido->load('detallePedidosManufacturados');
        $pedido->load('detallePedidosArticulos');

        if (isset($pedido->detallePedidosManufacturados)) {
            foreach ($pedido->detallePedidosManufacturados as $m) {
                $prep = (ArticuloManufacturado::find($m['articulo_manufacturado_id'])->load('ingredientes'));
                foreach($prep['ingredientes'] as $ingrediente){
                    $i = ArticuloInsumo::find($ingrediente['ids']);
                    $i['stockActual'] = $i['stockActual'] - $ingrediente['pivot']['cantidad'];
                    $i->save();
                }
            }
        }

        if (isset($pedido->detallePedidosArticulos)) {
            foreach ($pedido->detallePedidosArticulos as $a) {
                $a['stockActual'] = $a['stockActual'] - $a['cantidad'];
                $a->save();
            }
        }

        return response($pedido, 200);
    }

    //Armado Pagado Cocina Cocinado Listo Busqueda Delivery Facturado
    public function setPACocinar(Request $request)
    {
        $pedido = Pedido::where('id', $request->id)->first();
        if ($pedido) {
            $pedido->load('detallePedidosManufacturados');
            $pedido->load('detallePedidosArticulos');
            $this->consumirIngredientes($request->id);
            $pedido->estado = 3;
            $pedido->save();
            return response("pedido n° $pedido->id enviado a cocinar", 200);
        } else {
            return response("No se encuentra el pedido n°$request->id", 404);
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
        //Armado Pagado Cocina Cocinado Listo Busqueda Delivery Facturado
        $pedido = Pedido::where('usuario_id', Auth::id())
        ->whereBetween('estado', [0,1])
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

    public function crearMercadoPago(Request $request){
        $pedido = Pedido::find($request->pedido_id);
        if(isset($pedido)){
            $preference = new MercadoPago\Preference();
            $preference->items = array();

            foreach ($request->carrito as $producto) {

                $item = new MercadoPago\Item();
                $item->title = $producto->denominacion;
                $item->quantity = $producto->cantidad;
                $item->unit_price = $producto->precioVenta;
                $preference->items[] = $item;
            }

            if (count($preference->items) > 0) {
                $preference->save();
                MercadoPagoDatos::create([
                    'identificadorPago' => $preference->id
                ]);
                $pedido->identificadorPago = $preference->id;
                $pedido->save();

                return response([
                    'identificador' => $pedido->identificadorPago,
                    'mensaje' => 'Pedido generado correctamente'
                ],200);
            }
        }else{
            return response('No se encontro el pedido',405);
        }

    }

    public function enviarCorreo($numero, $correo){
        $details = [
            'title' => "Buen Sabor - pedido n° $numero",
            'numero' => $numero
        ];

        Mail::to($correo)->send(new Correo($details));
    }
}
