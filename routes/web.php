<?php

use App\Http\Controllers\Admin\OrdreCrudController;
use App\Http\Controllers\FilesController;
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

    return redirect('/CID');
});

Route::get('get-file', [FilesController::class,'getFile']); //return Attachements list
Route::get('download/{path}', [FilesController::class,'download']); //return download  link of file
Route::post('regeter', [FilesController::class,'rejeter']); //regter un ordre
