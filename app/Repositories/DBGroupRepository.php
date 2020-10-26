<?php

namespace app\Repositories;

use App\Models\Group;

class DBGroupRepository{

  function getGroupsByUser($user_id){
    return Group::when($user_id != 1, function($q){
          return $q->where('created_by_user_id', $user_id);
      })
      ->orderBy('created_at','desc')
      ->get();
  }

  function getGroupByCode($code){
    return Group::where('code', $code)
      ->get(['id', 'name'])
      ->first();
  }

  function getGroupById($id, $user_id){
    return Group::where('id', $id)
      ->where('created_by_user_id', $user_id)
      ->get(['id', 'name'])
      ->first();
  }
}