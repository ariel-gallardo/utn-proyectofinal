<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $config = Configuracion::find(1);
        if(isset($config)){
            return response($config,200);
        }else{
            return response('No existe configuracion',404);
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
        $config = Configuracion::find(1);

        if($config == null){
            $config = Configuracion::create([
                'cantidadCocineros' =>  $request->cantidadCocineros,
                'emailEmpresa' => $request->emailEmpresa,
                'tokenMercadoPago' => $request->tokenMercadoPago
            ]);
            return response($config, 200);
        }else{
            return response($config, 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Configuracion  $configuracion
     * @return \Illuminate\Http\Response
     */
    public function show(Configuracion $configuracion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Configuracion  $configuracion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Configuracion $configuracion)
    {
        $config = Configuracion::find(1);
        if(isset($config)){
            $config->cantidadCocineros = $request->cantidadCocineros;
            $config->save();
            return response('Guardado correctamente', 200);
        }else{
            return response('No se encuentra', 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Configuracion  $configuracion
     * @return \Illuminate\Http\Response
     */
    public function destroy(Configuracion $configuracion)
    {
        //
    }
}
