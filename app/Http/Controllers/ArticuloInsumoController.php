<?php

namespace App\Http\Controllers;

use App\Models\ArticuloInsumo;
use Illuminate\Http\Request;

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
            [$request]
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ArticuloInsumo  $articuloInsumo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ArticuloInsumo $articuloInsumo)
    {
        //
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
}
