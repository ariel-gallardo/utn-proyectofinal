<?php

namespace App\Http\Controllers;

use App\Models\ArticuloManufacturado;
use App\Models\ArticuloManufacturadoDetalle;
use Illuminate\Http\Request;

class AMDController extends Controller
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

    /*public function indexbyManufacturado(Request $request){
        if($request->trashed){
            $AMD = ArticuloManufacturadoDetalle::withTrashed()->where('articulo_manufacturado_id',$request->id)->get();
            if(isset($AMD)){
                $AMD->load('getIngredienteTrashed');
                return response(
                    $AMD,
                    200
                );
            }
            return response('No se encontraron ingredientes para ese articulo',405);
        }else{
            $AMD = ArticuloManufacturadoDetalle::where('articulo_manufacturado_id',$request->id)->get();
            if(isset($AMD)){
                $AMD->load('getIngrediente');
                return response(
                    $AMD,
                    200
                );
            }
            return response('No se encontraron ingredientes para ese articulo', 405);
        }
    }*/

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    /*public function encontrar(Request $request){

        $trashed = true;
        if($trashed){
            $AMD = ArticuloManufacturadoDetalle::withTrashed()
                ->where('articulo_insumo_id', $request->articulo_insumo_id)
                ->where('articulo_manufacturado_id', $request->articulo_manufacturado_id)
                ->first();

            return response($AMD, 200);
        }
    }*/


    public function store(Request $request)
    {
        $request->validate([
            'cantidad' => 'required | numeric',
            'unidadMedida' => 'required | string',
            'articulo_insumo_id' => 'required',
            'articulo_manufacturado_id' => 'required',
        ]);

        $AMD = null;

        if($request->trashed){
            $AMD = ArticuloManufacturadoDetalle::where('articulo_insumo_id', $request->articulo_insumo_id)
                ->where('articulo_manufacturado_id', $request->articulo_manufacturado_id)
                ->first();
        }else{
            $AMD = ArticuloManufacturadoDetalle::withTrashed()
                ->where('articulo_insumo_id', $request->articulo_insumo_id)
                ->where('articulo_manufacturado_id', $request->articulo_manufacturado_id)
                ->first();
        }


        if($AMD === null){
            /*
            if ($request->trashed) {
                $this->updateTrashed($request, true);
            } else {
                $this->updateTrashed($request);
            }
*/
            ArticuloManufacturadoDetalle::create([
                'cantidad' => $request->cantidad,
                'unidadMedida' => $request->unidadMedida,
                'articulo_insumo_id' => $request->articulo_insumo_id,
                'articulo_manufacturado_id' => $request->articulo_manufacturado_id
            ]);
            return response('Ingrediente agregado con exito', 200);

        }
        return response('Error ya se encuentra este ingrediente en el producto',405);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ArticuloManufacturadoDetalle  $articuloManufacturadoDetalle
     * @return \Illuminate\Http\Response
     */
    public function show($aiID,$amID)
    {
        $AMD = ArticuloManufacturadoDetalle::where('articulo_insumo_id', $aiID)
        ->where('articulo_manufacturado_id', $amID)
        ->first();
        if(isset($AMD)){
            $AMD->load('getIngrediente');
            return response($AMD, 200);
        }else{
            return response('No se encontro el ingrediente',404);
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ArticuloManufacturadoDetalle  $articuloManufacturadoDetalle
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request)
    {
        $request->validate([
            'cantidad' => 'required | numeric',
            'articulo_insumo_id' => 'required',
            'articulo_manufacturado_id' => 'required',
        ]);

        $AMD = null;

        if($request->trashed === false){
            $AMD = ArticuloManufacturadoDetalle::where('articulo_insumo_id', $request->articulo_insumo_id)
                ->where('articulo_manufacturado_id', $request->articulo_manufacturado_id)
                ->first();
        }else{
            $AMD = ArticuloManufacturadoDetalle::withTrashed()
                ->where('articulo_insumo_id', $request->articulo_insumo_id)
                ->where('articulo_manufacturado_id', $request->articulo_manufacturado_id)
                ->first();
        }


        if(isset($AMD)){
            $AMD->cantidad = $request->cantidad;
            $AMD->articulo_insumo_id = $request->articulo_insumo_id;
            $AMD->articulo_manufacturado_id = $request->articulo_manufacturado_id;
            $AMD->save();
            return response('Modificado satisfactoriamente', 200);
        }else{
            return response('No se encontro el articulo para modificar', 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ArticuloManufacturadoDetalle  $articuloManufacturadoDetalle
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $AMD = ArticuloManufacturadoDetalle::find($id);
        if(isset($AMD)){
            $AMD->delete($id);
            return response('Ingrediente eliminado satisfactoriamente',200);
        }else{
            return response('No se encuentra el ingrediente', 405);
        }

    }

    public function destroyDeleted($id)
    {
        $AMD = ArticuloManufacturadoDetalle::withTrashed()->where('id',$id)->first();
        if (isset($AMD)) {
            if($AMD->deleted_at == null){
                $AMD->delete($id);
                return response('Ingrediente eliminado satisfactoriamente', 200);
            }else{
                $AMD->deleted_at = null;
                $AMD->save();
                return response('Ingrediente restaurado satisfactoriamente', 200);
            }

        } else {
            return response('No se encuentra el ingrediente', 405);
        }
    }
}
