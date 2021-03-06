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

// Route::get('/', function () {
//     return view('welcome',);
// })->name('index');

// Route::get('/', function () {
//     return view('welcome');
// })->name('/');
Route::get('/', 'WelcomeController@index')->name('index');
Route::get('/', 'WelcomeController@index')->name('/');

Auth::routes(['verify' => true]);

//Authorised
Route::group(['middleware' => ['auth','restrictIp', 'TrackLastActiveAt']], function () {
    Route::get('/home', 'HomeController@index')->name('index');

    //Home
    Route::post('/createNewMonster', 'HomeController@create')->name('createNewMonster');
    Route::post('/unblockLockedMonsters', 'HomeController@update')->name('unblockLockedMonsters');
    Route::post('/createMonsterPngs', 'HomeController@update')->name('createMonsterPngs');
    Route::post('/createMissingThumbnails', 'HomeController@update')->name('createMissingThumbnails');
    Route::post('/closeInfoMessage', 'HomeController@update')->name('closeInfoMessage');
    Route::get('/fetchMonsters', 'HomeController@fetchMonsters')->name('fetchMonsters');
    Route::post('/fetchRandomMonster', 'HomeController@fetchRandomMonster')->name('fetchRandomMonster');
    Route::post('/awardTrophies', 'HomeController@update')->name('awardTrophies');
    Route::post('/awardWeeklyTrophies', 'HomeController@awardWeeklyTrophies')->name('awardWeeklyTrophies');
    Route::get('/trophies', 'TrophiesController@index')->name('trophies');
    Route::get('/getNewUserChanges', 'HomeController@getNewUserChanges')->name('getNewUserChanges');
    Route::post('/removeOldB64Images', 'HomeController@update')->name('removeOldB64Images');
    Route::post('/convertB64Images', 'HomeController@update')->name('convertB64Images');
    Route::post('/setHasUsedApp', 'HomeController@update')->name('setHasUsedApp');
    Route::post('/exitGroup', 'HomeController@update')->name('exitGroup');
    Route::post('/refreshStats', 'HomeController@update')->name('refreshStats');
    
    //Notifications
    Route::post('/updateNotificationsLastViewed', 'NotificationsController@update')->name('updateNotificationsLastViewed');
    Route::post('/closeNotification', 'NotificationsController@update')->name('closeNotification');

    //Canvas
    Route::get('/canvas/{monster_id?}', 'CanvasController@index')->name('canvas');
    Route::post('/saveImage', 'CanvasController@save')->name('saveImage');
    Route::post('/salvageImage', 'CanvasController@salvage')->name('salvageImage');
    Route::post('/cancelImage', 'CanvasController@cancel')->name('cancelImage');
    Route::post('/reviveImage', 'CanvasController@update')->name('reviveImage');
    Route::post('/updateName', 'CanvasController@update')->name('updateName');
    Route::post('/updateIsNSFW', 'CanvasController@update')->name('updateIsNSFW');
    Route::post('/updateLevel', 'CanvasController@update')->name('updateLevel');
    Route::post('/peekActivated', 'CanvasController@update')->name('peekActivated');
    Route::post('/canvas/updateIdleTimer', 'CanvasController@update')->name('updateIdleTimer');
    Route::post('/canvas/sendBirthAnnouncement', 'CanvasController@update')->name('sendBirthAnnouncement');

    //Ratings
    Route::post('/saveRating', 'RatingController@save')->name('saveRating');
    Route::post('/cancelRating', 'RatingController@delete')->name('cancelRating');

    //Comments
    Route::post('/comments', 'CommentController@store')->name('comments');
    Route::post('/comments/{commentId}/{type}', 'CommentController@update')->name('updateComment');

    //monsters
    Route::post('/monsters/gildUser', 'MyMonstersController@update')->name('gildUser');
    Route::post('/monsters/ungildUser', 'MyMonstersController@update')->name('ungildUser');
    Route::post('/monsters/monitorUser', 'MyMonstersController@update')->name('monitorUser');
    Route::post('/monsters/unmonitorUser', 'MyMonstersController@update')->name('unmonitorUser');
    Route::post('/monsters/followUser', 'MyMonstersController@update')->name('followUser');
    Route::post('/monsters/unfollowUser', 'MyMonstersController@update')->name('unfollowUser');
    
    //Gallery
    Route::post('/flagMonster', 'GalleryController@update')->name('flagMonster');
    Route::post('/validateMonster', 'GalleryController@update')->name('validateMonster');
    Route::post('/abortMonster', 'GalleryController@update')->name('abortMonster');
    Route::post('/rollback', 'GalleryController@update')->name('rollbackMonster');
    Route::post('/suggestRollback', 'GalleryController@update')->name('suggestRollbackMonster');
    Route::post('/takeTwo', 'GalleryController@update')->name('takeTwo');
    Route::post('/requestTakeTwo', 'GalleryController@update')->name('requestTakeTwo');
    Route::post('/rejectTakeTwo', 'GalleryController@update')->name('rejectTakeTwo');
    Route::post('/addTag', 'GalleryController@update')->name('addTag');
    Route::post('/removeTag', 'GalleryController@update')->name('removeTag');
    
    Route::post('/addFavourite', 'GalleryController@update')->name('addFavourite');
    Route::post('/removeFavourite', 'GalleryController@update')->name('removeFavourite');

    //Private Groups
    Route::get('/privategroups', 'GroupMaintenanceController@index')->name('privateGroups');
    Route::post('/privategroups/create', 'GroupMaintenanceController@create')->name('createPrivateGroup');
    Route::post('/privategroups/entergroup', 'GroupController@index')->name('enterPrivateGroup');

    //Settings
    Route::post('/updateNSFW', 'SettingsController@update')->name('updateNSFW');
    Route::get('/settings', 'SettingsController@index')->name('settings');
    Route::post('/settings/save', 'SettingsController@save')->name('settings');
    Route::post('/setProfilePic', 'SettingsController@update')->name('settings');
    Route::post('/unsetProfilePic', 'SettingsController@update')->name('settings');
    Route::post('/saveToRedis', 'SettingsController@update')->name('saveToRedis');
    Route::post('/fetchFromRedis', 'SettingsController@update')->name('fetchFromRedis');
    Route::post('/flushRedis', 'SettingsController@update')->name('flushRedis');

    //Book
    Route::get('/book/build/{groupId?}/{bookId?}', 'BookController@index')->name('buildBook');
    Route::post('/book/save', 'BookController@save')->name('saveBook');
    Route::get('/book/preview/{bookId}', 'BookPreviewController@index')->name('previewBook');
    Route::post('/book/update', 'BookPreviewController@update')->name('updateBook');

    //Payments
    Route::post('/stripe/create-checkout-session', 'OrderController@index')->name('createCheckoutSession');
    Route::get('/stripe/payment/{result?}/{order_id?}/{item_id?}', 'OrderController@completed')->name('paymentCompleted');
    
    //Random Words
    Route::get('/randomwords', 'RandomWordsController@index')->name('randomwords');
    Route::post('/randomwords/create', 'RandomWordsController@create')->name('createrandomword');
    Route::post('/randomwords/delete', 'RandomWordsController@delete')->name('deleterandomword');
    
    //Social Media Accounts
    Route::post('/socialMediaAccounts/update', 'SocialMediaAccountsController@update')->name('updateSocialMediaAccounts');
});

