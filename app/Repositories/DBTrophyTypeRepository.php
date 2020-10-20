<?php

namespace app\Repositories;

use App\Models\TrophyType;

class DBTrophyTypeRepository{

  function getAll(){
    return TrophyType::get(); 
  }
}