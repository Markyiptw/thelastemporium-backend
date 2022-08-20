<?php

use App\Http\Controllers\ObjController;
use App\Http\Controllers\ObjectLocationController;
use App\Http\Controllers\ObjectMediaController;
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

Route::resource('objects', ObjController::class)->only([
    'index',
]);

Route::resource('objects/{object}/locations', ObjectLocationController::class)->only([
    'index',
]);

Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('user')->get('/user', function (Request $request) {
        return $request->user();
    });
    Route::middleware('admin')->get('/admin', function (Request $request) {
        return $request->user();
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('objects/{object}/medias', ObjectMediaController::class)->only([
        'store',
    ]);
});
