<?php

namespace app\Repositories;

use App\Models\Tag;
use App\Models\TagSubmission;

class DBTagRepository{
  
  function find($tag_id){
    return Tag::find($tag_id);
  }

  function saveSubmission($user_id, $monster_id, $name){
    $tagSubmission = new TagSubmission;
    $tagSubmission->user_id = $user_id;
    $tagSubmission->monster_id = $monster_id;
    $tagSubmission->name = $name;
    $tagSubmission->save();
  }

  function getTagSubmissions($monster_id, $name){
    return TagSubmission::where('monster_id',$monster_id)
      ->where('name', $name)
      ->get();
  }

  function saveTag($monster_id, $name){
    $tagSubmission = new Tag;
    $tagSubmission->monster_id = $monster_id;
    $tagSubmission->name = $name;
    $tagSubmission->save();
  }
}