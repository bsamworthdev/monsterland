<?php

namespace app\Repositories;

use App\TrophyType;

class DBTrophyTypeRepository{

  function getAll(){
    return TrophyType::get(); 
  }
}