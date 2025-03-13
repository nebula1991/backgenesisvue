<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

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


// Red// Redirigir '/' al login o al dashboard según autenticaciónirección en "/"

// Redirección en "/"
Route::get('/', function () {
    return redirect(Auth::check() ? '/dashboard' : '/login');
});


// Cargar la vista Vue en cualquier ruta que empiece con "/vue"
Route::get('/{any?}', function () {
    return view('vue');
})->where('any', '.*');  // Acepta cualquier ruta dentro de Vue




// Route::get('/vue/{n1?}/{n2?}/{n3?}', function () {
//     return view('vue');
// });


// Rutas de autenticación
Route::post('user/login', [LoginController::class, 'authenticate']);