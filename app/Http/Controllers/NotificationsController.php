<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\DBUserRepository;
use App\Repositories\DBNotificationRepository;

class NotificationsController extends Controller
{
    protected $DBUserRepo;
    protected $DBNotificationRepo;

    public function __construct(Request $request, 
        DBUserRepository $DBUserRepo,
        DBNotificationRepository $DBNotificationRepo)
    {
        $this->middleware(['auth','verified']);
        $this->DBUserRepo = $DBUserRepo;
        $this->DBNotificationRepo = $DBNotificationRepo;
    }

    public function update(Request $request){
        $action = $request->action;
        $user_id = Auth::User()->id;
        if (Auth::check()){
            if ($action == 'updateLastViewed'){
                $this->DBUserRepo->updateNotificationsLastViewed($user_id);
            } elseif ($action == 'closeNotification'){
                $audit_id = $request->auditId;
                $this->DBNotificationRepo->closeNotification($user_id, $audit_id);
            }
        }
    }
}
