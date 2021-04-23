<?php

namespace app\Repositories;

use App\Models\Trophy;
use App\Models\Monster;
use Illuminate\Support\Facades\Log;

class DBTrophyRepository{

  function find($id){
    return Trophy::find($id); 
  }
  
  function awardTrophy($user_id, $trophyType=NULL, $color=NULL, $description=NULL){
    $trophy = new Trophy;
    $trophy->user_id = $user_id;
    if ($trophyType) $trophy->type_id = $trophyType->id;
    if ($color) $trophy->default_color = $color;
    if ($description) $trophy->default_description = $description;
    $trophy->save();
  }
  function awardWeeklyTrophies($monsterIds){

      //gold
      $monster = Monster::find($monsterIds['first']);
      $arr =[];
      foreach($monster->segments as $segment){
        $user_id=$segment->creator;
        if ($user_id && !in_array($user_id, $arr)){
          $description="Monster of the week: <a href=\"/gallery/$monster->id\">$monster->name</a>";
          if ($monster->nsfw) $description.=" (NSFW)";
          $this->awardTrophy($user_id, NULL, 'gold', $description);
          $arr[]=$user_id;
        }
      }

      //silver
      $monster = Monster::find($monsterIds['second']);
      $arr =[];
      foreach($monster->segments as $segment){
        $user_id=$segment->creator;
        if (!in_array($user_id, $arr)){
          $description="2nd place- Monster of the week: <a href=\"/gallery/$monster->id\">$monster->name</a>";
          if ($monster->nsfw) $description.=" (NSFW)";
          $this->awardTrophy($user_id, NULL, 'silver', $description);
          $arr[]=$user_id;
        }
      }

      //bronze
      $monster = Monster::find($monsterIds['third']);
      $arr =[];
      foreach($monster->segments as $segment){
        $user_id=$segment->creator;
        if (!in_array($user_id, $arr)){
          $description="3rd place- Monster of the week: <a href=\"/gallery/$monster->id\">$monster->name</a>";
          if ($monster->nsfw) $description.=" (NSFW)";
          $this->awardTrophy($user_id, NULL, 'bronze', $description);
          $arr[]=$user_id;
        }
      }

    // INSERT INTO `trophies` (`id`, `user_id`, `type_id`, `default_color`, `default_description`, `created_at`, `updated_at`)
    // VALUES
    //   (1745, 1531, NULL, 'gold', 'Monster of the week: <a href=\"/gallery/14551\">Rotten Wings</a>', '2021-03-05 22:12:23', '2021-03-05 22:12:23');
    
  }
}