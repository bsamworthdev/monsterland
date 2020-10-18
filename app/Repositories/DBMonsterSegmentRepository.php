<?php

namespace app\Repositories;

use App\MonsterSegment;

class DBMonsterSegmentRepository{

  function createInstance(){
    return new MonsterSegment;
  } 

  function getCurrentSegmentName($status){
    $monster_segment_name = '';
    if ($status == 'awaiting head'){
        $monster_segment_name = 'head';
    } elseif ($status == 'awaiting body'){
        $monster_segment_name = 'body';
    } elseif ($status == 'awaiting legs'){
        $monster_segment_name = 'legs';
    }
    return $monster_segment_name;
  }

  function deleteMonsterSegments($monster_id, $segments){
    MonsterSegment::where('monster_id', $monster_id)
      ->whereIn('segment', $segments)
      ->delete(); 
  }
}