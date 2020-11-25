<?php

namespace app\Repositories;

use App\Models\AuditAction;
use Illuminate\Support\Facades\Log;

class DBAuditRepository{
  
  function create($user_id = NULL, $monster_id = NULL, $type, $action){
    $auditAction = new AuditAction;
    $auditAction->user_id = $user_id;
    $auditAction->monster_id = $monster_id;
    $auditAction->type = $type;
    $auditAction->action = $action;
    $auditAction->save();
  }

  function getActions($user_id = NULL){
    return AuditAction::with(['monster','user'])
      ->orderBy('created_at', 'desc')
      ->limit(10)
      ->get();
  }
}