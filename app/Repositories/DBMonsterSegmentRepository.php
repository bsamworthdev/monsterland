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

      $date_time = date('Ymd_His');
      foreach ($segments as $segment){
        $old_name = $monster_id.'_'.$segment.'.png';
        $new_name = $monster_id.'_'.$segment.'_deleted_'.$date_time.'.png';
        $old_path = storage_path('app/public/segments/'.$old_name);
        $new_path = storage_path('app/public/segments/'.$new_name);
        if (file_exists($old_path)){
          rename($old_path, $new_path);
        }
      }
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

  function userDrewPreviousSection($current_segment, $monster, $user_id){
    if ( $monster->direction == 'down'){
      //Head first monster
      if (
        ($current_segment == 'body' && $this->findSegmentCreator($monster->id, 'head') == $user_id)
        || 
        ($current_segment == 'legs' && $this->findSegmentCreator($monster->id, 'body') == $user_id)
        ) {
          return true;
      }
    } else {
      //Legs first monster
      if (
        ($current_segment == 'body' && $this->findSegmentCreator($monster->id, 'legs') == $user_id)
        || 
        ($current_segment == 'head' && $this->findSegmentCreator($monster->id, 'body') == $user_id)
        ) {
          return true;
      }
    }
  }

  function getFirstSegmentName($monster){
    if ( $monster->direction == 'down'){
      //Head first monster
      return 'head';
    } else {
      //Legs first monster
      return 'legs';
    }
  } 
}