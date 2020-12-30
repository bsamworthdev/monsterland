<?php

namespace app\Repositories;

use App\Models\RandomWord;

class DBRandomWordsRepository{

  function getAll(){
    $words = RandomWord::all();
    $resp = [];
    foreach($words as $word){
      $resp[$word->type][]=$word->word;
    }
    return collect($resp);

  }
}