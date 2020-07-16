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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true]);

Route::group(['middleware' => 'auth'], function () {

    Route::get('/home', 'HomeController@index')->name('home');
    Route::post('/createNewMonster', 'HomeController@create');
    Route::get('/canvas/{monster_id?}', 'CanvasController@index');
    Route::post('/saveImage', 'CanvasController@save');
    Route::post('/cancelImage', 'CanvasController@cancel');
    Route::post('/saveRating', 'RatingController@save');
    
});
Route::get('/gallery/{monster_id?}', 'GalleryController@index');

