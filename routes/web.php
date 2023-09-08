<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\DocumentController;

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

Route::get('/', function () {
    // return view('welcome');
});

Route::get('config/revokeAccess', [ConfigController::class, "revokeAccess"])->name('config.revokeAccess');
Route::get('config/regenerateToken', [ConfigController::class, "regenerateToken"])->name('config.regenerateToken');
Route::get('config/login', [ConfigController::class, "authGoogle"])->name('config.login');
Route::get('test', [DocumentController::class, "testFile"]);
Route::resource('config', ConfigController::class);
Route::resource('document', DocumentController::class);