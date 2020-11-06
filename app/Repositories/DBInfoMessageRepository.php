<?php

namespace app\Repositories;

use App\Models\InfoMessage;
use Illuminate\Support\Facades\DB;

class DBInfoMessageRepository{

  function getActiveMessages($user_id = 0){
    return InfoMessage::where('start_date', '<', DB::raw('now()'))
      ->where('end_date', '>' , DB::raw('now()'))
      ->when($user_id > 0, function($q){
        $q->whereIn('member_status', ['members','any']);
      })
      ->when($user_id == 0, function($q){
        $q->whereIn('member_status', ['non-members','any']);
      })
      ->where(function ($q) use($user_id){
          $q->whereNull('user')
          ->orWhere('user', $user_id);
      })
      ->whereDoesntHave('closed_info_messages', function($q) use($user_id){
          $q->where('user_id', $user_id);
      })
      ->get();
  }
}