<?php

namespace App\Http\Controllers;

use App\Models\ArticuloManufacturado;
use Illuminate\Http\Request;

class ArticuloManufacturadoController extends Controller
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

    public function ingredientes(Request $request){
        if($request->trashed){
            $aM = ArticuloManufacturado::withTrashed()->where('id',$request->id)->first();
            if($aM){
                $aM->load('ingredientesTrashed');
                return response($aM,200);
            }else{
                return response('no se encontro el articulo manufacturado',405);
            }
        }else{
            $aM = ArticuloManufacturado::find($request->id);
            if($aM){
                $aM->load('ingredientes');
                return response($aM,200);
            }else{
                return response('no se encontro el articulo manufacturado',405);
            }
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
        $request->validate([
            'denominacion' => 'required | string | unique:articulo_manufacturados',
            'tiempoEstimadoCocina' => 'required | numeric',
            'precioVenta' => 'required | numeric',
            'rubro_generals_id' => 'required',
            'imagen' => 'sometimes| base64image'
        ]);

        ArticuloManufacturado::create([
            'denominacion' => $request->denominacion,
            'tiempoEstimadoCocina' => $request->tiempoEstimadoCocina,
            'precioVenta' => $request->precioVenta,
            'imagen' => $request->imagen,
            'rubro_generals_id' => $request->rubro_generals_id
        ]);

        return response(
            "el articulo $request->denominacion fue creado exitosamente.",
            200
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ArticuloManufacturado  $articuloManufacturado
     * @return \Illuminate\Http\Response
     */
    public function show(ArticuloManufacturado $articuloManufacturado)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ArticuloManufacturado  $articuloManufacturado
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ArticuloManufacturado $articuloManufacturado)
    {
        $request->validate([
            'denominacion' => 'required | string | unique:articulo_manufacturados',
            'tiempoEstimadoCocina' => 'required | numeric',
            'precioVenta' => 'required | numeric',
            'rubro_generals_id' => 'required',
            'imagen' => 'sometimes| base64image'
        ]);

        $articuloInsumo = ArticuloManufacturado::withTrashed()->where('id', $request->id)->first();
        if (isset($articuloInsumo)) {
            $articuloInsumo->denominacion = $request->denominacion;
            $articuloInsumo->precioVenta = $request->precioVenta;
            $articuloInsumo->tiempoEstimadoCocina = $request->tiempoEstimadoCocina;
            $articuloInsumo->imagen = $request->imagen;
            $articuloInsumo->rubro_generals_id = $request->rubro_generals_id;
            $articuloInsumo->save();
            return response("Articulo $articuloInsumo->denominacion modificado satisfactoriamente", 200);
        } else {
            return response("No se encontro el articulo para modificar", 405);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ArticuloManufacturado  $articuloManufacturado
     * @return \Illuminate\Http\Response
     */
    public function destroy(ArticuloManufacturado $articuloManufacturado)
    {
        //
    }

    public function destroyDeleted($id)
    {
        $aI = ArticuloManufacturado::withTrashed()->where('id', $id)->first();
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
