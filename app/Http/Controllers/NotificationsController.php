<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\DBUserRepository;
use App\Repositories\DBNotificationRepository;
use App\Services\RedisService;

class NotificationsController extends Controller
{
    protected $DBUserRepo;
    protected $DBNotificationRepo;
    protected $RedisService;

    public function __construct(Request $request, 
        DBUserRepository $DBUserRepo,
        DBNotificationRepository $DBNotificationRepo,
        RedisService $RedisService)
    {
        $this->middleware(['auth','verified']);
        $this->DBUserRepo = $DBUserRepo;
        $this->DBNotificationRepo = $DBNotificationRepo;
        $this->RedisService = $RedisService;
    }

    public function update(Request $request){
        $action = $request->action;
        $user_id = Auth::User()->id;
        $session = $request->session();
        $session_id = $session->getId();

        if (Auth::check()){
            if ($action == 'updateLastViewed'){
                $this->DBUserRepo->updateNotificationsLastViewed($user_id);
            } elseif ($action == 'closeNotification'){
                $audit_id = $request->auditId;
                $this->DBNotificationRepo->closeNotification($user_id, $audit_id);

                $this->RedisService->delete($session_id.'_gallery_title');
                $this->RedisService->delete($session_id.'_gallery_monster_ids');
            }
        }
    }
}
