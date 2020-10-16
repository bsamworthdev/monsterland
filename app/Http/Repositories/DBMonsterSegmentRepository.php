<?php

namespace app\http\Repositories;

use App\MonsterSegment;

class DBMonsterSegmentRepository{

  function createInstance(){
    return new MonsterSegment;
  } 

  function deleteMonsterSegments($monster_id, $segments){
    MonsterSegment::where('monster_id', $monster_id)
      ->whereIn('segment', $segments)
      ->delete(); 
  }
}