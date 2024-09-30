<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/data', [ApiController::class, 'getData']);

Route::post('/data', [ApiController::class, 'postData']);

Route::get('/admin/professors', [ApiController::class, 'professorsGet']);
Route::get('/admin/professors/{id}/show', [ApiController::class, 'professorGet']);
Route::post('/admin/tags', [ApiController::class, 'tagsPost']);
Route::post('/admin/professors/filter', [ApiController::class, 'professorsFilter']);



//Route::post('/auth/register', [AuthController::class, 'register']);
//Route::post('/auth/login', [AuthController::class, 'login']);

//Route::middleware('auth.api')->group(function () {
//    Route::post('/protected-route', [UserController::class, 'protectedAction']);
//});

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);


    Route::get('user', [AuthController::class, 'me']);
//    Route::post('refresh', 'AuthController@refresh');
//    Route::post('me', 'AuthController@me');

});
