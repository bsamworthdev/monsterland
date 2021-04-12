<?php

namespace app\Services;

use App\Trophy;

class TrophyService{

  function trophyConditionSatisfied($trophyType, $user){
    $trophyConditionSatisfied = false;
    switch ($trophyType->name){
        case 'first_monster':
            if (count($user->monsterSegments)>=1) $trophyConditionSatisfied = true;
            break;
        case 'ten_monsters':
            if (count($user->monsterSegments)>=10) $trophyConditionSatisfied = true;
            break;
        case 'hundred_monsters':
            if (count($user->monsterSegments)>=100) $trophyConditionSatisfied = true;
            break;
        case 'first_rating':
            if (count($user->ratings)>=1) $trophyConditionSatisfied = true;
            break;
        case 'ten_ratings':
            if (count($user->ratings)>=10) $trophyConditionSatisfied = true;
            break;
        case 'hundred_ratings':
            if (count($user->ratings)>=100) $trophyConditionSatisfied = true;
            break;
        case 'first_comment':
            if (count($user->comments)>=1) $trophyConditionSatisfied = true;
            break;
        case 'ten_comments':
            if (count($user->comments)>=10) $trophyConditionSatisfied = true;
            break;
        case 'hundred_comments':
            if (count($user->comments)>=100) $trophyConditionSatisfied = true;
            break;
        case 'popular_comment':
            $found = false;
            foreach ($user->comments as $comment){
                if ($comment->votes >= 5){
                    $found = true;
                break;
                }
            }
            $trophyConditionSatisfied = $found;
            break;
        case 'two_day_streak':
            // Log::debug('two day streak found'. $user->id.':'.$user->top_streak);
            if ($user->streak && $user->streak->top_streak>=2) $trophyConditionSatisfied = true;
            break;
        case 'first_tag_added':
            if ($user->tagsAdded && count($user->tagsAdded) >= 1) $trophyConditionSatisfied = true;
            break;
        case 'ten_tags_added':
            if ($user->tagsAdded  && count($user->tagsAdded) >= 10) $trophyConditionSatisfied = true;
            break;
        case 'hundred_tags_added':
            if ($user->tagsAdded  && count($user->tagsAdded) >= 100) $trophyConditionSatisfied = true;
            break;
    }

    return $trophyConditionSatisfied;
  }
  
}