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

    //Home
    Route::post('/createNewMonster', 'HomeController@create');
    Route::post('/unblockLockedMonsters', 'HomeController@update');

    //Canvas
    Route::get('/canvas/{monster_id?}', 'CanvasController@index');
    Route::post('/saveImage', 'CanvasController@save');
    Route::post('/cancelImage', 'CanvasController@cancel');

    //Ratings
    Route::post('/saveRating', 'RatingController@save');

    //Comments
    Route::get('/comments/{monsterId}', 'CommentController@index');
    Route::post('/comments', 'CommentController@store');
    Route::post('/comments/{commentId}/{type}', 'CommentController@update');

    //My monsters
    Route::get('/mymonsters/{page?}/{filter?}', 'MyMonstersController@index');

    //Gallery
    Route::post('/flagMonster', 'GalleryController@update');
    Route::post('/rollback', 'GalleryController@update');
});
Route::get('/nonauth/home', 'NonAuthHomeController@index')->name('home');
Route::post('/nonauth/createNewMonster', 'NonAuthHomeController@create');
Route::get('/nonauth/canvas/{monster_id?}', 'NonAuthCanvasController@index');
Route::post('/nonauth/saveImage', 'NonAuthCanvasController@save');
Route::post('/nonauth/cancelImage', 'NonAuthCanvasController@cancel');


Route::get('/gallery/{monster_id?}', 'GalleryController@index');
Route::get('/halloffame/{page?}/{filter?}', 'HallOfFameController@index');




