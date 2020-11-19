<?php

namespace app\Repositories;

use App\Models\User;
use App\Models\InfoMessage;
use Carbon\Carbon;

class DBUserRepository{

  function find($user_id, $with = NULL){
    return User::when($with, function($q) use ($with){
      $q->with($with);
    })->find($user_id); 
  }

  function getAllActiveUsers($includeSegments = false, $includeTrophies = false,
    $includeRatings = false, $includeStreak = false){

    return User::when($includeSegments, function($q) {
          $q->with('monsterSegments');
      })
      ->when($includeTrophies, function($q) {
          $q->with('trophies');
      })
      ->when($includeRatings, function($q) {
          $q->with('ratings');
      })
      ->when($includeStreak, function($q) {
        $q->with('streak');
      })
      ->whereNotNull('email_verified_at')
      ->get();
  }

  function hasTrophyOfType($user, $trophyType){
    $hasTrophy = false;
    foreach($user->trophies as $trophy){
      if ($trophy->type_id == $trophyType->id){
          $hasTrophy=true;
      break;
      }
    }
    return $hasTrophy;
  }

  function gildUser($user_id){
    User::where('id',$user_id)
      ->update([
        'vip' => '1'
      ]);
    InfoMessage::create([
      'text' => 'Good news! Your contributions have been so good you\'ve 
        been upgraded to a pro. You\'ll see a little star next to your name 
        and you can now create Pro monsters!',
      'user' => $user_id,
      'style' => 'success',
      'start_date' => Carbon::now(),
      'end_date' => Carbon::now()->addHours(2),
    ]);
  }
}