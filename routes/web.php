<?php

use App\Image;


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
    $images = Image::where('processed', true)
        ->orderBy('created_at', 'desc')
        ->get();

    return view('welcome', ['images' => $images]);
})->name('home');

Route::get('/images', 'ImageController@index');

Route::post('/image', 'ImageController@store');
