<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route; 
use App\Http\Controllers\api\PersonControllerApi;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/people', [PersonControllerApi::class, 'index']);
Route::post('/people', [PersonControllerApi::class, 'store'])->name('api.store');
Route::get('/people/{id}', [PersonControllerApi::class, 'show'])->name('api.show');
Route::put('/people/{id}', [PersonControllerApi::class, 'update'])->name('api.update');
Route::delete('/people/{id}', [PersonControllerApi::class, 'destroy'])->name('api.destroy');
Route::get('/raffle', [PersonControllerApi::class, 'raffle'])->name('api.raffle');
Route::get('/pairs/{id}', [PersonControllerApi::class, 'showPair']);