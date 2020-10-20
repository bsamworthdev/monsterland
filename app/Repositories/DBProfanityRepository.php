<?php

namespace app\Repositories;

use App\Models\Profanity;
use Illuminate\Support\Facades\DB;

class DBProfanityRepository{

  function getMatchingProfanities($text){
    return Profanity::whereRaw('"'.$text.'" like CONCAT("%", word, "%")')
      ->orderBy('nsfl','desc')
      ->orderBy('nsfw','desc')
      ->get();
  }

  function isNSFW($text){
    $profanities = $this->getMatchingProfanities($text);
    if (count($profanities) > 0) {
        if ($profanities[0]->nsfw){
            return true;
        }
    }
    return false;
  }

  function isNSFL($text){
    $profanities = $this->getMatchingProfanities($text);
    if (count($profanities) > 0) {
        if ($profanities[0]->nsfl){
            return true;
        }
    }
    return false;
  }
}