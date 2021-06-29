<?php

namespace App\Http\Controllers;

use App\Models\ArticuloInsumo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArticuloInsumoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(
            [ArticuloInsumo::all()], 200
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'denominacion' => 'required | string | unique:articulo_insumos',
            'precioCompra' => 'required | numeric',
            'precioVenta' => 'required | numeric',
            'stockActual' => 'required | integer',
            'stockMinimo' => 'required | integer',
            'unidadMedida' => 'required | string',
            'esInsumo' => 'required | boolean',
            'rubro_articulos_id' => 'required'
        ]);

        ArticuloInsumo::create([
            'denominacion' => $request->denominacion,
            'precioCompra' => $request->precioCompra,
            'precioVenta' => $request->precioVenta,
            'stockActual' => $request->stockActual,
            'stockMinimo' => $request->stockMinimo,
            'unidadMedida' => $request->unidadMedida,
            'esInsumo' => $request->esInsumo,
            'rubro_articulos_id' => $request->rubro_articulos_id
        ]);


        return response(
            "el articulo $request->denominacion fue creado exitosamente."
            ,200
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ArticuloInsumo  $articuloInsumo
     * @return \Illuminate\Http\Response
     */
    public function show(ArticuloInsumo $articuloInsumo)
    {
        //
    }

    public function consStock(Request $request){
        $a = ArticuloInsumo::find($request->id);
        if(isset($a)){
            if($a->stockActual - $request->cantidad > 0){
                return response([
                    'mensaje' => "",
                    'resultado' => true
                ]);
            }else{
                return response([
                    'mensaje' => "No hay suficiente stock de $a->denominacion",
                    'resultado' => false
                ]);
            }
        }else{
            return response('No se encuentra el articuo', 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ArticuloInsumo  $articuloInsumo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ArticuloInsumo $articuloInsumo)
    {
        $request->validate([
            'denominacion' => 'required | string | unique:articulo_insumos',
            'precioCompra' => 'required | numeric',
            'precioVenta' => 'required | numeric',
            'stockActual' => 'required | integer',
            'stockMinimo' => 'required | integer',
            'unidadMedida' => 'required | string',
            'esInsumo' => 'required | boolean',
            'rubro_articulos_id' => 'required'
        ]);

        $articuloInsumo = ArticuloInsumo::withTrashed()->where('id',$request->id)->first();
        if(isset($articuloInsumo)){
            $articuloInsumo->denominacion = $request->denominacion;
            $articuloInsumo->precioCompra = $request->precioCompra;
            $articuloInsumo->precioVenta = $request->precioVenta;
            $articuloInsumo->stockActual = $request->stockActual;
            $articuloInsumo->stockMinimo = $request->stockMinimo;
            $articuloInsumo->unidadMedida = $request->unidadMedida;
            $articuloInsumo->esInsumo = $request->esInsumo;
            $articuloInsumo->rubro_articulos_id = $request->rubro_articulos_id;
            $articuloInsumo->save();
            return response("Articulo $articuloInsumo->denominacion modificado satisfactoriamente",200);
        }else{
            return response("No se encontro el articulo para modificar", 405);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ArticuloInsumo  $articuloInsumo
     * @return \Illuminate\Http\Response
     */
    public function destroy(ArticuloInsumo $articuloInsumo)
    {
        //
    }


    public function destroyDeleted($id)
    {
        $aI = ArticuloInsumo::withTrashed()->where('id', $id)->first();
        if (isset($aI)) {
            if ($aI->deleted_at === null) {
                $aI->deleted_at = new \DateTime('NOW');
                $aI->save();
                return response('borrado exitosamente', 200);
            } else {
                $aI->deleted_at = null;
                $aI->save();
                return response('restaurado exitosamente', 200);
            }
        } else {
            return response('no se encontro el insumo', 405);
        }
    }
}
