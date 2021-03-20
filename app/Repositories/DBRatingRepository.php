<?php

namespace app\Repositories;

use App\Models\Rating;

class DBRatingRepository{
  function saveRating($user_id, $monster_id, $this_rating){
    $rating = new Rating;
    $rating->user_id = $user_id;
    $rating->monster_id = $monster_id;
    $rating->rating = $this_rating;
    $rating->save();
  }

  function deleteRating($user_id, $monster_id){
    Rating::where('user_id',$user_id)
      ->where('monster_id',$monster_id)
      ->delete();
  }

  function hasRated($user_id, $monster_id){
    $count = Rating::where('user_id', $user_id)
      ->where('monster_id', $monster_id)
      ->count();
    return $count > 0;
  }
}