<?php

use App\Http\Controllers\StockController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/stock-control/{line}/{code}/{qty}', [StockController::class,'stock_control'])->name('stock-control.main');
Route::get('/ng-part/{line}/{code}', [StockController::class,'ng_part'])->name('ng-part.main');
