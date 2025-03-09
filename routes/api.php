<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\LoginController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\SubcategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::post('user/login', [LoginController::class, 'authenticate']);





Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::get('categories/all',[ CategoryController::class,'all'] ); 
   Route::get('subcategories/all',[ SubcategoryController::class,'all'] );
 Route::get('products/all',[ ProductController::class,'all'] );

Route::post('products/upload/{product}',[ProductController::class, 'upload']);


Route::get('suppliers/all',[ SupplierController::class,'all'] );
Route::resource('suppliers', SupplierController::class)->except('create','edit');


Route::get('events/all',[ EventController::class,'all'] );
Route::resource('events', EventController::class)->except('create','edit');


Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::resource('categories', CategoryController::class)->except('create','edit');
    
    Route::resource('subcategories', SubcategoryController::class)->except('create','edit');
 
    Route::resource('products', ProductController::class)->except('create','edit');
   
   
});


Route::post('vue/login', [UserController::class, 'login']);

Route::post('token-check',[UserController::class, 'checkToken']);

Route::post('logout', [UserController::class, 'logout'])->middleware('auth:sanctum');