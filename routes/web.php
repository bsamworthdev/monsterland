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
})->name('index');

Route::get('/', function () {
    return view('welcome');
})->name('/');

Auth::routes(['verify' => true]);

Route::group(['middleware' => 'auth'], function () {
    Route::get('/home', 'HomeController@index')->name('index');

    //Home
    Route::post('/createNewMonster', 'HomeController@create')->name('createNewMonster');
    Route::post('/unblockLockedMonsters', 'HomeController@update')->name('unblockLockedMonsters');
    Route::post('/createMonsterPngs', 'HomeController@update')->name('createMonsterPngs');
    Route::post('/closeInfoMessage', 'HomeController@update')->name('closeInfoMessage');
    Route::get('/fetchMonsters', 'HomeController@fetchMonsters')->name('closeInfoMessage');
    Route::post('/awardTrophies', 'HomeController@update')->name('awardTrophies');
    Route::get('/trophies', 'TrophiesController@index')->name('trophies');
    Route::get('/getNewUserChanges', 'HomeController@getNewUserChanges')->name('getNewUserChanges');

    //Canvas
    Route::get('/canvas/{monster_id?}', 'CanvasController@index')->name('canvas');
    Route::post('/saveImage', 'CanvasController@save')->name('saveImage');
    Route::post('/cancelImage', 'CanvasController@cancel')->name('cancelImage');

    //Ratings
    Route::post('/saveRating', 'RatingController@save')->name('saveRating');

    //Comments
    Route::post('/comments', 'CommentController@store')->name('comments');
    Route::post('/comments/{commentId}/{type}', 'CommentController@update')->name('updateComment');

    //monsters
    Route::get('/monsters/{userId}/{page?}/{filter?}/{search?}', 'MyMonstersController@index')->name('myMonsters');
    Route::post('/monsters/gildUser', 'MyMonstersController@update')->name('gildUser');

    //Gallery
    Route::post('/flagMonster', 'GalleryController@update')->name('flagMonster');
    Route::post('/abortMonster', 'GalleryController@update')->name('abortMonster');
    Route::post('/rollback', 'GalleryController@update')->name('rollbackMonster');
    Route::post('/suggestRollback', 'GalleryController@update')->name('suggestRollbackMonster');

    //Private Groups
    Route::get('/privategroups', 'GroupMaintenanceController@index')->name('privateGroups');
    Route::post('/privategroups/create', 'GroupMaintenanceController@create')->name('createPrivateGroup');

    //Settings
    Route::post('/updateNSFW', 'SettingsController@update')->name('updateNSFW');
    Route::get('/settings', 'SettingsController@index')->name('settings');
    Route::post('/settings/save', 'SettingsController@save')->name('settings');

    //Book
    Route::get('/book/build/{groupId?}/{bookId?}', 'BookController@index')->name('buildBook');
    Route::post('/book/save', 'BookController@save')->name('saveBook');
    Route::get('/book/preview/{bookId}', 'BookPreviewController@index')->name('previewBook');
    Route::post('/book/update', 'BookPreviewController@update')->name('updateBook');

    //Payments
    Route::post('/stripe/create-checkout-session', 'OrderController@index')->name('createCheckoutSession');
    Route::get('/stripe/payment/{result?}/{order_id?}/{book_id?}', 'OrderController@completed')->name('paymentCompleted');
    
});
Route::get('/nonauth/home', 'NonAuthHomeController@index')->name('home');
Route::post('/nonauth/createNewMonster', 'NonAuthHomeController@create')->name('nonAuthCreateNewMonster');
Route::get('/nonauth/canvas/{monster_id?}', 'NonAuthCanvasController@index')->name('nonAuthCanvas');
Route::post('/nonauth/saveImage', 'NonAuthCanvasController@save')->name('nonAuthSaveImage');
Route::post('/nonauth/cancelImage', 'NonAuthCanvasController@cancel')->name('nonAuthCancelImage');
Route::get('/nonauth/fetchMonsters', 'NonAuthHomeController@fetchMonsters')->name('nonAuthFetchMonsters');

Route::get('/comments/{monsterId}', 'CommentController@index')->name('nonAuthComments');
Route::get('/gallery/{monster_id?}', 'GalleryController@index')->name('nonAuthGallery');
Route::get('/halloffame/{page?}/{filter?}/{search?}', 'HallOfFameController@index')->name('hallOfFame');
Route::get('/halloffamesingle/{skip?}/{filter?}/{search?}', 'HallOfFameSingleController@index')->name('hallOfFameSingle');
Route::get('/about', 'AboutController@index')->name('about');

Route::post('/nonauth/entergroup', 'GroupController@index')->name('enterGroup');
Route::post('/resetsession', function(){
    Session::flush();   
})->name('resetSession');


