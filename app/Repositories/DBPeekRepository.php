<?php

namespace app\Repositories;

use App\Models\Peek;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DBPeekRepository{
  
  function create($user_id = NULL, $monster_id = NULL, $segment){

    $peek = new Peek;
    $peek->user_id = $user_id;
    $peek->monster_id = $monster_id;
    $peek->segment = $segment;
    $peek->save();
    
  }

}