<?php

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
// Route::post('login', [ApiController::class, 'authenticate']);
// Route::post('register', [ApiController::class, 'register']);

// Route::group(['middleware' => ['jwt.verify']], function() {
//     Route::get('logout', [ApiController::class, 'logout']);
//     Route::get('get_user', [ApiController::class, 'get_user']);
//     Route::get('products', [ProductController::class, 'index']);
//     Route::get('products/{id}', [ProductController::class, 'show']);
//     Route::post('create', [ProductController::class, 'store']);
//     Route::put('update/{product}',  [ProductController::class, 'update']);
//     Route::delete('delete/{product}',  [ProductController::class, 'destroy']);
// });
Route::get('/', function () {
    return view('welcome');
});
// Route::get('send-mail', function () {
//
////     $details = [
////         'title' => 'Mail from ItSolutionStuff.com',
////         'body' => 'This is for testing email using smtp'
////     ];
//
////     Mail::to('obararaida@gmail.com')->send(new Gmail($details));
//
////     dd("Email is Sent.");
//// });
//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
//
//// Route::post(
////     'api/charge-callback',
////     '\App\Http\Controllers\InvoiceController@charge_callback'
//// );
//Route::post('charge-callback', [InvoiceController::class, 'charge_callback']);
//Route::post('api/charge-callback', [InvoiceController::class, 'charge_callback']);
//
//Auth::routes();
