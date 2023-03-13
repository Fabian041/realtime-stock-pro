<?php

use App\Events\StockDataUpdated;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FgController;
use App\Http\Controllers\WipController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\MaterialMasterController;
use App\Http\Controllers\PartNumberMasterController;
use App\Http\Controllers\ProfileController;

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
    return view('layouts.login');
})->middleware('guest');

Route::middleware(['guest'])->group(function () {

    Route::get('/login', [LoginController::class, 'index'])->name('login.index');
    Route::post('/login-auth', [LoginController::class, 'authenticate'])->name('login.auth');
    Route::get('/register', [RegisterController::class, 'index'])->name('register.index');
    Route::post('/register-store', [RegisterController::class, 'store'])->name('register.store');
    
});

Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout.auth');
    
    Route::prefix('/dashboard')->group(function () {
    
        Route::get('/fg-dashboard', [FgController::class, 'index'])->name('fg.dashboard');
        Route::get('/material-dashboard', [MaterialController::class, 'index'])->name('material.dashboard');
        Route::get('/getMaterial', [MaterialController::class, 'getMaterial'])->name('material.get');
        Route::get('/wip-dashboard', [WipController::class, 'index'])->name('wip.dashboard');
        
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
        Route::post('/profile-update', [ProfileController::class, 'update'])->name('profile.update');
    });
    
    Route::prefix('/master')->group(function () {
    
        // Part Number Master
        Route::get('/part-number-master', [PartNumberMasterController::class, 'index'])->name('part-number.master');
        Route::post('/part-number-master/insertData', [PartNumberMasterController::class, 'store'])->name('part-number.master.insertData');
        Route::get('/part-number-master/getData', [PartNumberMasterController::class, 'getData'])->name('part-number.master.getData');

        // Material Master
        Route::get('/material-master', [MaterialMasterController::class, 'index'])->name('material.master');
        Route::post('/material-master/import', [MaterialMasterController::class, 'import'])->name('material.master.import');
        Route::get('/material-master/getData', [MaterialMasterController::class, 'getData'])->name('material.master.getData');
    
    });
    
});
