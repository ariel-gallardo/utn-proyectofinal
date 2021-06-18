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
            [
                'rubro_articulo' => RubroArticulo::all()
            ], 200
        );
    }

    public function indexTrashed(Request $request){
        return response(
            [
                'rubro_articulo' => RubroArticulo::withTrashed()->get()
            ],200
        );
    }

    public function indexByPadreTrashed(Request $request)
    {
        $subCategorias = RubroArticulo::withTrashed()
        ->where('id',$request->id)
        ->with('subRubroArticulos')->get();
        if (isset($subCategorias)) {
            return response($subCategorias[0],200);
        }
        return response(
            'no se encontro el rubro padre',
            404
        );
    }

    public function indexByPadre(Request $request){
        $subCategorias = RubroArticulo::find($request->id);
        if(isset($subCategorias)){
            $subCategorias->load('subRubroArticulos');
            if(isset($subCategorias->subRubroArticulos)){
                return response(
                    $subCategorias->subRubroArticulos,
                    200
                );
            }else{
                return response(
                    [],
                    200
                );
            }
        }
        return response(
            'no se encontro el rubro padre',
            404
        );
    }

    public function articulosByCategoria(Request $request){
        $ra = RubroArticulo::find($request->id);
        $ra->load('articulos');
        if(isset($ra)){
            return response($ra, 200);
        }else{
            return response([], 200);
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
            'denominacion' => 'required | string | unique:rubro_articulos'
        ]);

        if($request->rubro_articulo_id > 0){
            $rA = RubroArticulo::find($request->rubro_articulo_id);

            if(isset($rA)){
                $rubro_articulo = RubroArticulo::create([
                    'denominacion' => $request->denominacion,
                    'rubro_articulo_id' => $request->rubro_articulo_id
                ]);
                return response([
                    'rubro_articulo' => $rubro_articulo
                ], 200);
            }else{
                return response([
                    'No se encontro el padre'
                ], 405);
            }

        }else{
            $rubro_articulo = RubroArticulo::create([
                'denominacion' => $request->denominacion
            ]);
            return response([
                'rubro_articulo' => $rubro_articulo
            ], 200);
        }
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
        $rA = RubroArticulo::find($request->id);

        if(isset($rA)){
            $rA->denominacion = $request->denominacion;
            $rA->save();
            return response([
                'rubro_articulo' => $rA
            ], 200);
        }else{
            return response([
                'rubro_articulo no encontrado'
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RubroArticulo  $rubroArticulo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rA = RubroArticulo::find($id);
        if(isset($rA)){
            RubroArticulo::find($id)->delete();
            return response('borrado exitosamente', 200);
        }else{
            return response('no se encontro el rubro articulo', 200);
        }

    }

    public function destroyDeleted($id){
        $rA = RubroArticulo::withTrashed()->where('id',$id)->first();
        if (isset($rA)) {
            if($rA->deleted_at === null){
                $rA->deleted_at = new \DateTime('NOW');
                $rA->save();
                return response('borrado exitosamente', 200);
            }else{
                $rA->deleted_at = null;
                $rA->save();
                return response('restaurado exitosamente', 200);
            }
        } else {
            return response('no se encontro el rubro articulo', 405);
        }
    }
}
