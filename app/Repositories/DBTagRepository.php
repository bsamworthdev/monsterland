<?php

namespace app\Repositories;

use App\Models\Tag;
use App\Models\TagSubmission;
use App\Models\Profanity;
use Illuminate\Support\Facades\Log;

class DBTagRepository{
  
  function find($tag_id){
    return Tag::find($tag_id);
  }

  function saveSubmission($user_id, $monster_id, $name){
    if (Profanity::where('word', $name)->count() > 0) return;

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

  function removeTag($monster_id, $tag_id){
    $tag = Tag::find($tag_id);

    TagSubmission::where('monster_id', $monster_id)
      ->where('name', $tag->name)
      ->delete();

    Tag::where('id', $tag_id)
      ->delete();

  }

  function addTag($user_id, $monster_id, $name){
  
    $tag = new Tag;
    // $tag->user_id = $user_id;
    $tag->monster_id = $monster_id;
    $tag->manually_added_by = $user_id;
    $tag->name = $name;
    $tag->save();

    return $tag;
  }

}