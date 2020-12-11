<?php

namespace app\Repositories;

use App\Models\AuditAction;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DBAuditRepository{
  
  function create($user_id = NULL, $monster_id = NULL, $type, $action){
    $auditAction = new AuditAction;
    $auditAction->user_id = $user_id;
    $auditAction->monster_id = $monster_id;
    $auditAction->type = $type;
    $auditAction->action = $action;
    $auditAction->save();

    if ($user_id){
      if ($type == 'monster_completed' || 
        $type == 'segment_completed' ||
        $type == 'comment'
      ){
        DB::table('user_linked_monsters')->updateOrInsert([
            'user_id' => $user_id, 
            'monster_id' => $monster_id,
          ],[
            'user_id' => $user_id, 
            'monster_id' => $monster_id,
            'created_at' => now(),
            'updated_at' => now()
          ]
        );
      }
    }
  }

  function getActions($user = NULL, $timeInterval = NULL){
    
    $actions = AuditAction::with(['user', 'monster'])
      ->when($timeInterval, function($q) use($timeInterval) {
        $q->where('created_at', '>', Carbon::now()->subSeconds($timeInterval)->toDateTimeString());
    })
    ->whereHas('monster', function($q) use ($user){
      $q->where('group_id','0')
      ->when(!$user || $user->allow_nsfw == 0, function($q1) {
        $q1->where('nsfw', '0');
      });
    })
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

    return $actions;
  }
}