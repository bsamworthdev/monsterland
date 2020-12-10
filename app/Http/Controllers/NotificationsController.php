<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\DBUserRepository;

class NotificationsController extends Controller
{
    protected $DBUserRepo;

    public function __construct(Request $request, 
    DBUserRepository $DBUserRepo)
    {
        $this->middleware(['auth','verified']);
        $this->DBUserRepo = $DBUserRepo;
    }

    public function update(Request $request){
        $action = $request->action;
        $user_id = Auth::User()->id;
        if ($action == 'updateLastViewed'){
            $this->DBUserRepo->updateNotificationsLastViewed($user_id);

        } 
    }
}
