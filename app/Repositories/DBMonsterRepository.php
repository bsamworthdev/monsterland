<?php

namespace app\Repositories;

use App\Models\Monster;
use App\Models\MonsterSegment;
use App\Models\RollbackSuggestion;
use App\Models\TakeTwoRequest;
use App\Models\Rating;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use \Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Image;

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
      ->where('suggest_rollback', '0')
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
        ->get()
        ->first();
    } else {
      $monster = Monster::where('id',$id)
        ->when(!$user, function($q) {
            $q->where('status','complete');
        })
        ->where('group_id',$group_id)
        ->when($user && in_array($user->id, [1,2]), function($q) {
          $q->where('suggest_rollback', '0');
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
      ->where('suggest_rollback', '0')
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
      ->where('suggest_rollback', '0')
      ->when(!$user || $user->allow_nsfw == 0, function($q) {
          $q->where('nsfw', '0');
      })
      ->where('status','complete')
      ->where('group_id', $group_id)
      ->orderBy('completed_at', 'desc')
      ->get(['id','name'])
      ->first();

  }

  function rollbackMonster($monster_id, $segments){

    //Do rollback
    $monster = $this->find($monster_id);
    if (in_array('body',$segments)){
      $monster->status = 'awaiting body';
    } else {
      $monster->status = 'awaiting legs';
    }
    $monster->suggest_rollback = 0;
    $monster->image = NULL;
    $monster->save();

    //Roll back ratings
    Rating::where('monster_id',$monster_id)
      ->delete();

    //Set suggestion as accepted
    RollbackSuggestion::where('monster_id',$monster_id)
      ->update([
        'status' => 'accepted'
      ]);
  }

  function flagMonster($monster_id, $severity){

    $monster = $this->find($monster_id);

    if ($severity == 'nsfl'){
      $monster->nsfl = 1;
      $monster->nsfw = 1;
      $rollback_status = 'censored';
    } else if ($severity == 'nsfw'){
      $monster->nsfl = 0;
      $monster->nsfw = 1;
      $rollback_status = 'censored';
    } else if ($severity == 'safe'){
      $monster->nsfl = 0;
      $monster->nsfw = 0;
      $monster->approved_by_admin = 1;
      $rollback_status = 'rejected';
    } else if ($severity == 'validated'){
      //validated that latest 
    }
    $monster->suggest_rollback = 0;

    $monster->save();

    RollbackSuggestion::where('monster_id',$monster_id)
      ->update([
        'status' => $rollback_status
      ]);
  }

  function validateMonster($monster_id){
    $monster = $this->find($monster_id);
    $monster->needs_validating = 0;
    $monster->save();
  }

  function abortMonster($id){
    $monster = $this->find($id);
    $monster->status = 'cancelled';
    $monster->suggest_rollback = 0;
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

    RollbackSuggestion::where('monster_id',$monster_id)
      ->update([
        'status' => 'accepted'
      ]);
  }

  function takeTwoOnMonster($monster_id, $segment_name){
    //Monster
    $existing_monster = Monster::find($monster_id);
    
    $monster_name = $existing_monster->name;
    $new_monster = $existing_monster->replicate();
    $pattern = "/(\(v.*?)\)/"; //e.g. (v2)

    
    if (preg_match($pattern, $monster_name) > 0){
      $start_pos = strpos($monster_name, '(v');
      Log::Debug('$start_pos'.$start_pos);
      $end_pos = strpos($monster_name, ')',$start_pos);
      Log::Debug('$end_pos'.$end_pos);
      $version = substr($monster_name, ($start_pos+2), ($end_pos-$start_pos-2));
      Log::Debug('$version'.$version);
      $new_monster_name = substr($existing_monster->name, 0, $start_pos)." (v".($version+1).")";
    } else {
      $new_monster_name = $existing_monster->name." (v2)";
    }
    $new_monster->name = $new_monster_name;
    $new_monster->image = NULL;
    $new_monster->request_take_two = 0;
    if ($segment_name == 'head'){
      $new_monster->status = "awaiting body";
    } else {
      $new_monster->status = "awaiting legs";
    }
    $new_monster->save();
    $new_monster_id = $new_monster->id;

    //Segments
    $existing_segments = MonsterSegment::where('monster_id', $monster_id)
      ->when($segment_name == 'head', function($q){
        $q->where('segment','head');
      })
      ->when($segment_name == 'body', function($q){
        $q->whereIn('segment',['head','body']);
      })
      ->get();

    $monster_image_path = $existing_monster->image;
    $monster_image = Storage::disk('public')->get(basename($monster_image_path));
    foreach ($existing_segments as $existing_segment){
      $new_segment = $existing_segment->replicate();
      $new_segment->monster_id = $new_monster_id;
    
      if ($new_segment->image === 'NULL'){
        $new_segment->image = 'data:image/png;base64,'.$this->base64EncodeSegment($new_segment->segment, $monster_image);
      }
      $new_segment->save();
    }

    TakeTwoRequest::where('monster_id', $monster_id)->update([
      'status' => 'accepted'
    ]);
  }

  function base64EncodeSegment($segment, $image){

    if ($segment == 'head'){
      $cropped_image = Image::make($image)->crop(800, 266, 0, 0)->encode('png');
    } elseif($segment == 'body') {
      $cropped_image = Image::make($image)->crop(800, 299, 0, 236)->encode('png');
    }
    return base64_encode($cropped_image); 
  }

  function rejectTakeTwoOnMonster($monster_id){
    TakeTwoRequest::where('monster_id', $monster_id)->update([
      'status' => 'rejected'
    ]);
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

  function removeOldB64Images(){
    //Find ids of monsters created more than 10 days ago
    $monster_ids = Monster::where('completed_at','<',DB::raw('date_sub(now(),interval 10 day)'))
      ->whereIn('status',['complete','cancelled'])
      ->where('id','>',100)
      ->where('id','>=',8000)
      ->whereNotNull('image') 
      ->where('image','<>','n/a') 
      ->where('image','<>','') 
      ->pluck('id');

      Log::Debug($monster_ids);

    //Clear base64 images older than 10 days
    MonsterSegment::whereIn('monster_id',$monster_ids)
      ->update(
          [
          'image' => ''
          ]
      );
  }

  function getTopMonsters($user, $group_id, $date = NULL, $search = '', $page = -1){
    return Monster::withCount([
      'ratings as average_rating' => function($q) {
          $q->select(DB::raw('coalesce(avg(rating),0)'));
      }, 
      'ratings as ratings_count'])
      ->where('status', 'complete')
      ->where('suggest_rollback', '0')
      ->when($date, function($q) use ($date) {
        $q->where('completed_at','>=',$date);
      })
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
      ->when($page <> -1, function($q) use ($page) {
        $q->skip($page*8)
          ->take(8);
      })
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
      ->where('suggest_rollback', '0')
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
    $monsters = Monster::where('status', '<>', 'complete')
      ->where('status', '<>', 'cancelled')
      ->where('status', '<>', 'awaiting head')
      ->where('nsfl', '0')
      ->where('suggest_rollback', '0')
      ->when(!$user || $user->allow_nsfw == 0, function($q) {
        $q->where('nsfw', '0');
      })
      ->where('group_id', $group_id)
      ->orderBy('created_at', 'desc')
      ->get(['id', 'name', 'in_progress', 'nsfw','nsfl','group_id','vip','needs_validating','status','auth','created_at',
          DB::Raw("(updated_at<'".Carbon::now()->subHours(1)->toDateTimeString()."') as abandoned") 
      ]);
      $monsters->append('created_at_tidy');
      return $monsters;
  }

  function getFlaggedMonsters(){
    return Monster::where('suggest_rollback', '1')
      ->where('group_id', '0')
      ->get(['id', 'name', 'nsfw','status']);
  }

  function getFlaggedCommentMonsters(){
    return Monster::join('comments', 'monsters.id','=','comments.monster_id')
      ->where('comments.spam', '1')
      ->where('comments.deleted', '0')
      ->distinct()
      ->get(['monsters.id', 'monsters.name', 'monsters.nsfw','monsters.status']);
  }

  function getMonitoredMonsters(){
    return Monster::where('needs_validating', '1')
      ->get(['id', 'name', 'nsfw','status']);
  }

  function getTakeTwoMonsters(){
    return Monster::join('take_two_requests', 'monsters.id','=','take_two_requests.monster_id')
      ->where('take_two_requests.status', 'pending')
      ->get(['monsters.id', 'monsters.name', 'monsters.nsfw','monsters.status']);
  }

  function suggestMonsterRollback($user_id, $monster_id){
    Monster::where('id', $monster_id)
      ->update(
          [
          'suggest_rollback' => 1
          ]
      );
    RollbackSuggestion::create([
      'monster_id' => $monster_id,
      'requested_by' => $user_id,
      'status' => 'pending'
    ]);
  }

  function requestTakeTwoOnMonster($user_id, $monster_id, $segment_name){
    Monster::where('id', $monster_id)
    ->update(
        [
        'request_take_two' => 1
        ]
    );
    TakeTwoRequest::create([
      'monster_id' => $monster_id,
      'requested_by' => $user_id,
      'from_segment' => $segment_name,
      'status' => 'pending'
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

  function updateMonsterName($user_id, $monster_id, $monster_name){

    Monster::where('id', $monster_id)
      ->where('in_progress_with', $user_id)
      ->update(
          [
          'name' => $monster_name
          ]
      );
  }
  
  function updateMonsterLevel($user_id, $monster_id, $monster_level){

    switch ($monster_level){
      case 'basic':
        $isVip = 0;
        $isAuth = 0;
          break;
      case 'standard':
        $isVip = 0;
        $isAuth = 1;
          break;
      case 'pro':
          $isVip = 1;
          $isAuth = 1;
          break;
    }

    Monster::where('id', $monster_id)
      ->where('in_progress_with', $user_id)
      ->update(
          [
            'vip' => $isVip,
            'auth' => $isAuth
          ]
      );
  }

  function getFeaturedMonsters(){
    return Monster::where('featured', '1')
      ->get();
  }

  function getRandomMonster(){
    return Monster::without(['segments','ratings'])
      ->select('id','name','created_at')
      ->where('created_at', '<',  DB::raw('date_sub(now(),interval 4 week)'))
      ->where('nsfw',0)
      ->where('nsfl',0)
      ->where('status','complete')
      ->withCount([
        'ratings as average_rating' => function($query) {
            $query->select(DB::raw('coalesce(avg(rating),0)'));
        }, 
        'ratings as ratings_count'])
      ->having('average_rating', '>', 8)
      ->having('ratings_count', '>', 1)
      ->inRandomOrder()
      ->get()
      ->first();
  }

  function updateAuthLevel($monster_id, $level){
    switch ($level){
      case 'basic':
        $isVip = 0;
        $isAuth = 0;
          break;
      case 'standard':
        $isVip = 0;
        $isAuth = 1;
          break;
      case 'pro':
          $isVip = 1;
          $isAuth = 1;
          break;
    }
    Monster::where('id', $monster_id)
      ->where('in_progress',0)
      ->update(
          [
          'vip' => $isVip,
          'auth' => $isAuth
          ]
      );
  }
}