//Non authorised
Route::group(['middleware' => 'restrictIp'], function () {
    Route::get('/nonauth/home', 'NonAuthHomeController@index')->name('home');
    Route::post('/nonauth/createNewMonster', 'NonAuthHomeController@create')->name('nonAuthCreateNewMonster');
    Route::get('/nonauth/canvas/{monster_id?}', 'NonAuthCanvasController@index')->name('nonAuthCanvas');
    Route::post('/nonauth/saveImage', 'NonAuthCanvasController@save')->name('nonAuthSaveImage');
    Route::post('/nonauth/salvageImage', 'NonAuthCanvasController@salvage')->name('nonAuthSalvageImage');
    Route::get('/nonauth/fetchMonsters', 'NonAuthHomeController@fetchMonsters')->name('nonAuthFetchMonsters');
    Route::get('/getDailyActionCount', 'HomeController@getDailyActionCount')->name('GetDailyActionCount');
    Route::post('/nonauth/cancelImage', 'NonAuthCanvasController@cancel')->name('cancelImage');
    Route::post('/nonauth/reviveImage', 'NonAuthCanvasController@update')->name('reviveImage');
    Route::post('/nonauth/canvas/updateIdleTimer', 'NonAuthCanvasController@update')->name('updateIdleTimer');
    Route::post('/monstergrid/getData', 'MonsterGridController@getData')->name('getGalleryMonsters');
    
    Route::get('/comments/{monsterId}', 'CommentController@index')->name('nonAuthComments');
    Route::post('/gallery/searchbytag', 'GalleryController@update')->name('nonAuthGallery');
    Route::get('/gallery/{monster_id?}', 'GalleryController@index')->name('nonAuthGallery');
    
    Route::get('/monstergrid/{pageType?}/{userId?}/{search?}', 'MonsterGridController@index')->name('nonAuthMonsterGrid');
    Route::get('/halloffame/{page?}/{filter?}/{search?}', 'HallOfFameController@index')->name('hallOfFame');
    Route::get('/halloffamesingle/{skip?}/{filter?}/{search?}', 'HallOfFameSingleController@index')->name('hallOfFameSingle');
    Route::get('/about', 'AboutController@index')->name('about');
    Route::get('/eula', 'EULAController@index')->name('eula');
    Route::get('/privacy', 'PrivacyController@index')->name('privacy');
    Route::get('/tshirtguide', 'TShirtGuideController@index')->name('tshirtguide');
    Route::get('/mobileapp', 'MobileAppController@index')->name('mobileapp');
    Route::get('/findUserByName/{search?}', 'GalleryController@findUserByName')->name('findUserByName');
    Route::get('/favourites/{userId}/{page?}/{filter?}/{search?}', 'FavouritesController@index')->name('myFavourites');
    Route::get('/monsters/{userId}/{page?}/{filter?}/{search?}', 'MyMonstersController@index')->name('myMonsters');

    //Tagging game
    Route::get('/taggame', 'TagGameController@index')->name('taggame');
    Route::post('/taggame/savesubmission', 'TagGameController@update')->name('savesubmission');
    Route::post('/taggame/savescore', 'TagGameController@update')->name('saveScore');
    Route::post('/taggame/saveskip', 'TagGameController@update')->name('saveSkip');

    Route::post('/nonauth/entergroup', 'GroupController@index')->name('enterGroup');
    Route::post('/resetsession', function(){
        Session::flush();   
    })->name('resetSession');

    //Tshirt
    Route::get('/tshirt/build/{monsterId?}', 'TShirtController@index')->name('startTshirtOrder');
    //Route::get('/tshirt/build/{monsterId?}', 'MerchController@index')->name('startMerchOrder');
    Route::post('/tshirt/save', 'TShirtController@save')->name('saveTshirtOrder');
    Route::get('/tshirt/preview/{bookId}', 'TShirtController@index')->name('previewTshirtOrder');
    Route::post('/tshirt/update', 'TShirtController@update')->name('updateTshirtOrder');
});