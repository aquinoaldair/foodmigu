<?php

use App\Http\Controllers\AuthController;
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
    return view('landing');
});

Route::get('/c', fn () => redirect('/app/c'));
Route::get('/c/{path}', fn ($path) => redirect('/app/c/' . $path))->where('path', '.+');

Route::get('/app{any?}', function () {
    return view('app');
})->where('any', '.*');

// SPA Authentication (cookie-based with Sanctum)
Route::get('/login', fn () => redirect('/app/login'))->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');
