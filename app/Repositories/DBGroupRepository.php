<?php

namespace app\Repositories;

use App\Models\Group;

class DBGroupRepository{

  function getGroupsByUser($user_id){
    $groups = Group::when($user_id != 1, function($q) use ($user_id){
        $q->where('created_by_user_id', $user_id);
      })
      ->when($user_id == 1, function($q) use ($user_id){
        $q->withCount('completedMonsters as completed_monsters_count')
          ->having('completed_monsters_count', '>', 1);
      })
      ->orderBy('created_at','desc')
      ->get();

      return $groups;
  }

  function getGroupByCode($code){
    return Group::where('code', $code)
      ->get(['id', 'name'])
      ->first();
  }

  function getGroupById($id, $user_id){
    return Group::where('id', $id)
      ->when($user_id != 1, function($q) use ($user_id){
        $q->where('created_by_user_id', $user_id);
      })
      ->get(['id', 'name'])
      ->first();
  }
}