<?php

namespace App\Http\Controllers;

use App\Models\RubroArticulo;
use Illuminate\Http\Request;

class RubroController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return RubroArticulo::all();
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
            'denominacion' => 'string | required | unique:rubro_articulos',
            'rubro_articulo_id' => 'sometimes | numeric | required'
        ]);

        $padre = null;

        if(isset($request->rubro_articulo_id)){
            $padre = RubroArticulo::find($request->rubro_articulo_id);
            if(!isset($padre)){
                $error = \Illuminate\Validation\ValidationException::withMessages([
                    'rubro_articulo_id' => ['categoria padre no existe.'],
                ]);
                throw $error;
            }
        }

        RubroArticulo::create(
            [
                'denominacion' => $request->denominacion,
                'rubro_articulo_id' => isset($padre) ? $padre->id : null
            ]
        );
        return response(['mensaje'=> "$request->denominacion creado".(isset($padre) ? " en $padre->denominacion." : ".")],201);
    }


    public function show($id)
    {
        $rubroArticulo = RubroArticulo::find($id);
        if(isset($rubroArticulo)){
            return response([
                'rubroArticulo' => $rubroArticulo
            ], 200);
        }
        return response([
            'mensaje' => 'no se encontro la categoria'
        ],405);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RubroArticulo  $rubroArticulo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rubroArticulo = RubroArticulo::find($id);

        $request->validate([
            'denominacion' => 'sometimes | string | required | unique:rubro_articulos',
            'rubro_articulo_id' => 'sometimes | numeric | required'
        ]);

        if(isset($request->denominacion) || isset($request->rubro_articulo_id)){
            $padre = null;

            if (isset($request->rubro_articulo_id)) {
                if($request->rubro_articulo_id > 0 && $rubroArticulo->id != $request->rubro_articulo_id){
                    $padre = RubroArticulo::find($request->rubro_articulo_id);
                }

                if (!isset($padre) && $request->rubro_articulo_id > 0) {
                    $error = \Illuminate\Validation\ValidationException::withMessages([
                        'rubro_articulo_id' => ['categoria padre no existe.'],
                    ]);
                    throw $error;
                }
            }
            $rubroArticulo->update(
                [
                    'denominacion' => isset($request->denominacion) ? $request->denominacion : $rubroArticulo->denominacion,
                    'rubro_articulo_id' => isset($padre) ? $padre->id : ($request->rubro_articulo_id > 0 ? $rubroArticulo->id : null)
                ]
            );
            return response(['mensaje' => "$rubroArticulo->denominacion actualizado."], 200);
        }
        return response(['mensaje' => "completar."], 405);
    }

    public function destroy($id)
    {
        $rubroArticulo = RubroArticulo::find($id);
        if(isset($rubroArticulo)){
            $rubroArticulo->load('rubroArticulos');
            $rubroArticulo->rubroArticulos()->delete();
            $rubroArticulo->delete();
            return response([
                'mensaje' => 'categoria eliminada'
            ], 200);
        }
        return response([
            'mensaje' => 'No existe la categoria seleccionada'
        ],405);
    }
}
