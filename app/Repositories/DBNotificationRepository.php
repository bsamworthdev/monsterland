<?php

namespace app\Repositories;

use App\Models\NotificationClosed;

class DBNotificationRepository{

  function closeNotification($user_id, $audit_id){
    $notificationClosed = new NotificationClosed;
    $notificationClosed->user_id = $user_id;
    $notificationClosed->audit_id = $audit_id;
    $notificationClosed->save();
  }

}