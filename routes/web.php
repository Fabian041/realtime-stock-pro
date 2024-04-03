<?php

use App\Events\StockDataUpdated;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FgController;
use App\Http\Controllers\WipController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\BomMasterController;
use App\Http\Controllers\MaterialMasterController;
use App\Http\Controllers\PartNumberMasterController;
use App\Http\Controllers\TransactionMasterController;

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

    Route::get('/getBackNumber', [StockController::class,'getBackNumber'])->name('getBackNumber');
    Route::get('/getCurrentStock', [StockController::class,'getCurrentStock'])->name('getCurrentStock');

    Route::get('/periodStock/{area}', [StockController::class,'periodStock'])->name('period.stock');

    Route::prefix('/dashboard')->group(function () {

        // finsih good dashboard
        Route::get('/fg/dc', [FgController::class, 'fgDc'])->name('fg.dc');
        Route::get('/fg/dc/getTransaction', [FgController::class, 'fgDcGetTransaction'])->name('dc.getTransaction');

        Route::get('/fg/ma', [FgController::class, 'fgMa'])->name('fg.ma');
        Route::get('/fg/ma/getTransaction', [FgController::class, 'fgMaGetTransaction'])->name('ma.getTransaction');

        Route::get('/fg/assy', [FgController::class, 'fgAssy'])->name('fg.assy');
        Route::get('/fg/assy/getTransaction', [FgController::class, 'fgAssyGetTransaction'])->name('assy.getTransaction');
        
        // wip dashboard
        Route::get('/wip/dc', [WipController::class, 'wipDc'])->name('wip.dc');
        Route::get('/wip/ma', [WipController::class, 'wipMa'])->name('wip.ma');
        Route::get('/wip/ma/getTransaction', [WipController::class, 'wipMaGetTransaction'])->name('wipMa.getTransaction');

        Route::get('/wip/assy', [WipController::class, 'wipAssy'])->name('wip.assy');

        // material dashboard
        Route::get('/material/wh', [MaterialController::class, 'materialWh'])->name('material.wh');
        Route::get('/material/oh', [MaterialController::class, 'materialOh'])->name('material.oh');
        Route::get('/material/dc', [MaterialController::class, 'materialDc'])->name('material.dc');
        Route::get('/material/ma', [MaterialController::class, 'materialMa'])->name('material.ma');
        Route::get('/material/assy', [MaterialController::class, 'materialAssy'])->name('material.assy');

        Route::prefix('/material-transaction')->group(function () {

            // stock balancings
            Route::get('/stockBalancing', [StockController::class, 'stockBalancing'])->name('stockBalancing.index');
            Route::post('/stockBalancing/adjust', [StockController::class, 'adjustStock'])->name('stockBalancing.adjust');

            // Material transaction
            Route::get('/wh', [MaterialController::class, 'indexWh'])->name('wh.index');
            Route::post('/wh/import', [MaterialController::class, 'import'])->name('wh.import');
            Route::get('/wh/scan', [MaterialController::class, 'scanWh'])->name('wh.scan');
            Route::get('/wh/getData', [MaterialController::class, 'getDataWh'])->name('wh.getData');

            // Material transaction
            Route::get('/oh', [MaterialController::class, 'indexOh'])->name('oh.index');
            Route::post('/oh/unbox', [MaterialController::class, 'unboxOh'])->name('oh.unbox');
            Route::get('/oh/scan', [MaterialController::class, 'scanOh'])->name('oh.scan');
            Route::get('/oh/getData', [MaterialController::class, 'getDataOh'])->name('oh.getData');

            // ng part
            Route::get('/ng', [MaterialController::class, 'indexNg'])->name('ng.index');
            Route::post('/ng/store', [MaterialController::class, 'storeNg'])->name('ng.store');
            Route::get('/ng/scan', [MaterialController::class, 'scanNg'])->name('ng.scan');
            Route::get('/ng/getData', [MaterialController::class, 'getDataNg'])->name('ng.getData');


            // checkout proccess
            Route::get('/checkout', [MaterialController::class, 'checkout'])->name('checkout.index');
            Route::get('/checkout/scan', [MaterialController::class, 'scanProd'])->name('checkout.scan');
            Route::post('/checkout/store', [MaterialController::class, 'checkoutStore'])->name('checkout.store');
            Route::get('/checkout/getData', [MaterialController::class, 'getDataCheckout'])->name('checkout.getData');
        });

        // Material Dashboard
        Route::get('/getWhMaterial', [MaterialController::class, 'getWhMaterial'])->name('material.getWh');
        Route::get('/getOhMaterial', [MaterialController::class, 'getOhMaterial'])->name('material.getOh');
        Route::get('/getDcMaterial', [MaterialController::class, 'getDcMaterial'])->name('material.getDc');
        Route::get('/getMaMaterial', [MaterialController::class, 'getMaMaterial'])->name('material.getMa');
        Route::get('/getAssyMaterial', [MaterialController::class, 'getAssyMaterial'])->name('material.getAssy');

        // get finsih good part
        Route::get('/getFgPart/ma', [FgController::class, 'getFgMaStock'])->name('fgMa.get');
        Route::get('/getFgPart/dc', [FgController::class, 'getFgDcStock'])->name('fgDc.get');
        Route::get('/getFgPart/assy', [FgController::class, 'getFgAssyStock'])->name('fgAssy.get');

        // get wip part
        Route::get('/getWipPart/ma', [WipController::class, 'getWipMaStock'])->name('wipMa.get');
        Route::get('/getWipPart/dc', [WipController::class, 'getWipDcStock'])->name('wipDc.get');
        Route::get('/getWipPart/assy', [WipController::class, 'getPartAssy'])->name('wipAssy.get');

        Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
        Route::post('/profile-update', [ProfileController::class, 'update'])->name('profile.update');
    });

    Route::prefix('/master')->group(function () {

        // Part Number Master
        Route::get('/part-number-master', [PartNumberMasterController::class, 'index'])->name('part-number.master');
        Route::post('/part-number-master/insertData', [PartNumberMasterController::class, 'store'])->name('part-number.master.insertData');
        Route::get('/part-number-master/getData', [PartNumberMasterController::class, 'getData'])->name('part-number.master.getData');
        Route::post('/part-number-master/update/{part}', [PartNumberMasterController::class, 'update'])->name('part-number.master.update');

        // Material Master
        Route::get('/material-master', [MaterialMasterController::class, 'index'])->name('material.master');
        Route::post('/material-master/import', [MaterialMasterController::class, 'import'])->name('material.master.import');
        Route::post('/material-master/update/{material}', [MaterialMasterController::class, 'update'])->name('material.master.update');
        Route::get('/material-master/getData', [MaterialMasterController::class, 'getData'])->name('material.master.getData');

        // Transaction Master
        Route::get('/transaction-master', [TransactionMasterController::class, 'index'])->name('transaction.master');
        Route::post('/transaction-master/import', [TransactionMasterController::class, 'store'])->name('transaction.master.insertData');
        Route::get('/transaction-master/getData', [TransactionMasterController::class, 'getData'])->name('transaction.master.getData');

        // BOM Master
        Route::get('/bom-master', [BomMasterController::class, 'index'])->name('bom.master');
        Route::post('/bom-master/store', [BomMasterController::class, 'store'])->name('bom.master.insertData');
        Route::post('/bom-master/import', [BomMasterController::class, 'import'])->name('bom.master.import');
        Route::get('/bom-master/getData', [BomMasterController::class, 'getData'])->name('bom.master.getData');
        Route::post('/bom-master/update/{bom}', [BomMasterController::class, 'update'])->name('bom.master.update');
    });
});
