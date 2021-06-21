<?php

use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RubroArticuloController;
use App\Http\Controllers\ArticuloInsumoController;
use App\Http\Controllers\RubroGeneralController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('usuario/registrarse',[UsuarioController::class, 'registrar']);
Route::post('usuario/loguearse', [UsuarioController::class, 'loguear']);

Route::resource('r_articulo', RubroArticuloController::class, ['except' => ['store', 'update', 'destroy']]);
Route::resource('r_generals', RubroGeneralController::class, ['except' => ['store', 'update', 'destroy']]);
Route::post('r_articulo/hijo',[RubroArticuloController::class, 'indexByPadre']);

Route::post('r_insumo/articulos', [RubroArticuloController::class, 'articulosByCategoria']);
Route::resource('r_insumo', ArticuloInsumoController::class, ['except' => ['store', 'update', 'destroy']]);


Route::middleware(['auth:sanctum'])->group(function () {
    Route::delete('usuario/desloguearse', [UsuarioController::class, 'desloguear']);
    Route::delete('usuario/borrar',[UsuarioController::class, 'borrar']);
    Route::put('usuario/modificar',[UsuarioController::class, 'modificar']);
    Route::post('usuario/datos',[UsuarioController::class, 'ver']);
});

Route::middleware(['auth:sanctum', 'cocinero'])->group(function () {
    Route::resource('r_articulo',RubroArticuloController::class, ['except' => ['index', 'show', 'articulosByCategoria']]);
    Route::resource('r_insumo',ArticuloInsumoController::class);
    Route::resource('r_generals', RubroGeneralController::class);
});

Route::middleware(['auth:sanctum'], 'administrador')->group(
    function(){
        Route::post('r_articulo/borrado',[RubroArticuloController::class, 'indexTrashed']);
        Route::delete('r_articulo/destroyDeleted/{id}', [RubroArticuloController::class, 'destroyDeleted']);
        Route::post('r_articulo/hijo/borrado', [RubroArticuloController::class, 'indexByPadreTrashed']);
        Route::post('r_insumo/articulos/borrado', [RubroArticuloController::class, 'articulosByCategoriaTrashed']);
        Route::delete('r_insumo/destroyDeleted/{id}', [ArticuloInsumoController::class, 'destroyDeleted']);

        Route::post('r_generals/borrado', [RubroGeneralController::class, 'indexTrashed']);
        Route::delete('r_generals/destroyDeleted/{id}', [RubroGeneralController::class, 'destroyTrashed']);
    }
);
