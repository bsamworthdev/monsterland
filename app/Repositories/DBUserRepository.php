<?php

namespace app\Repositories;

use App\User;

class DBUserRepository{

  function find($user_id){
    return User::find($user_id); 
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
}