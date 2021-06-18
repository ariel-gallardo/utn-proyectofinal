<?php

namespace App\Http\Controllers;

use App\Models\RubroArticulo;
use Illuminate\Http\Request;

class RubroArticuloController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response(
            ['rubro_articulo' => RubroArticulo::all()], 200
        );
    }

    public function indexByCategoria(Request $request){
        $ra = RubroArticulo::find($request->id);
        $ra->load('articulos');
        return response($ra,200);
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
            'denominacion' => 'required | string | unique:rubro_articulos'
        ]);

        $rubro_articulo = RubroArticulo::create([
            'denominacion' => $request->denominacion
        ]);

        return response([
            'rubro_articulo' => $rubro_articulo
        ],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RubroArticulo  $rubroArticulo
     * @return \Illuminate\Http\Response
     */
    public function show(RubroArticulo $rubroArticulo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RubroArticulo  $rubroArticulo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RubroArticulo $rubroArticulo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RubroArticulo  $rubroArticulo
     * @return \Illuminate\Http\Response
     */
    public function destroy(RubroArticulo $rubroArticulo)
    {
        //
    }
}
