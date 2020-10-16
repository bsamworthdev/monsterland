<?php

namespace app\http\Repositories;

use App\TrophyType;

class DBTrophyTypeRepository{

  function getAll(){
    return TrophyType::get(); 
  }
}