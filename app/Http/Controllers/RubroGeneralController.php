<?php

namespace App\Http\Controllers;

use App\Models\RubroGeneral;
use Illuminate\Http\Request;

class RubroGeneralController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(
            [
                'rubro_general' => RubroGeneral::all()
            ],
            200
        );
    }

    public function indexTrashed()
    {
        return response(
            [
                'rubro_general' => RubroGeneral::withTrashed()->get()
            ],
            200
        );
    }

    public function articulosByCategoria(Request $request)
    {
        $ra = RubroGeneral::find($request->id);
        $ra->load('articulos');
        if (isset($ra)) {
            return response($ra, 200);
        } else {
            return response([], 200);
        }
    }

    public function articulosByCategoriaTrashed(Request $request)
    {
        $ra = RubroGeneral::withTrashed()->where('id', $request->id)->first();
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
            'denominacion' => 'required | string | unique:rubro_generals'
        ]);

        $rubro_generals = RubroGeneral::create([
            'denominacion' => $request->denominacion
        ]);

        return response("Categoria $request->denominacion creada correctamente.", 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RubroGeneral  $rubroGeneral
     * @return \Illuminate\Http\Response
     */
    public function show(RubroGeneral $rubroGeneral)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RubroGeneral  $rubroGeneral
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $rA = RubroGeneral::where('id',$request->id)->first();

        if (isset($rA)) {
            $rA->denominacion = $request->denominacion;
            $rA->save();
            return response('Editado correctamente', 200);
        } else {
            return response([
                'rubro_generals no encontrado'
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RubroGeneral  $rubroGeneral
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rA = RubroGeneral::find($id);
        if (isset($rA)) {
            RubroGeneral::find($id)->delete();
            return response('borrado exitosamente', 200);
        } else {
            return response('no se encontro el rubro general', 200);
        }
    }

    public function destroyTrashed($id)
    {
        $rA = RubroGeneral::withTrashed()->where('id', $id)->first();
        if (isset($rA)) {
            if ($rA->deleted_at === null) {
                $rA->deleted_at = new \DateTime('NOW');
                $rA->save();
                return response('borrado exitosamente', 200);
            } else {
                $rA->deleted_at = null;
                $rA->save();
                return response('restaurado exitosamente', 200);
            }
        } else {
            return response('no se encontro el rubro general', 405);
        }
    }
}
