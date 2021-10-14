<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('erro', function () {
    response(['msg'=> "solicitação invalida"], 404);
})->name('api.erro');

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('cpflogin', 'AuthController@loginCpf')->name('api.logincpf');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::post('register', 'AuthController@register');

    Route::post('reunioes', 'AuthController@reunioes');

    //Endpoints para o modulo dos pés
    Route::post('pes/registrosemanal', 'Api\PesController@registroSemanal');
});

