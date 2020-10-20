<?php

namespace app\Repositories;

use App\Models\Trophy;

class DBTrophyRepository{

  function find($id){
    return Trophy::find($id); 
  }
  
  function awardTrophy($user, $trophyType){
    $trophy = new Trophy;
    $trophy->user_id = $user->id;
    $trophy->type_id = $trophyType->id;
    $trophy->save();
  }
}