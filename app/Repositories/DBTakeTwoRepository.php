<?php

namespace app\Repositories;

use App\Models\TakeTwo;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DBTakeTwoRepository{
  
  function create($user_id = NULL, $monster_id = NULL, $from_segment){

    $takeTwo = new TakeTwo;
    $takeTwo->user_id = $user_id;
    $takeTwo->monster_id = $monster_id;
    $takeTwo->from_segment = $from_segment;
    $takeTwo->save();
    
  }

}