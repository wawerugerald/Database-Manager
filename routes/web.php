<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DatabaseController;

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

Route::get('/', [DatabaseController::class, 'index'])->name('dashboard');

Route::get('/instances/{id}/status', [DatabaseController::class, 'status']);
Route::post('/instances/{id}/start', [DatabaseController::class, 'start']);
Route::post('/instances/{id}/stop', [DatabaseController::class, 'stop']);
