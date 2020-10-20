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
}