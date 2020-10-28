<?php

namespace app\Repositories;

use App\Models\MonsterSegment;

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

  function findSegmentCreators($monster_id, $excluded_user_id = 0){
    return MonsterSegment::where('monster_id', $monster_id)
      ->whereNotNull('created_by')
      ->where('created_by','<>','0')
      ->where('created_by','<>',$excluded_user_id)
      ->pluck('created_by'); 
  }

  function deleteMonsterSegments($monster_id, $segments){
    MonsterSegment::where('monster_id', $monster_id)
      ->whereIn('segment', $segments)
      ->delete(); 
  }
}