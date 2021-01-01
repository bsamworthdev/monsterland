<?php

namespace app\Repositories;

use App\Models\RandomWord;

class DBRandomWordsRepository{

  function getAll(){
    $words = RandomWord::orderBy('word')->get();
    $resp = [];
    foreach($words as $word){
      $resp[$word->type][]=$word->word;
    }
    return collect($resp);

  }
}