<?php

namespace app\Repositories;

use App\Models\User;
use App\Models\InfoMessage;
use App\Models\Comment;
use App\Models\Rating;
use App\Models\Streak;
use App\Models\Monster;
use App\Models\Favourite;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DBUserRepository{

  function find($user_id, $with = NULL){
    return User::when($with, function($q) use ($with){
      $q->with($with);
    })->find($user_id); 
  }

  function getAllActiveUsers($includeSegments = false, $includeTrophies = false,
    $includeRatings = false, $includeStreak = false, $inLastDays = NULL){

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
      ->when($inLastDays <> NULL, function($q) use ($inLastDays) {
        $q->where('last_active_at','>=', Carbon::now()->subDays($inLastDays)->toDateTimeString());
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

  function ungildUser($user_id){
    User::where('id',$user_id)
      ->update([
        'vip' => '0'
      ]);
  }

  function monitorUser($user_id){
    User::where('id',$user_id)
      ->update([
        'needs_monitoring' => '1'
      ]);
  }

  function unmonitorUser($user_id){
    User::where('id',$user_id)
      ->update([
        'needs_monitoring' => '0'
      ]);
  }

  function getStats($user_id){
    $comments = Comment::where('user_id', $user_id)
      ->limit(5)
      ->orderBy('created_at', 'desc')
      ->get(); 

    $commentCount = Comment::where('user_id', $user_id)
      ->orderBy('created_at', 'desc')
      ->count(); 

    $monsterCount = Monster::join('monster_segments', 'monster_segments.monster_id', '=', 'monsters.id')
      ->where('monsters.status', 'complete')
      ->where('monster_segments.created_by',$user_id)
      ->count();

    $ratings = Rating::with(['monster'])
      ->where('user_id', $user_id)
      ->limit(5)
      ->orderBy('created_at', 'desc')
      ->get(); 

    $ratingCount = Rating::where('user_id', $user_id)
      ->orderBy('created_at', 'desc')
      ->count(); 

    $topStreak = Streak::where('user_id', $user_id)
      ->pluck('top_streak')
      ->first();

    $favouritesCount = Favourite::where('user_id', $user_id)->count();

    $stats = [
      'comments' => $comments,
      'comment_count' => $commentCount,
      'monster_count' => $monsterCount,
      'ratings' => $ratings,
      'rating_count' => $ratingCount,
      'top_streak' => $topStreak,
      'favourites_count' => $favouritesCount
    ];

    return collect($stats);
  }

  function updateNotificationsLastViewed($user_id){
    User::where('id',$user_id)
      ->update([
        'last_viewed_notifications_at' => Carbon::NOW()
      ]);
  }

  function setHasUsedApp($user_id, $key){
    if ($key == 'f349'.$user_id.'v4t3' || $key == 'q34s'.$user_id.'v41U'){
      User::where('id',$user_id)
        ->update([
          'has_used_app' => 1
        ]);
    }
  }

  function decrementPeekCount($user_id){

    $user = User::where('id',$user_id)->first();
    $peek_count = $user->peek_count;

    User::where('id',$user_id)
      ->update([
        'peek_count' => $peek_count-1
      ]);
  }

  function decrementTakeTwoCount($user_id){

    $user = User::where('id',$user_id)->first();
    $take_two_count = $user->take_two_count;

    User::where('id',$user_id)
      ->update([
        'take_two_count' => $take_two_count-1
      ]);
  }

  function findUserByName($search){
      //$user = User::whereRaw("replace(name, ' ','_') = '" . $search . "'")->first();
      $user = User::where(DB::Raw("replace(name, ' ','')"), $search)->first();
      return $user; 
  }

}