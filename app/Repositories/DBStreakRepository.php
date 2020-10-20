<?php

namespace app\Repositories;

use App\Models\Streak;

class DBStreakRepository{

  function findOrCreateByUser($user_id){
    return Streak::where('user_id',$user_id)->firstOrNew(); 
  }

  function updateStreak($user_id){
    $streak = $this->findOrCreateByUser($user_id);
    $streak->user_id = $user_id;
    if (date('Y-m-d', strtotime($streak->updated_at)) == date('Y-m-d',strtotime("-1 days"))){
        //Yesterday
        $streak->current_streak += 1;
    } else {
        if (date('Y-m-d', strtotime($streak->updated_at)) != date('Y-m-d')){
            //Not Today
            $streak->current_streak = 1;
        }
    }

    if ($streak->current_streak > $streak->top_streak) {
        //Broken top streak record
        $streak->top_streak = $streak->current_streak;
        $streak->top_streak_at = date('Y-m-d H:i:s');
    }
    $streak->save();
  }
  
}