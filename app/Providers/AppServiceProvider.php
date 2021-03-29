<?php

namespace App\Providers;

use View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // View::composer('app', function($view){
        //     $view->with('notifications', );
        // });

        // if (Auth::check()){
        //     $notifications = Auth::user()->myNotifications;
        //     // Redis::set('notifications', $notifications);

        //     View::composer('*', function($view) use ($notifications){
        //         $val = $notifications;
        //         $view->with('notifications', $val);
        //     });
        // }

        
        View::composer('*', function($view){
            if (Auth::check()){
                $hashed_user_id = Hash::make(Auth::user()->id);
                if (Redis::exists($hashed_user_id.'_notifications_last_fetched') && 
                    Carbon::NOW()->diffInMinutes(Redis::get($hashed_user_id.'_notifications_last_fetched')) < 10){

                    //Only get notifications once every 10 minutes
                    $notifications = Redis::get($hashed_user_id.'_notifications');
                } else {
                    $notifications = Auth::user()->myNotifications;
                    Redis::set($hashed_user_id.'_notifications', $notifications);
                    Redis::set($hashed_user_id.'_notifications_last_fetched', Carbon::NOW());
                }
                $view->with('notifications', $notifications);
            }
        });
    }
}
