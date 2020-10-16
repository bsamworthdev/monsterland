<?php

namespace app\http\Repositories;

use App\Monster;

class DBMonsterRepository{

  function getLatestCompletedMonster($user, $group_id){
    return Monster::without(['segments', 'ratings'])
      ->where('nsfl', '0')
      ->where('status','complete')
      ->when(!$user || $user->allow_nsfw == 0, function($q) {
          $q->where('nsfw', '0');
      })
      ->where('group_id',$group_id)
      ->orderBy('completed_at', 'desc')
      ->get(['id'])
      ->first();
  }

  function getMonsterById($id, $user){

    if ($user && in_array($user->id, [1,2])){
      $monster = Monster::with('segmentsWithImages')
        ->where('id',$id)
        ->get()
        ->first();
    } else {
      $monster = Monster::where('id',$id)
        ->when(!$user, function($q) {
            $q->where('status','complete');
        })
        ->get()
        ->first();
    }
    return $monster;
  }

  function getNextMonster($monster, $user, $group_id){
    return Monster::without(['segments', 'ratings'])
      ->where('id','<>', $monster->id)
      ->when($monster->completed_at, function($q) use($monster) {
          $q->where('completed_at','>', $monster->completed_at);
      })
      ->where('nsfl', '0')
      ->when(!$user || $user->allow_nsfw == 0, function($q) {
          $q->where('nsfw', '0');
      })
      ->where('status','complete')
      ->where('group_id', $group_id)
      ->orderBy('completed_at')
      ->get(['id','name'])
      ->first();
  }

  function getPrevMonster($monster, $user, $group_id){
    return Monster::without(['segments', 'ratings'])
      ->where('id','<>', $monster->id)
      ->when($monster->completed_at, function($q) use($monster) {
          $q->where('completed_at','<', $monster->completed_at);
      })
      ->where('nsfl', '0')
      ->when(!$user || $user->allow_nsfw == 0, function($q) {
          $q->where('nsfw', '0');
      })
      ->where('status','complete')
      ->where('group_id', $group_id)
      ->orderBy('completed_at', 'desc')
      ->get(['id','name'])
      ->first();

  }

  function rollbackMonster($id, $segments){

    $monster = Monster::find($id);

    if (in_array('body',$segments)){
      $monster->status = 'awaiting body';
    } else {
      $monster->status = 'awaiting legs';
    }

    $monster->image = NULL;
    $monster->save();
  }

  function flagMonster($id, $severity){

    $monster = Monster::find($id);

    if ($severity == 'nsfl'){
        $monster->nsfl = 1;
        $monster->nsfw = 1;
    } else if ($severity == 'nsfw'){
        $monster->nsfl = 0;
        $monster->nsfw = 1;
    } else if ($severity == 'safe'){
        $monster->nsfl = 0;
        $monster->nsfw = 0;
    }

    $monster->save();
  }

  function abortMonster($id){
    $monster = Monster::find($id);
    $monster->status = 'cancelled';
    $monster->save();
  }

  function resetUserMonsters($monster_id, $session_id){
    Monster::where('in_progress','1')
      ->where('id','<>', $monster_id)
      ->where('in_progress_with_session_id', $session_id)
      ->update(
          [
          'in_progress' => 0, 
          'in_progress_with' => 0, 
          'in_progress_with_session_id' => NULL
          ]
      );
  }

  function find($id, $with = NULL){
    if ($with) {
      return Monster::with($with)->find($id);
    } else {
      return Monster::find($id);
    }
  }
}