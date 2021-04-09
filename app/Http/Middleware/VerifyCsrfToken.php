<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
        '/createNewMonster',
        '/updateNSFW',
        '/closeInfoMessage',
        '/resetsession',
        '/login',
        '/fetchMonsters',
        '/nonauth/fetchMonsters',
        '/getDailyActionCount',
        '/fetchRandomMonster',
        '/privategroups/create',
        '/stripe/*',
        '/monsters/gildUser',
        '/monsters/ungildUser',
        '/monsters/monitorUser',
        '/monsters/unmonitorUser',
        '/monsters/followUser',
        '/monsters/unfollowUser',
        '/getNewUserChanges',
        '/updateNotificationsLastViewed',
        '/closeNotification',
        '/randomwords/create',
        '/randomwords/delete',
        '/setHasUsedApp',
        '/peekActivated',
        '/cancelImage',
        '/reviveImage',
        '/nonauth/cancelImage',
        '/saveImage',
        '/nonauth/saveImage',
        '/findUserByName',
        '/addFavourite',
        '/removeFavourite',
        '/awardWeeklyTrophies',
        '/monstergrid/getData',
        '/gallery/searchByTag'
    ];
}
