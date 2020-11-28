<?php

namespace app\Repositories;

use App\Models\AuditAction;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DBAuditRepository{
  
  function create($user_id = NULL, $monster_id = NULL, $type, $action){
    $auditAction = new AuditAction;
    $auditAction->user_id = $user_id;
    $auditAction->monster_id = $monster_id;
    $auditAction->type = $type;
    $auditAction->action = $action;
    $auditAction->save();
  }

  function getActions($user = NULL, $timeInterval = NULL){
    
    $actions = AuditAction::with(['user', 'monster' => function($q) use($user) {
      $q->when(!$user || $user->allow_nsfw == 0, function($q1) {
        $q1->where('nsfw', '0');
      });
    }])
    ->when($timeInterval, function($q) use($timeInterval) {
      $q->where('created_at', '>', Carbon::now()->subSeconds($timeInterval)->toDateTimeString());
    })
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

    return $actions;
  }
}