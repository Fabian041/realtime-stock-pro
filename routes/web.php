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
    return view('layouts.auth.login');
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
    
        // finsih good dashboard
        Route::get('/fg/dc', [FgController::class, 'fgDc'])->name('fg.dc');
        Route::get('/fg/ma', [FgController::class, 'fgMa'])->name('fg.ma');
        Route::get('/fg/assy', [FgController::class, 'fgAssy'])->name('fg.assy');

        // wip dashboard
        Route::get('/wip/dc', [WipController::class, 'wipDc'])->name('wip.dc');
        Route::get('/wip/ma', [WipController::class, 'wipMa'])->name('wip.ma');
        Route::get('/wip/assy', [WipController::class, 'wipAssy'])->name('wip.assy');

        // material dashboard
        Route::get('/material/ppic', [MaterialController::class, 'materialPpic'])->name('material.ppic');
        Route::get('/material/dc', [MaterialController::class, 'materialDc'])->name('material.dc');
        Route::get('/material/ma', [MaterialController::class, 'materialMa'])->name('material.ma');
        Route::get('/material/assy', [MaterialController::class, 'materialAssy'])->name('material.assy');

        Route::get('/getMaterial', [MaterialController::class, 'getMaterial'])->name('material.get');

        // get finsih good part
        Route::get('/getFgPart/ma', [FgController::class, 'getPartMa'])->name('fgMa.get');
        Route::get('/getFgPart/dc', [FgController::class, 'getPartDc'])->name('fgDc.get');
        Route::get('/getFgPart/assy', [FgController::class, 'getPartAssy'])->name('fgAssy.get');

        // get wip part
        Route::get('/getWipPart/ma', [WipController::class, 'getPartMa'])->name('wipMa.get');
        Route::get('/getWipPart/dc', [WipController::class, 'getPartDc'])->name('wipDc.get');
        Route::get('/getWipPart/assy', [WipController::class, 'getPartAssy'])->name('wipAssy.get');
        
        // checkout proccess
        Route::get('/checkout', [MaterialController::class, 'checkout'])->name('checkout.index');
        Route::get('/checkout/getData', [MaterialController::class, 'getDataCheckout'])->name('checkout.getData');
        Route::post('/checkout/store', [MaterialController::class, 'checkoutStore'])->name('checkout.store');

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