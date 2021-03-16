<?php

namespace app\Repositories;

use App\Models\MonsterSegment;
use Illuminate\Support\Facades\DB;

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

  function findSegmentCreator($monster_id, $segment_name){
    $segment = MonsterSegment::where('monster_id', $monster_id)
      ->where('segment',$segment_name)
      ->first();

      if ($segment){
        $user_id = $segment->created_by;
      } else {
        $user_id = 0;
      }
      return $user_id;
  }

  function deleteMonsterSegments($monster_id, $segments){
    MonsterSegment::where('monster_id', $monster_id)
      ->whereIn('segment', $segments)
      ->delete(); 
  }

  function convertB64Images(){
    $segments = MonsterSegment::where('image','<>','')
      ->where('image', '<>', NULL)
      ->where('image_path', NULL)
      ->limit(100)
      ->get();

    foreach($segments as $segment){
      $monster_id = $segment->monster_id;
      $segment_name = $segment->segment;
      $image = $segment->image;
      $path = $segment->createImage($monster_id, $image, $segment_name);
      $segment->update(
        [
          'image_path' => $path
        ]
      );
    }
  }
}