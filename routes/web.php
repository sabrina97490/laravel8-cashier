<?php

//use GuzzleHttp\Psr7\Request;
use Illuminate\Http\Request;
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
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/subscribe', function () {
    // Pour vérifer que les informations sont correctement passées
    // On utilise dd()
   //dd(auth()->user()->createSetupIntent());
    return view('subscribe', [
        'intent' => auth()->user()->createSetupIntent(),
    ]);
})->middleware(['auth', 'verified'])->name('subscribe');

Route::post('/subscribe', function (Request $request) {
    //dd($request->all());
    auth()->user()->newSubscription(
        'cashier', $request->plan
    )->create($request->paymentMethod);
    
})->middleware(['auth', 'verified'])->name('subscribe');

require __DIR__.'/auth.php';
