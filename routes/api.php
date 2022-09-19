<?php

use App\Http\Controllers\MediaController;
use App\Http\Controllers\ObjController;
use App\Http\Controllers\ObjectDraftController;
use App\Http\Controllers\ObjectLocationController;
use App\Http\Controllers\ObjectMailController;
use App\Http\Controllers\ObjectMediaController;
use App\Http\Controllers\PostController;
use App\Http\Resources\ObjResource;
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

Route::resource('medias', MediaController::class)->only([
    'index',
]);

Route::resource('objects/{object}/locations', ObjectLocationController::class)->only([
    'index',
]);

Route::resource('objects/{object}/medias', ObjectMediaController::class)->only([
    'index',
]);

Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('user')->get('/user', function (Request $request) {
        return $request->user();
    });
    Route::middleware('admin')->get('/admin', function (Request $request) {
        return $request->user();
    });
    Route::resource('objects/{object}/medias', ObjectMediaController::class)->only([
        'store',
    ]);
    Route::resource('objects/{object}/locations', ObjectLocationController::class)->only([
        'store',
    ]);
    Route::middleware('admin')->resource('objects', ObjController::class)->only([
        'store',
    ]);
    Route::middleware('user')->get('/object', function (Request $request) {
        return new ObjResource($request->user()->object);
    });
    Route::resource('objects.mails', ObjectMailController::class)
        ->only(['store', 'index', 'show'])
        ->middleware('can:object-specific-action,object')
        ->scoped();

    Route::resource('objects.drafts', ObjectDraftController::class)
        ->only(['store']);

    Route::resource('posts', PostController::class)
        ->only(['index', 'show']);

    Route::resource('posts', PostController::class)
        ->middleware('admin')
        ->only(['store', 'update', 'destroy']);
});
