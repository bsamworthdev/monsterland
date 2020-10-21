<?php

namespace app\Repositories;

use App\Models\Monster;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DBMonsterRepository{

  function find($id, $with = NULL){
    if ($with) {
      return Monster::with($with)->find($id);
    } else {
      return Monster::find($id);
    }
  }

  function getInstance(){
    return new Monster;
  }

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

  function getMonsterById($id, $user, $group_id){

    if ($user && in_array($user->id, [1,2])){
      $monster = Monster::with('segmentsWithImages')
        ->where('id',$id)
        ->where('group_id',$group_id)
        ->get()
        ->first();
    } else {
      $monster = Monster::where('id',$id)
        ->when(!$user, function($q) {
            $q->where('status','complete');
        })
        ->where('group_id',$group_id)
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

    $monster = $this->find($id);
    if (in_array('body',$segments)){
      $monster->status = 'awaiting body';
    } else {
      $monster->status = 'awaiting legs';
    }
    $monster->image = NULL;
    $monster->save();
  }

  function flagMonster($id, $severity){

    $monster = $this->find($id);

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
    $monster = $this->find($id);
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

  function startMonster($id, $user_id, $session_id){
    $monster = $this->find($id);
    $monster->in_progress = 1;
    $monster->in_progress_with = $user_id;
    $monster->in_progress_with_session_id = $session_id;
    $monster->save();
  }

  function cancelMonster($id){
    $monster = $this->find($id);
    $monster->in_progress = 0;
    $monster->in_progress_with = 0;
    $monster->in_progress_with_session_id = NULL;
    $monster->save();
  }

  function cancelInactiveMonsters(){
    //Cancel monster if inactive for 30 minutes
    Monster::where('in_progress','1')
      ->where('updated_at', '<', 
          Carbon::now()->subMinutes(30)->toDateTimeString()
      )
      ->update(
          [
          'in_progress' => 0, 
          'in_progress_with' => 0, 
          'in_progress_with_session_id' => NULL
          ]
      );
  }

  function createMissingMonsterImages(){
    $monsters = Monster::where('status','complete')
      ->whereNull('image')
      ->get();
    foreach($monsters as $monster){
        $monster = $this->find($monster->id); 
        $image = $monster->createImage();
        $monster->image = $image;
        $monster->save();
    } 
  }

  function getTopMonsters($user, $date, $group_id, $search, $page){
    return Monster::withCount([
      'ratings as average_rating' => function($query) {
          $query->select(DB::raw('coalesce(avg(rating),0)'));
      }, 
      'ratings as ratings_count'])
      ->where('status', 'complete')
      ->where('completed_at','>=',$date)
      ->where('nsfl', '0')
      ->when(!$user || $user->allow_nsfw == 0, function($q) {
          $q->where('nsfw', '0');
      })
      ->where('group_id', $group_id)
      ->where('name','LIKE','%'.$search.'%')
      ->when($group_id == 0, function($q) {
          $q->having('average_rating', '>', 0)
          ->having('ratings_count', '>', 0);
      })
      ->orderBy('average_rating','desc')
      ->orderBy('ratings_count', 'desc')
      ->orderBy('name', 'asc')
      ->skip($page*8)
      ->take(8)
      ->get();
  }

  function getTopMonstersByUser($selected_user, $current_user, $date, $search, $page){
    return Monster::withCount([
      'ratings as average_rating' => function($query) {
          $query->select(DB::raw('coalesce(avg(rating),0)'));
      }, 
      'ratings as ratings_count'])
      ->join('monster_segments', 'monster_segments.monster_id', '=', 'monsters.id')
      ->where('monsters.status', 'complete')
      ->where('monsters.completed_at','>=',$date)
      ->where('monster_segments.created_by',$selected_user->id)
      ->where('nsfl', '0')
      ->when(!$current_user || $current_user->allow_nsfw == 0, function($q) {
          $q->where('nsfw', '0');
      })
      ->where('name','LIKE','%'.$search.'%')
      ->groupBy('monsters.id')
      ->orderBy('average_rating','desc')
      ->orderBy('ratings_count', 'desc')
      ->orderBy('monsters.name', 'asc')
      ->skip($page*8)
      ->take(8)
      ->get();
  }

  function getUnfinishedMonsters($user = NULL, $group_id = 0){
    return Monster::where('status', '<>', 'complete')
      ->where('status', '<>', 'cancelled')
      ->where('status', '<>', 'awaiting head')
      ->where('nsfl', '0')
      ->when(!$user || $user->allow_nsfw == 0, function($q) {
        $q->where('nsfw', '0');
      })
      ->where('group_id', $group_id)
      ->get(['id', 'name', 'in_progress', 'nsfw','nsfl','group_id','vip','status','auth',
          DB::Raw("(updated_at<'".Carbon::now()->subHours(1)->toDateTimeString()."') as abandoned") 
      ]);
  }

  function isAuth($level, $authUser){
    $auth=0;
    switch ($level){
        case 'basic':
            $auth = 0;
            break;
        case 'standard':
            if (!$authUser) return 0;
            $auth = 1;
            break;
        case 'pro':
            if (!$authUser) return 0;
            $auth = 1;
            break;
    }
    return $auth;
  }

  function isVIP($level, $vipUser){
    $isVip=0;
    switch ($level){
        case 'basic':
          $isVip = 0;
            break;
        case 'standard':
          $isVip = 0;
            break;
        case 'pro':
            if (!$vipUser) return 0;
            $isVip = 1;
            break;
    }
    return $isVip;
  }
}