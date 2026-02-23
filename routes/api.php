<?php

use App\Http\Controllers\Api\DinerController;
use App\Http\Controllers\Api\DiningHallController;
use App\Http\Controllers\Api\MenuCategoryController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\WeeklyMenuBuildController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user()->load('roles.permissions');
});

Route::middleware('auth:sanctum')->apiResource('dining-halls', DiningHallController::class);
Route::middleware('auth:sanctum')->post('dining-halls/{diningHall}/diners/import', [DinerController::class, 'import']);
Route::middleware('auth:sanctum')->apiResource('dining-halls.diners', DinerController::class);
Route::middleware('auth:sanctum')->apiResource('menu-categories', MenuCategoryController::class);
Route::middleware('auth:sanctum')->apiResource('menus', MenuController::class);
Route::middleware('auth:sanctum')->put('weekly-menu-builds/{weekly_menu_build}/dining-halls', [WeeklyMenuBuildController::class, 'updateDiningHalls']);
Route::middleware('auth:sanctum')->post('weekly-menu-builds/{weekly_menu_build}/publish', [WeeklyMenuBuildController::class, 'publish']);
Route::middleware('auth:sanctum')->post('weekly-menu-builds/{weekly_menu_build}/archive', [WeeklyMenuBuildController::class, 'archive']);
Route::middleware('auth:sanctum')->apiResource('weekly-menu-builds', WeeklyMenuBuildController::class);
