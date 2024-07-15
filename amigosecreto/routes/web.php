<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PersonController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/home', [PersonController::class, 'index'])->name('home');
Route::post('/person', [PersonController::class, 'store'])->name('person.store');
Route::get('/people/{id}', [PersonController::class, 'show'])->name('web.show');
Route::put('/people/{id}', [PersonController::class, 'update'])->name('people.update');
Route::delete('/people/{id}', [PersonController::class, 'destroy'])->name('web.destroy');
Route::get('/raffles', [PersonController::class, 'raffles'])->name('web.raffles');
