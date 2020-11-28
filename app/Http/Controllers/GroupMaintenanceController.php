<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use App\Services\StringService;
use App\Repositories\DBGroupRepository;

class GroupMaintenanceController extends Controller
{

    protected $StringService;
    protected $DBGroupRepository;

    public function __construct(StringService $StringService, DBGroupRepository $DBGroupRepository)
    {
        $this->middleware(['auth','verified']);
        $this->DBGroupRepository = $DBGroupRepository;
        $this->StringService = $StringService;
    }

    public function index(Request $request){ 

        $user_id = Auth::User()->id;
        $groups = $this->DBGroupRepository->getGroupsByUser($user_id);

        return view('groupMaintenance', [
            'groups' => $groups,
            'user_id' => $user_id
        ]);
    }

    public function create(Request $request)
    {
        $group = new Group;

        $user_id = Auth::User()->id;
        $user = User::find($user_id);

        $name = trim($request->name);
        if ($name == "" || strlen($name) > 20){
            die();
        }

        $group->name = $name;
        $group->created_by_user_id = $user_id;
        $group->code = $this->StringService->generateRandomString();
        $group->save();

        header('Location: /privategroups');
        die();

        //header("Refresh:0");
    }
}
