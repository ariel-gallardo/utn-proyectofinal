<?php

use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RubroArticuloController;
use App\Http\Controllers\ArticuloInsumoController;
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
Route::get('r_insumo/articulos', [RubroArticuloController::class, 'indexByCategoria']);
Route::resource('r_insumo', ArticuloInsumoController::class, ['except' => ['store', 'update', 'destroy']]);


Route::middleware(['auth:sanctum'])->group(function () {
    Route::delete('usuario/desloguearse', [UsuarioController::class, 'desloguear']);
    Route::delete('usuario/borrar',[UsuarioController::class, 'borrar']);
    Route::put('usuario/modificar',[UsuarioController::class, 'modificar']);
    Route::post('usuario/datos',[UsuarioController::class, 'ver']);
});

Route::middleware(['auth:sanctum', 'cocinero'])->group(function () {
    Route::resource('r_articulo',RubroArticuloController::class, ['except' => ['index', 'show']]);
    Route::resource('r_insumo',ArticuloInsumoController::class, ['except' => ['index', 'show']]);
});
