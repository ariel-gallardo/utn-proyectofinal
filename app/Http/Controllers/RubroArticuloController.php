<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
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
        $ra = RubroArticulo::whereNull('rubro_articulo_id')->get();

        if(isset($ra)){
            return response(
                $ra
                , 200
            );
        }else{
            return response(
                []
                , 200
            );
        }

    }

    public function indexCliente(Request $request)
    {
        $ra = RubroArticulo::whereNull('rubro_articulo_id')
        ->where('visiblecliente',true)->get();

        if (isset($ra)) {
            return response(
                $ra,
                200
            );
        } else {
            return response(
                [],
                200
            );
        }
    }

    public function indexTrashed(Request $request){
        return response(
            [
                'rubro_articulo' => RubroArticulo::withTrashed()->whereNull('rubro_articulo_id')->get()
            ],200
        );
    }

    public function indexByPadreTrashed(Request $request)
    {
        $subCategorias = RubroArticulo::withTrashed()
        ->where('id',$request->id)
        ->with('subRubroArticulosTrashed')->get();
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

    public function articulosByCategoriaTrashed(Request $request)
    {
        $ra = RubroArticulo::withTrashed()->where('id',$request->id)->first();
        $ra->load('articulosTrashed');
        if (isset($ra)) {
            return response($ra, 200);
        } else {
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

        if(isset($request->rubro_articulo_id)){
            $rA = RubroArticulo::find($request->rubro_articulo_id);

            if(isset($rA)){
                $rubro_articulo = RubroArticulo::create([
                    'denominacion' => $request->denominacion,
                    'rubro_articulo_id' => $request->rubro_articulo_id
                ]);
                return response('Rubro creado correctamente', 200);
            }else{
                return response('No se encontro el rubro padre', 405);
            }

        }else{
            $rubro_articulo = RubroArticulo::create([
                'denominacion' => $request->denominacion
            ]);
            return response('rubro creado correctamente', 200);
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
            return response('editado correctamente', 200);
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

    public function changeVisibleCliente(Request $request){
        $rA = RubroArticulo::withTrashed()->where('id',$request->id)->first();
        if(isset($rA)){
            if ($rA->visiblecliente == true) {
                $rA->visiblecliente = false;
                $rA->save();
                return response("$rA->denominacion no visible para cliente.", 200);
            } else {
                $rA->visiblecliente = true;
                $rA->save();
                return response("$rA->denominacion es visible para cliente.", 200);
            }
        }else{
            return response('no se encontro el rubro articulo', 405);
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
