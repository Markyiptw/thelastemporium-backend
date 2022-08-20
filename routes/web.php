<?php

use App\Http\Controllers\HigherOrderLoginController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest:web')->post('/login', App::make(HigherOrderLoginController::class)->authenticate('web'));
Route::middleware('guest:admin')->post('/admin/login', App::make(HigherOrderLoginController::class)->authenticate('admin'));
